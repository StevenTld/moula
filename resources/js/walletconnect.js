// WalletConnect Integration
import { createWeb3Modal, defaultWagmiConfig } from '@web3modal/wagmi'
import { base, baseSepolia } from 'viem/chains'
import { reconnect, watchAccount, getAccount, disconnect } from '@wagmi/core'

// Configuration WalletConnect Project ID
// Vous devez créer un projet sur https://cloud.walletconnect.com/
const projectId = import.meta.env.VITE_WALLETCONNECT_PROJECT_ID || 'YOUR_PROJECT_ID'

// Métadonnées de l'application
const metadata = {
    name: 'Base Wallet Manager',
    description: 'Gérez vos wallets Base en toute simplicité',
    url: window.location.origin,
    icons: [`${window.location.origin}/favicon.ico`]
}

// Chaînes supportées
const chains = [base, baseSepolia]

// Configuration Wagmi avec persistance activée
const wagmiConfig = defaultWagmiConfig({
    chains,
    projectId,
    metadata,
    enableCoinbase: true,
    enableInjected: true,
    enableWalletConnect: true,
})

// Créer le modal Web3Modal
const modal = createWeb3Modal({
    wagmiConfig,
    projectId,
    chains,
    themeMode: 'light',
    themeVariables: {
        '--w3m-accent': '#3b82f6',
        '--w3m-border-radius-master': '8px'
    },
    enableAnalytics: false
})

// État de la connexion
let connectedAddress = null
let connectedChainId = null
let isInitialized = false

// Fonction pour initialiser et reconnecter
async function initializeWallet() {
    if (isInitialized) return
    
    try {
        // Reconnecter automatiquement si une session existe
        await reconnect(wagmiConfig)
        
        // Vérifier l'état initial
        const account = getAccount(wagmiConfig)
        if (account.isConnected) {
            connectedAddress = account.address
            connectedChainId = account.chainId
            updateWalletUI(connectedAddress, connectedChainId)
            console.log('✅ Wallet reconnecté:', connectedAddress)
        }
        
        isInitialized = true
    } catch (error) {
        console.error('Erreur lors de l\'initialisation du wallet:', error)
        isInitialized = true
    }
}

// Fonction pour ouvrir le modal de connexion
export function connectWallet() {
    modal.open()
}

// Fonction pour se déconnecter
export async function disconnectWallet() {
    try {
        await disconnect(wagmiConfig)
        connectedAddress = null
        connectedChainId = null
        updateWalletUI(null, null)
        console.log('Wallet disconnected')
    } catch (error) {
        console.error('Error disconnecting wallet:', error)
    }
}

// Fonction pour obtenir l'adresse connectée
export function getConnectedAddress() {
    const account = getAccount(wagmiConfig)
    return account.address || null
}

// Fonction pour obtenir la chaîne connectée
export function getConnectedChain() {
    const account = getAccount(wagmiConfig)
    return account.chainId || null
}

// Watcher pour les changements de compte
watchAccount(wagmiConfig, {
    onChange(account) {
        connectedAddress = account.address || null
        connectedChainId = account.chainId || null
        
        console.log('Account changed:', {
            address: connectedAddress,
            chainId: connectedChainId,
            isConnected: account.isConnected
        })
        
        updateWalletUI(connectedAddress, connectedChainId)
        
        // Dispatcher un événement personnalisé
        window.dispatchEvent(new CustomEvent('walletconnect:accountChanged', {
            detail: {
                address: connectedAddress,
                chainId: connectedChainId,
                isConnected: account.isConnected
            }
        }))
    }
})

