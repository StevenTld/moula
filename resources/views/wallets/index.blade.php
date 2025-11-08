@extends('layouts.app')

@section('title', 'Mes Wallets')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mes Wallets</h1>
                <p class="mt-1 text-sm text-gray-500">Gérez tous vos wallets Base en un seul endroit</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('wallets.import') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Importer un Wallet
                </a>
                <a href="{{ route('wallets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Créer un Wallet
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Wallets</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_wallets'] }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Wallets Actifs</dt>
                                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['active_wallets'] }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Balance Totale</dt>
                                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ number_format($stats['total_balance_eth'], 6) }} ETH</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">Valeur USD</dt>
                                <dd class="mt-1 text-3xl font-semibold text-purple-600">${{ number_format($stats['total_balance_usd'] ?? 0, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Wallets List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Liste des Wallets</h3>
                </div>
                
                @if($wallets->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun wallet</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par créer votre premier wallet.</p>
                        <div class="mt-6">
                            <a href="{{ route('wallets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                + Créer un Wallet
                            </a>
                        </div>
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($wallets as $wallet)
                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                                    {{ substr($wallet->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center">
                                                    <h3 class="text-lg font-medium text-gray-900">{{ $wallet->name }}</h3>
                                                    @if($wallet->is_active)
                                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Actif</span>
                                                    @else
                                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactif</span>
                                                    @endif
                                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ strtoupper($wallet->network) }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    <p class="text-sm text-gray-600 font-mono">{{ $wallet->address }}</p>
                                                </div>
                                                <div class="mt-2 flex items-center space-x-4">
                                                    <div>
                                                        <span class="text-sm text-gray-500">Balance:</span>
                                                        <span class="ml-1 text-sm font-medium text-gray-900">{{ number_format($wallet->balance, 6) }} ETH</span>
                                                    </div>
                                                    @if($wallet->balance_usd)
                                                        <div>
                                                            <span class="text-sm text-gray-500">≈</span>
                                                            <span class="ml-1 text-sm font-medium text-gray-900">${{ number_format($wallet->balance_usd, 2) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex space-x-2">
                                        <a href="{{ route('wallets.show', $wallet->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Voir Détails
                                        </a>
                                    </div>
                                </div>
                            </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
