/**
 * Service pour interagir avec les smart contracts
 * Utilise viem pour les appels de contrats
 */

import { readContract, writeContract, waitForTransactionReceipt } from '@wagmi/core'
import { parseAbi } from 'viem'

export class ContractInteractionService {
    constructor(wagmiConfig) {
        this.wagmiConfig = wagmiConfig
    }

    /**
     * Lire une fonction d'un contrat (view/pure)
     */
    async readContractFunction(contractAddress, abi, functionName, args = []) {
        try {
            console.log('Reading contract function:', {
                address: contractAddress,
                functionName,
                args
            })

            const result = await readContract(this.wagmiConfig, {
                address: contractAddress,
                abi: abi,
                functionName: functionName,
                args: args,
            })

            console.log('Read result:', result)
            return {
                success: true,
                data: result,
                error: null
            }
        } catch (error) {
            console.error('Error reading contract:', error)
            return {
                success: false,
                data: null,
                error: error.message
            }
        }
    }

    /**
     * Écrire une fonction d'un contrat (transaction)
     */
    async writeContractFunction(contractAddress, abi, functionName, args = [], value = '0') {
        try {
            console.log('Writing contract function:', {
                address: contractAddress,
                functionName,
                args,
                value
            })

            // Envoyer la transaction
            const hash = await writeContract(this.wagmiConfig, {
                address: contractAddress,
                abi: abi,
                functionName: functionName,
                args: args,
                value: value !== '0' ? BigInt(value) : undefined,
            })

            console.log('Transaction hash:', hash)

            return {
                success: true,
                hash: hash,
                error: null
            }
        } catch (error) {
            console.error('Error writing contract:', error)
            return {
                success: false,
                hash: null,
                error: error.message
            }
        }
    }

    /**
     * Attendre la confirmation d'une transaction
     */
    async waitForTransaction(hash) {
        try {
            console.log('Waiting for transaction:', hash)

            const receipt = await waitForTransactionReceipt(this.wagmiConfig, {
                hash: hash,
            })

            console.log('Transaction receipt:', receipt)

            return {
                success: receipt.status === 'success',
                receipt: receipt,
                gasUsed: receipt.gasUsed.toString(),
                blockNumber: receipt.blockNumber.toString(),
                error: receipt.status === 'reverted' ? 'Transaction reverted' : null
            }
        } catch (error) {
            console.error('Error waiting for transaction:', error)
            return {
                success: false,
                receipt: null,
                gasUsed: null,
                blockNumber: null,
                error: error.message
            }
        }
    }

    /**
     * Appeler une fonction read et enregistrer l'interaction
     */
    async callReadFunction(contractId, contractAddress, abi, functionName, args = []) {
        const result = await this.readContractFunction(contractAddress, abi, functionName, args)
        
        // Enregistrer l'interaction dans la base de données
        if (result.success) {
            await this.recordInteraction({
                smart_contract_id: contractId,
                function_name: functionName,
                type: 'read',
                parameters: args,
                result: JSON.stringify(result.data),
                status: 'success'
            })
        }

        return result
    }

    /**
     * Appeler une fonction write et enregistrer l'interaction
     */
    async callWriteFunction(contractId, contractAddress, abi, functionName, args = [], value = '0') {
        // Envoyer la transaction
        const writeResult = await this.writeContractFunction(contractAddress, abi, functionName, args, value)
        
        if (!writeResult.success) {
            // Enregistrer l'échec
            await this.recordInteraction({
                smart_contract_id: contractId,
                function_name: functionName,
                type: 'write',
                parameters: args,
                status: 'failed',
                error_message: writeResult.error,
                value: value
            })
            return writeResult
        }

        // Enregistrer la transaction en attente
        const interactionId = await this.recordInteraction({
            smart_contract_id: contractId,
            function_name: functionName,
            type: 'write',
            parameters: args,
            transaction_hash: writeResult.hash,
            status: 'pending',
            value: value
        })

        // Attendre la confirmation en arrière-plan
        this.waitForTransaction(writeResult.hash).then(receipt => {
            this.updateInteraction(interactionId, {
                status: receipt.success ? 'success' : 'failed',
                gas_used: receipt.gasUsed,
                error_message: receipt.error
            })
        })

        return writeResult
    }

    /**
     * Enregistrer une interaction dans la base de données via API
     */
    async recordInteraction(data) {
        try {
            const response = await fetch('/api/contract-interactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(data)
            })

            if (response.ok) {
                const result = await response.json()
                return result.id
            }
        } catch (error) {
            console.error('Error recording interaction:', error)
        }
        return null
    }

    /**
     * Mettre à jour une interaction dans la base de données
     */
    async updateInteraction(interactionId, data) {
        try {
            await fetch(`/api/contract-interactions/${interactionId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(data)
            })
        } catch (error) {
            console.error('Error updating interaction:', error)
        }
    }

    /**
     * Obtenir les fonctions read d'un ABI
     */
    getReadFunctions(abi) {
        return abi.filter(item => 
            item.type === 'function' && 
            (!item.stateMutability || ['view', 'pure'].includes(item.stateMutability))
        )
    }

    /**
     * Obtenir les fonctions write d'un ABI
     */
    getWriteFunctions(abi) {
        return abi.filter(item => 
            item.type === 'function' && 
            item.stateMutability && 
            ['nonpayable', 'payable'].includes(item.stateMutability)
        )
    }

    /**
     * Formater les paramètres pour l'affichage
     */
    formatFunctionInputs(func) {
        if (!func.inputs || func.inputs.length === 0) {
            return 'Aucun paramètre'
        }

        return func.inputs.map(input => {
            const name = input.name || 'param'
            return `${name}: ${input.type}`
        }).join(', ')
    }

    /**
     * Valider les arguments d'une fonction
     */
    validateArguments(func, args) {
        if (!func.inputs) {
            return { valid: true, errors: [] }
        }

        const errors = []
        
        if (args.length !== func.inputs.length) {
            errors.push(`Attendu ${func.inputs.length} arguments, reçu ${args.length}`)
            return { valid: false, errors }
        }

        func.inputs.forEach((input, index) => {
            const arg = args[index]
            
            // Validation basique selon le type
            if (input.type.startsWith('uint') || input.type.startsWith('int')) {
                if (isNaN(arg)) {
                    errors.push(`${input.name || `Argument ${index + 1}`} doit être un nombre`)
                }
            } else if (input.type === 'address') {
                if (!/^0x[a-fA-F0-9]{40}$/.test(arg)) {
                    errors.push(`${input.name || `Argument ${index + 1}`} doit être une adresse Ethereum valide`)
                }
            } else if (input.type === 'bool') {
                if (typeof arg !== 'boolean' && arg !== 'true' && arg !== 'false') {
                    errors.push(`${input.name || `Argument ${index + 1}`} doit être un booléen`)
                }
            }
        })

        return { valid: errors.length === 0, errors }
    }
}

// Créer une instance globale
let contractServiceInstance = null

export function initContractService(wagmiConfig) {
    contractServiceInstance = new ContractInteractionService(wagmiConfig)
    window.ContractService = contractServiceInstance
    return contractServiceInstance
}

export function getContractService() {
    return contractServiceInstance
}
