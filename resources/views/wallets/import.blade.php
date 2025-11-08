@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('wallets.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Importer un Wallet Existant</h1>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <!-- Warning Card -->
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>⚠️ Attention - Sécurité:</strong>
                    </p>
                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                        <li>Ne partagez JAMAIS votre clé privée avec qui que ce soit</li>
                        <li>Assurez-vous d'être seul et dans un environnement sûr</li>
                        <li>Vérifiez que vous êtes bien sur le bon site</li>
                        <li>La clé privée sera chiffrée avant d'être stockée</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Comment importer un wallet:</strong> Entrez la clé privée de votre wallet existant (64 caractères hexadécimaux, avec ou sans le préfixe 0x). 
                        L'adresse sera automatiquement calculée à partir de la clé privée.
                    </p>
                </div>
            </div>
        </div>

        <!-- Import Wallet Form -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations du Wallet</h3>
                <p class="mt-1 text-sm text-gray-500">Importez votre wallet existant en utilisant sa clé privée</p>
            </div>

            <form action="{{ route('wallets.store-import') }}" method="POST" class="px-4 py-5 sm:p-6">
                @csrf

                <!-- Wallet Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom du Wallet *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required
                        value="{{ old('name') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Mon Wallet MetaMask">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Private Key -->
                <div class="mb-6">
                    <label for="private_key" class="block text-sm font-medium text-gray-700">Clé Privée *</label>
                    <div class="mt-1 relative">
                        <input 
                            type="password" 
                            name="private_key" 
                            id="private_key" 
                            required
                            value="{{ old('private_key') }}"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 pr-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                            placeholder="0x1234567890abcdef...">
                        <button 
                            type="button" 
                            onclick="togglePrivateKeyVisibility()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Entrez votre clé privée (64 caractères hexadécimaux)</p>
                    @error('private_key')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Network Selection -->
                <div class="mb-6">
                    <label for="network" class="block text-sm font-medium text-gray-700">Réseau *</label>
                    <select 
                        name="network" 
                        id="network" 
                        required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="base" {{ old('network') == 'base' ? 'selected' : '' }}>Base Mainnet</option>
                        <option value="base-sepolia" {{ old('network') == 'base-sepolia' ? 'selected' : '' }}>Base Sepolia (Testnet)</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500">Sélectionnez le réseau où se trouve votre wallet</p>
                    @error('network')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (optionnel)</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Wallet importé depuis MetaMask...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms Checkbox -->
                <div class="mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input 
                                id="agree" 
                                name="agree" 
                                type="checkbox" 
                                required
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="agree" class="font-medium text-gray-700">Je comprends les risques *</label>
                            <p class="text-gray-500">Je confirme que je suis conscient des risques de sécurité liés au partage de ma clé privée et que je fais confiance à cette application.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-5 border-t border-gray-200">
                    <a href="{{ route('wallets.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Importer le Wallet
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">💡 Où trouver ma clé privée ?</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <div>
                        <strong>MetaMask:</strong>
                        <ol class="list-decimal list-inside ml-4 mt-1 space-y-1">
                            <li>Cliquez sur les 3 points → Détails du compte</li>
                            <li>Cliquez sur "Afficher la clé privée"</li>
                            <li>Entrez votre mot de passe MetaMask</li>
                            <li>Copiez la clé privée</li>
                        </ol>
                    </div>
                    <div>
                        <strong>Trust Wallet:</strong>
                        <ol class="list-decimal list-inside ml-4 mt-1 space-y-1">
                            <li>Paramètres → Wallets</li>
                            <li>Sélectionnez votre wallet → Info</li>
                            <li>Appuyez sur "Afficher la phrase de récupération"</li>
                        </ol>
                    </div>
                    <div class="pt-2 border-t border-gray-200">
                        <strong>Format de la clé privée:</strong>
                        <p class="mt-1">La clé doit être une chaîne hexadécimale de 64 caractères, avec ou sans le préfixe "0x".</p>
                        <p class="mt-1 font-mono text-xs bg-gray-100 p-2 rounded">Exemple: 0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePrivateKeyVisibility() {
    const input = document.getElementById('private_key');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
    }
}
</script>
@endsection