// Mettre à jour l'interface utilisateur
function updateWalletUI(address, chainId) {
    const connectButton = document.getElementById('wallet-connect-btn')
    const addressDisplay = document.getElementById('wallet-address-display')
    const disconnectButton = document.getElementById('wallet-disconnect-btn')
    
    if (address) {
        // Wallet connecté
        if (connectButton) {
            connectButton.classList.add('hidden')
        }
        
        if (addressDisplay) {
            addressDisplay.textContent = `${address.substring(0, 6)}...${address.substring(address.length - 4)}`
            addressDisplay.classList.remove('hidden')
        }
        
        if (disconnectButton) {
            disconnectButton.classList.remove('hidden')
            // Ajouter l'écouteur d'événement pour la déconnexion
            disconnectButton.onclick = disconnectWallet
        }
        
        // Afficher le nom du réseau
        const networkName = chainId === base.id ? 'Base' : chainId === baseSepolia.id ? 'Base Sepolia' : 'Unknown'
        const networkDisplay = document.getElementById('wallet-network-display')
        if (networkDisplay) {
            networkDisplay.textContent = networkName
            networkDisplay.classList.remove('hidden')
        }
    } else {
        // Wallet déconnecté
        if (connectButton) {
            connectButton.classList.remove('hidden')
        }
        
        if (addressDisplay) {
            addressDisplay.classList.add('hidden')
        }
        
        if (disconnectButton) {
            disconnectButton.classList.add('hidden')
            disconnectButton.onclick = null
        }
        
        const networkDisplay = document.getElementById('wallet-network-display')
        if (networkDisplay) {
            networkDisplay.classList.add('hidden')
        }
    }
}

// Initialiser l'UI au chargement
document.addEventListener('DOMContentLoaded', async () => {
    console.log('WalletConnect: DOM loaded')
    
    // Initialiser le wallet
    await initializeWallet()
    
    // Mettre à jour l'UI
    const account = getAccount(wagmiConfig)
    if (account.isConnected) {
        connectedAddress = account.address
        connectedChainId = account.chainId
        updateWalletUI(connectedAddress, connectedChainId)
    }
    
    // Ajouter l'écouteur d'événement pour le bouton de connexion
    const connectButton = document.getElementById('wallet-connect-btn')
    console.log('WalletConnect: Connect button found:', !!connectButton)
    if (connectButton) {
        connectButton.addEventListener('click', () => {
            console.log('WalletConnect: Button clicked')
            connectWallet()
        })
        console.log('WalletConnect: Event listener added')
    }
})

// Vérifier la connexion lors du changement de page (navigation SPA ou reload)
window.addEventListener('load', async () => {
    await initializeWallet()
})

// Reconnecter automatiquement quand la page devient visible
document.addEventListener('visibilitychange', async () => {
    if (!document.hidden) {
        const account = getAccount(wagmiConfig)
        if (account.isConnected) {
            updateWalletUI(account.address, account.chainId)
        }
    }
})

// Exposer les fonctions globalement pour les utiliser dans les vues Blade
window.WalletConnect = {
    connect: connectWallet,
    disconnect: disconnectWallet,
    getAddress: getConnectedAddress,
    getChain: getConnectedChain,
    modal,
    wagmiConfig  // Exposer la configuration pour le service de contrat
}

// Initialiser automatiquement au chargement du module
;(async () => {
    console.log('WalletConnect: Module loaded')
    
    // Attendre que le DOM soit prêt
    if (document.readyState === 'loading') {
        await new Promise(resolve => document.addEventListener('DOMContentLoaded', resolve))
    }
    
    console.log('WalletConnect: Initializing...')
    await initializeWallet()
    
    // Mettre à jour l'UI si déjà connecté
    const account = getAccount(wagmiConfig)
    if (account.isConnected) {
        connectedAddress = account.address
        connectedChainId = account.chainId
        updateWalletUI(connectedAddress, connectedChainId)
    }
    
    // Ajouter l'écouteur d'événement pour le bouton de connexion
    setTimeout(() => {
        const connectButton = document.getElementById('wallet-connect-btn')
        console.log('WalletConnect: Connect button found:', !!connectButton)
        if (connectButton) {
            connectButton.addEventListener('click', () => {
                console.log('WalletConnect: Button clicked!')
                connectWallet()
            })
            console.log('WalletConnect: Event listener added to connect button')
        } else {
            console.error('WalletConnect: Connect button not found!')
        }
    }, 100)
})()
