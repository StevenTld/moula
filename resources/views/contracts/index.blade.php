@extends('layouts.app')

@section('title', 'Mes Contrats')

@section('styles')
    @vite(['resources/css/contracts-index.css'])
@endsection

@section('content')
<div class="contracts-page">
    <!-- Hero Section -->
    <div class="contracts-hero">
        <div class="hero-content">
            <h1 class="hero-title">Mes Smart Contracts</h1>
            <p class="hero-subtitle">Gérez et interagissez avec vos contrats intelligents</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('contracts.create') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Ajouter un contrat
            </a>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Contrats Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon verified">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['verified'] }}</div>
                <div class="stat-label">Vérifiés</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon interactions">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['interactions'] }}</div>
                <div class="stat-label">Interactions</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon chains">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['chains'] }}</div>
                <div class="stat-label">Blockchains</div>
            </div>
        </div>
    </div>

    <!-- Contracts Grid -->
    @if($contracts->count() > 0)
        <div class="contracts-grid">
            @foreach($contracts as $contract)
                <div class="contract-card">
                    <div class="contract-header">
                        <div class="contract-type-badge {{ $contract->type }}">
                            {{ ucfirst($contract->type) }}
                        </div>
                        @if($contract->is_verified)
                            <div class="verified-badge" title="Contrat vérifié">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="contract-body">
                        <h3 class="contract-name">{{ $contract->name }}</h3>
                        
                        <div class="contract-address">
                            <span class="address-label">Adresse:</span>
                            <code class="address-code">{{ Str::limit($contract->address, 12, '...') }}{{ substr($contract->address, -6) }}</code>
                            <button class="btn-copy" onclick="copyToClipboard('{{ $contract->address }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </button>
                        </div>

                        @if($contract->description)
                            <p class="contract-description">{{ Str::limit($contract->description, 100) }}</p>
                        @endif

                        <div class="contract-meta">
                            <span class="meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                {{ ucfirst($contract->chain) }}
                            </span>
                            <span class="meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ $contract->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="contract-footer">
                        <a href="{{ route('contracts.show', $contract) }}" class="btn-view">
                            Voir les détails
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($contracts->hasPages())
            <div class="pagination-wrapper">
                {{ $contracts->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
            <h3 class="empty-title">Aucun contrat pour le moment</h3>
            <p class="empty-message">Commencez par ajouter votre premier smart contract pour interagir avec la blockchain.</p>
            <a href="{{ route('contracts.create') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Ajouter mon premier contrat
            </a>
        </div>
    @endif
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Adresse copiée !', 'success');
    }).catch(err => {
        console.error('Erreur lors de la copie:', err);
        showToast('Erreur lors de la copie', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endsection
