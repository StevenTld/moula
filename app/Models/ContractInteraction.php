<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'smart_contract_id',
        'wallet_id',
        'function_name',
        'type',
        'parameters',
        'result',
        'transaction_hash',
        'status',
        'error_message',
        'gas_used',
        'gas_price',
        'value',
    ];

    protected $casts = [
        'parameters' => 'array',
        'value' => 'decimal:18',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le contrat intelligent
     */
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class);
    }

    /**
     * Relation avec le wallet
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Générer l'URL de la transaction sur l'explorateur
     */
    public function getTransactionUrlAttribute(): ?string
    {
        if (!$this->transaction_hash) {
            return null;
        }

        $explorers = [
            'base' => 'https://basescan.org/tx/',
            'baseSepolia' => 'https://sepolia.basescan.org/tx/',
            'ethereum' => 'https://etherscan.io/tx/',
            'sepolia' => 'https://sepolia.etherscan.io/tx/',
        ];

        $chain = $this->smartContract->chain ?? 'base';
        
        return isset($explorers[$chain]) 
            ? $explorers[$chain] . $this->transaction_hash 
            : null;
    }

    /**
     * Scope pour filtrer les interactions réussies
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope pour filtrer les interactions échouées
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pour filtrer les interactions en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour filtrer par type (read/write)
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
