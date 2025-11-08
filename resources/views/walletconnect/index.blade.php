@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">WalletConnect</h1>
            <p class="mt-2 text-gray-600">Connectez votre wallet externe pour interagir avec la blockchain Base</p>
        </div>

        <!-- Connection Status Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">État de la Connexion</h3>
            </div>
            <div class="px-6 py-6">
                <!-- Not Connected State -->
                <div id="not-connected-state" class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun Wallet Connecté</h3>
                    <p class="text-gray-500 mb-6">Connectez votre wallet pour voir vos informations blockchain</p>
                    <button 
                        onclick="window.WalletConnect.connect()"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        Connecter mon Wallet
                    </button>
                </div>

                <!-- Connected State -->
                <div id="connected-state" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Address Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-medium text-gray-700">Adresse du Wallet</h4>
                                <button 
                                    onclick="copyAddress()"
                                    class="text-blue-600 hover:text-blue-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p id="connected-address" class="text-lg font-mono font-semibold text-gray-900 break-all">0x0000...0000</p>
                        </div>

                        <!-- Network Card -->
                        <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-lg p-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Réseau</h4>
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-2"></span>
                                <p id="connected-network" class="text-lg font-semibold text-gray-900">Base Mainnet</p>
                            </div>
                        </div>

                        <!-- Balance Card -->
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Balance</h4>
                            <p id="connected-balance" class="text-2xl font-bold text-gray-900">0.0000 ETH</p>
                            <button 
                                onclick="refreshBalance()"
                                class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Actualiser
                            </button>
                        </div>

                        <!-- Chain ID Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Chain ID</h4>
                            <p id="connected-chain-id" class="text-2xl font-bold text-gray-900">8453</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button 
                            onclick="window.WalletConnect.disconnect()"
                            class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Déconnecter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Comment connecter votre wallet ?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Cliquez sur "Connecter mon Wallet"</li>
                            <li>Sélectionnez votre wallet préféré (MetaMask, Trust Wallet, etc.)</li>
                            <li>Approuvez la connexion dans votre wallet</li>
                            <li>Assurez-vous d'être sur le réseau Base</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supported Wallets -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Wallets Supportés</h3>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                        <div class="text-4xl mb-2">🦊</div>
                        <p class="text-sm font-medium text-gray-900">MetaMask</p>
                    </div>
                    <div class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                        <div class="text-4xl mb-2">💼</div>
                        <p class="text-sm font-medium text-gray-900">Trust Wallet</p>
                    </div>
                    <div class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                        <div class="text-4xl mb-2">🌈</div>
                        <p class="text-sm font-medium text-gray-900">Rainbow</p>
                    </div>
                    <div class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                        <div class="text-4xl mb-2">🔵</div>
                        <p class="text-sm font-medium text-gray-900">Coinbase</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-500 text-center">Et plus de 300 autres wallets compatibles WalletConnect...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Update UI on wallet connection
window.addEventListener('walletconnect:accountChanged', (event) => {
    const { address, chainId, isConnected } = event.detail
    
    const notConnectedState = document.getElementById('not-connected-state')
    const connectedState = document.getElementById('connected-state')
    const connectedAddress = document.getElementById('connected-address')
    const connectedNetwork = document.getElementById('connected-network')
    const connectedChainId = document.getElementById('connected-chain-id')
    
    if (isConnected && address) {
        notConnectedState.classList.add('hidden')
        connectedState.classList.remove('hidden')
        
        connectedAddress.textContent = address
        connectedChainId.textContent = chainId
        
        const network = chainId === 8453 ? 'Base Mainnet' : chainId === 84532 ? 'Base Sepolia' : 'Unknown Network'
        connectedNetwork.textContent = network
        
        // Fetch balance (you can implement this with Web3)
        fetchBalance(address, chainId)
    } else {
        notConnectedState.classList.remove('hidden')
        connectedState.classList.add('hidden')
    }
})

// Copy address to clipboard
function copyAddress() {
    const address = document.getElementById('connected-address').textContent
    navigator.clipboard.writeText(address).then(() => {
        alert('Adresse copiée dans le presse-papiers!')
    })
}

// Fetch balance (placeholder - implement with actual RPC call)
async function fetchBalance(address, chainId) {
    // You can implement this with viem or ethers.js
    const balanceElement = document.getElementById('connected-balance')
    balanceElement.textContent = 'Chargement...'
    
    // Placeholder
    setTimeout(() => {
        balanceElement.textContent = '0.0000 ETH'
    }, 1000)
}

// Refresh balance
function refreshBalance() {
    const address = window.WalletConnect.getAddress()
    const chainId = window.WalletConnect.getChain()
    
    if (address && chainId) {
        fetchBalance(address, chainId)
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Petit délai pour laisser WalletConnect s'initialiser
    setTimeout(() => {
        checkConnectionState()
    }, 500)
})

// Vérifier périodiquement l'état de connexion
setInterval(() => {
    checkConnectionState()
}, 2000)

// Fonction pour vérifier l'état de connexion
function checkConnectionState() {
    if (window.WalletConnect && typeof window.WalletConnect.getAddress === 'function') {
        const address = window.WalletConnect.getAddress()
        const chainId = window.WalletConnect.getChain()
        
        if (address && chainId) {
            const event = new CustomEvent('walletconnect:accountChanged', {
                detail: { address, chainId, isConnected: true }
            })
            window.dispatchEvent(event)
        }
    }
}
</script>
@endsection
