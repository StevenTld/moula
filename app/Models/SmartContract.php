<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmartContract extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'chain',
        'description',
        'abi',
        'type',
        'is_verified',
        'explorer_url',
        'metadata',
    ];

    protected $casts = [
        'abi' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les interactions
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(ContractInteraction::class);
    }

    /**
     * Obtenir les fonctions read du contrat
     */
    public function getReadFunctions(): array
    {
        $functions = [];
        foreach ($this->abi as $item) {
            if (isset($item['type']) && $item['type'] === 'function' && 
                (!isset($item['stateMutability']) || in_array($item['stateMutability'], ['view', 'pure']))) {
                $functions[] = $item;
            }
        }
        return $functions;
    }

    /**
     * Obtenir les fonctions write du contrat
     */
    public function getWriteFunctions(): array
    {
        $functions = [];
        foreach ($this->abi as $item) {
            if (isset($item['type']) && $item['type'] === 'function' && 
                isset($item['stateMutability']) && 
                in_array($item['stateMutability'], ['nonpayable', 'payable'])) {
                $functions[] = $item;
            }
        }
        return $functions;
    }

    /**
     * Obtenir les événements du contrat
     */
    public function getEvents(): array
    {
        $events = [];
        foreach ($this->abi as $item) {
            if (isset($item['type']) && $item['type'] === 'event') {
                $events[] = $item;
            }
        }
        return $events;
    }

    /**
     * Générer l'URL de l'explorateur pour ce contrat
     */
    public function getExplorerUrlAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        // Générer automatiquement selon la chaîne
        $explorers = [
            'base' => 'https://basescan.org/address/',
            'baseSepolia' => 'https://sepolia.basescan.org/address/',
            'ethereum' => 'https://etherscan.io/address/',
            'sepolia' => 'https://sepolia.etherscan.io/address/',
        ];

        return isset($explorers[$this->chain]) 
            ? $explorers[$this->chain] . $this->address 
            : null;
    }
}
