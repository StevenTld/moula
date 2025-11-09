@extends('layouts.app')

@section('title', 'Ajouter un Contrat')

@section('styles')
    @vite(['resources/css/contracts-create.css'])
@endsection

@section('content')
<div class="create-contract-page">
    <div class="page-header">
        <a href="{{ route('contracts.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour
        </a>
        <h1 class="page-title">Ajouter un Smart Contract</h1>
        <p class="page-subtitle">Importez un contrat intelligent pour interagir avec la blockchain</p>
    </div>

    <div class="form-container">
        <form action="{{ route('contracts.store') }}" method="POST" class="contract-form">
            @csrf

            <!-- Informations de base -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Informations de base
                </h2>

                <div class="form-group">
                    <label for="name" class="form-label">Nom du contrat *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input @error('name') error @enderror" 
                        value="{{ old('name') }}"
                        placeholder="Ex: USDC Token, My NFT Collection"
                        required
                    >
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Adresse du contrat *</label>
                    <input 
                        type="text" 
                        id="address" 
                        name="address" 
                        class="form-input monospace @error('address') error @enderror" 
                        value="{{ old('address') }}"
                        placeholder="0x..."
                        required
                        pattern="^0x[a-fA-F0-9]{40}$"
                    >
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Adresse Ethereum valide (0x suivie de 40 caractères hexadécimaux)</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="chain" class="form-label">Blockchain *</label>
                        <select id="chain" name="chain" class="form-select @error('chain') error @enderror" required>
                            <option value="">Sélectionner une blockchain</option>
                            <option value="base" {{ old('chain') === 'base' ? 'selected' : '' }}>Base Mainnet</option>
                            <option value="baseSepolia" {{ old('chain') === 'baseSepolia' ? 'selected' : '' }}>Base Sepolia (Testnet)</option>
                            <option value="ethereum" {{ old('chain') === 'ethereum' ? 'selected' : '' }}>Ethereum Mainnet</option>
                            <option value="sepolia" {{ old('chain') === 'sepolia' ? 'selected' : '' }}>Sepolia (Testnet)</option>
                        </select>
                        @error('chain')
                            <span class="error-message">{{ $message }}</span>
                        @enderror>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">Type de contrat *</label>
                        <select id="type" name="type" class="form-select @error('type') error @enderror" required>
                            <option value="">Sélectionner un type</option>
                            <option value="token" {{ old('type') === 'token' ? 'selected' : '' }}>Token (ERC20/ERC721/ERC1155)</option>
                            <option value="nft" {{ old('type') === 'nft' ? 'selected' : '' }}>NFT Collection</option>
                            <option value="defi" {{ old('type') === 'defi' ? 'selected' : '' }}>DeFi Protocol</option>
                            <option value="custom" {{ old('type') === 'custom' ? 'selected' : '' }}>Personnalisé</option>
                        </select>
                        @error('type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description (optionnel)</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-textarea @error('description') error @enderror" 
                        rows="3"
                        placeholder="Décrivez brièvement ce contrat et son utilité..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- ABI (optionnel) -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"></polyline>
                        <polyline points="8 6 2 12 8 18"></polyline>
                    </svg>
                    ABI du contrat
                </h2>

                <div class="info-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div>
                        <strong>L'ABI est optionnel</strong>
                        <p>Si le contrat est vérifié sur l'explorateur blockchain, nous récupérerons automatiquement son ABI. Sinon, vous pouvez le fournir manuellement.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="abi" class="form-label">ABI (JSON)</label>
                    <textarea 
                        id="abi" 
                        name="abi" 
                        class="form-textarea monospace @error('abi') error @enderror" 
                        rows="8"
                        placeholder='[{"type":"function","name":"balanceOf",...}]'
                    >{{ old('abi') }}</textarea>
                    @error('abi')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">Format JSON valide requis si fourni</span>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="form-actions">
                <a href="{{ route('contracts.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Ajouter le contrat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
