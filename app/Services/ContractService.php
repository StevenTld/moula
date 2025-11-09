<?php

namespace App\Services;

use App\Models\SmartContract;
use App\Models\ContractInteraction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContractService
{
    /**
     * Enregistrer une interaction read avec un contrat
     */
    public function recordReadInteraction(
        SmartContract $contract,
        string $functionName,
        ?array $parameters = null,
        ?string $result = null,
        ?int $walletId = null
    ): ContractInteraction {
        return ContractInteraction::create([
            'user_id' => $contract->user_id,
            'smart_contract_id' => $contract->id,
            'wallet_id' => $walletId,
            'function_name' => $functionName,
            'type' => 'read',
            'parameters' => $parameters,
            'result' => $result,
            'status' => 'success',
        ]);
    }

    /**
     * Enregistrer une interaction write avec un contrat
     */
    public function recordWriteInteraction(
        SmartContract $contract,
        string $functionName,
        array $parameters = null,
        ?string $transactionHash = null,
        int $walletId = null,
        string $status = 'pending',
        ?string $value = null
    ): ContractInteraction {
        return ContractInteraction::create([
            'user_id' => $contract->user_id,
            'smart_contract_id' => $contract->id,
            'wallet_id' => $walletId,
            'function_name' => $functionName,
            'type' => 'write',
            'parameters' => $parameters,
            'transaction_hash' => $transactionHash,
            'status' => $status,
            'value' => $value ?? '0',
        ]);
    }

    /**
     * Mettre à jour le statut d'une interaction
     */
    public function updateInteractionStatus(
        ContractInteraction $interaction,
        string $status,
        ?string $errorMessage = null,
        ?string $gasUsed = null,
        ?string $gasPrice = null
    ): bool {
        return $interaction->update([
            'status' => $status,
            'error_message' => $errorMessage,
            'gas_used' => $gasUsed,
            'gas_price' => $gasPrice,
        ]);
    }

    /**
     * Récupérer l'ABI d'un contrat depuis un explorateur de blockchain
     */
    public function fetchAbiFromExplorer(string $address, string $chain = 'base'): ?array
    {
        try {
            $apiUrls = [
                'base' => 'https://api.basescan.org/api',
                'baseSepolia' => 'https://api-sepolia.basescan.org/api',
                'ethereum' => 'https://api.etherscan.io/api',
                'sepolia' => 'https://api-sepolia.etherscan.io/api',
            ];

            if (!isset($apiUrls[$chain])) {
                return null;
            }

            $apiKey = config("services.{$chain}_scan_api_key", '');
            
            $response = Http::get($apiUrls[$chain], [
                'module' => 'contract',
                'action' => 'getabi',
                'address' => $address,
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === '1' && isset($data['result'])) {
                    return json_decode($data['result'], true);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'ABI', [
                'address' => $address,
                'chain' => $chain,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Vérifier si un contrat est vérifié sur l'explorateur
     */
    public function isContractVerified(string $address, string $chain = 'base'): bool
    {
        try {
            $apiUrls = [
                'base' => 'https://api.basescan.org/api',
                'baseSepolia' => 'https://api-sepolia.basescan.org/api',
                'ethereum' => 'https://api.etherscan.io/api',
                'sepolia' => 'https://api-sepolia.etherscan.io/api',
            ];

            if (!isset($apiUrls[$chain])) {
                return false;
            }

            $apiKey = config("services.{$chain}_scan_api_key", '');
            
            $response = Http::get($apiUrls[$chain], [
                'module' => 'contract',
                'action' => 'getabi',
                'address' => $address,
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['status'] === '1';
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du contrat', [
                'address' => $address,
                'chain' => $chain,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Obtenir les statistiques d'un contrat
     */
    public function getContractStats(SmartContract $contract): array
    {
        $totalInteractions = $contract->interactions()->count();
        $readInteractions = $contract->interactions()->ofType('read')->count();
        $writeInteractions = $contract->interactions()->ofType('write')->count();
        $successfulWrites = $contract->interactions()->ofType('write')->successful()->count();
        $failedWrites = $contract->interactions()->ofType('write')->failed()->count();
        $pendingWrites = $contract->interactions()->ofType('write')->pending()->count();

        // Calcul du total de gas utilisé
        $totalGasUsed = $contract->interactions()
            ->ofType('write')
            ->successful()
            ->whereNotNull('gas_used')
            ->sum('gas_used');

        return [
            'total_interactions' => $totalInteractions,
            'read_interactions' => $readInteractions,
            'write_interactions' => $writeInteractions,
            'successful_writes' => $successfulWrites,
            'failed_writes' => $failedWrites,
            'pending_writes' => $pendingWrites,
            'total_gas_used' => $totalGasUsed,
            'read_functions_count' => count($contract->getReadFunctions()),
            'write_functions_count' => count($contract->getWriteFunctions()),
            'events_count' => count($contract->getEvents()),
        ];
    }

    /**
     * Valider une adresse de contrat Ethereum
     */
    public function isValidAddress(string $address): bool
    {
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
    }

    /**
     * Formater une adresse pour l'affichage
     */
    public function formatAddress(string $address, int $length = 6): string
    {
        if (strlen($address) <= ($length * 2 + 2)) {
            return $address;
        }

        return substr($address, 0, $length + 2) . '...' . substr($address, -$length);
    }

    /**
     * ABIs standard pour les contrats courants
     */
    public function getStandardAbi(string $type): ?array
    {
        $standardAbis = [
            'erc20' => [
                ['type' => 'function', 'name' => 'name', 'stateMutability' => 'view', 'inputs' => [], 'outputs' => [['type' => 'string', 'name' => '']]],
                ['type' => 'function', 'name' => 'symbol', 'stateMutability' => 'view', 'inputs' => [], 'outputs' => [['type' => 'string', 'name' => '']]],
                ['type' => 'function', 'name' => 'decimals', 'stateMutability' => 'view', 'inputs' => [], 'outputs' => [['type' => 'uint8', 'name' => '']]],
                ['type' => 'function', 'name' => 'totalSupply', 'stateMutability' => 'view', 'inputs' => [], 'outputs' => [['type' => 'uint256', 'name' => '']]],
                ['type' => 'function', 'name' => 'balanceOf', 'stateMutability' => 'view', 'inputs' => [['type' => 'address', 'name' => 'account']], 'outputs' => [['type' => 'uint256', 'name' => '']]],
                ['type' => 'function', 'name' => 'transfer', 'stateMutability' => 'nonpayable', 'inputs' => [['type' => 'address', 'name' => 'to'], ['type' => 'uint256', 'name' => 'amount']], 'outputs' => [['type' => 'bool', 'name' => '']]],
                ['type' => 'function', 'name' => 'approve', 'stateMutability' => 'nonpayable', 'inputs' => [['type' => 'address', 'name' => 'spender'], ['type' => 'uint256', 'name' => 'amount']], 'outputs' => [['type' => 'bool', 'name' => '']]],
                ['type' => 'function', 'name' => 'allowance', 'stateMutability' => 'view', 'inputs' => [['type' => 'address', 'name' => 'owner'], ['type' => 'address', 'name' => 'spender']], 'outputs' => [['type' => 'uint256', 'name' => '']]],
                ['type' => 'function', 'name' => 'transferFrom', 'stateMutability' => 'nonpayable', 'inputs' => [['type' => 'address', 'name' => 'from'], ['type' => 'address', 'name' => 'to'], ['type' => 'uint256', 'name' => 'amount']], 'outputs' => [['type' => 'bool', 'name' => '']]],
            ],
            'erc721' => [
                ['type' => 'function', 'name' => 'name', 'stateMutability' => 'view', 'inputs' => [], 'outputs' => [['type' => 'string', 'name' => '']]],
                ['type' => 'function', 'name' => 'symbol', 'stateMutability' => 'view', 'inputs' => [], 'outputs' => [['type' => 'string', 'name' => '']]],
                ['type' => 'function', 'name' => 'balanceOf', 'stateMutability' => 'view', 'inputs' => [['type' => 'address', 'name' => 'owner']], 'outputs' => [['type' => 'uint256', 'name' => '']]],
                ['type' => 'function', 'name' => 'ownerOf', 'stateMutability' => 'view', 'inputs' => [['type' => 'uint256', 'name' => 'tokenId']], 'outputs' => [['type' => 'address', 'name' => '']]],
                ['type' => 'function', 'name' => 'tokenURI', 'stateMutability' => 'view', 'inputs' => [['type' => 'uint256', 'name' => 'tokenId']], 'outputs' => [['type' => 'string', 'name' => '']]],
                ['type' => 'function', 'name' => 'approve', 'stateMutability' => 'nonpayable', 'inputs' => [['type' => 'address', 'name' => 'to'], ['type' => 'uint256', 'name' => 'tokenId']], 'outputs' => []],
                ['type' => 'function', 'name' => 'transferFrom', 'stateMutability' => 'nonpayable', 'inputs' => [['type' => 'address', 'name' => 'from'], ['type' => 'address', 'name' => 'to'], ['type' => 'uint256', 'name' => 'tokenId']], 'outputs' => []],
            ],
        ];

        return $standardAbis[$type] ?? null;
    }
}
