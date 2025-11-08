@extends('layouts.app')

@section('title', 'Accueil - Base Manager')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Bienvenue sur Base Manager</h1>
            <p class="mt-2 text-lg text-gray-600">Gérez vos wallets Base en toute simplicité et sécurité</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Wallets -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Wallets</dt>
                                <dd class="text-3xl font-bold text-gray-900">{{ $stats['total_wallets'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 px-6 py-3">
                    <div class="text-sm">
                        <a href="{{ route('wallets.index') }}" class="font-medium text-blue-700 hover:text-blue-900">
                            Voir tous les wallets →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Wallets -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wallets Actifs</dt>
                                <dd class="text-3xl font-bold text-green-600">{{ $stats['active_wallets'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 px-6 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-green-700">
                            {{ $stats['active_wallets'] > 0 ? 'Tous opérationnels' : 'Créez votre premier wallet' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Balance ETH -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Balance Totale</dt>
                                <dd class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_balance_eth'] ?? 0, 4) }} ETH</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-purple-50 px-6 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-purple-700">
                            Base Network
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total USD Value -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Valeur USD</dt>
                                <dd class="text-2xl font-bold text-yellow-600">${{ number_format($stats['total_balance_usd'] ?? 0, 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-50 px-6 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-yellow-700">
                            Estimation en temps réel
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Create Wallet Card -->
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg shadow-lg p-8 text-white">
                <div class="flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Créer un Nouveau Wallet</h3>
                        <p class="text-blue-100 mb-6">Générez une nouvelle adresse Base en quelques secondes</p>
                    </div>
                    <a href="{{ route('wallets.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 shadow-md">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Créer Maintenant
                    </a>
                </div>
            </div>

            <!-- Import Wallet Card -->
            <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-lg p-8 text-white">
                <div class="flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Importer un Wallet</h3>
                        <p class="text-green-100 mb-6">Ajoutez un wallet existant avec sa clé privée</p>
                    </div>
                    <a href="{{ route('wallets.import') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors duration-200 shadow-md">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Importer Maintenant
                    </a>
                </div>
            </div>

            <!-- View Wallets Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 border-2 border-gray-200">
                <div class="flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Gérer vos Wallets</h3>
                        <p class="text-gray-600 mb-6">Consultez, gérez et surveillez tous vos wallets</p>
                    </div>
                    <a href="{{ route('wallets.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors duration-200 shadow-md">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Voir les Wallets
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Fonctionnalités</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sécurité Maximale</h3>
                    <p class="text-gray-600 text-sm">Clés privées chiffrées avec les meilleurs standards de sécurité</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Rapide & Efficace</h3>
                    <p class="text-gray-600 text-sm">Création et gestion de wallets en quelques clics seulement</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Suivi en Temps Réel</h3>
                    <p class="text-gray-600 text-sm">Surveillez vos balances et transactions en direct</p>
                </div>
            </div>
        </div>

        <!-- Recent Wallets -->
        @if(isset($recentWallets) && $recentWallets->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Wallets Récents</h2>
                <a href="{{ route('wallets.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                    Voir tout →
                </a>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <ul class="divide-y divide-gray-200">
                    @foreach($recentWallets->take(5) as $wallet)
                    <li class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                        <a href="{{ route('wallets.show', $wallet->id) }}" class="flex items-center justify-between">
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        {{ substr($wallet->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $wallet->name }}</p>
                                    <p class="text-sm text-gray-500 font-mono truncate">{{ $wallet->address }}</p>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0 text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($wallet->balance, 4) }} ETH</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $wallet->is_active ? 'green' : 'gray' }}-100 text-{{ $wallet->is_active ? 'green' : 'gray' }}-800">
                                    {{ $wallet->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
