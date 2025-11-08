<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletRepository
{
    /**
     * Create a new wallet.
     * 
     * @param array $data
     * @return Wallet
     */
    public function create(array $data): Wallet
    {
        return Wallet::create($data);
    }

    /**
     * Find a wallet by ID.
     * 
     * @param int $id
     * @return Wallet|null
     */
    public function find(int $id): ?Wallet
    {
        return Wallet::find($id);
    }

    /**
     * Find a wallet by address.
     * 
     * @param string $address
     * @param int|null $userId
     * @return Wallet|null
     */
    public function findByAddress(string $address, ?int $userId = null): ?Wallet
    {
        $query = Wallet::where('address', $address);
        
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }
        
        return $query->first();
    }

    /**
     * Get all wallets for a user.
     * 
     * @param int $userId
     * @param bool $activeOnly
     * @return Collection
     */
    public function getUserWallets(int $userId, bool $activeOnly = true): Collection
    {
        $query = Wallet::forUser($userId);

        if ($activeOnly) {
            $query->active();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get paginated wallets for a user.
     * 
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserWalletsPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Wallet::forUser($userId)
            ->with(['transactions' => function ($query) {
                $query->recent(5);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get wallets by network.
     * 
     * @param string $network
     * @return Collection
     */
    public function getByNetwork(string $network): Collection
    {
        return Wallet::network($network)->active()->get();
    }

    /**
     * Update a wallet.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $wallet = $this->find($id);
        
        if (!$wallet) {
            return false;
        }

        return $wallet->update($data);
    }

    /**
     * Update wallet balance and create history entry.
     * 
     * @param int $walletId
     * @param float $balance
     * @param float|null $balanceUsd
     * @param float|null $ethPriceUsd
     * @param string $snapshotType
     * @return bool
     */
    public function updateBalance(
        int $walletId,
        float $balance,
        ?float $balanceUsd = null,
        ?float $ethPriceUsd = null,
        string $snapshotType = 'manual'
    ): bool {
        $wallet = $this->find($walletId);
        
        if (!$wallet) {
            return false;
        }

        // Calculate changes
        $changeAmount = $balance - $wallet->balance;
        $changePercentage = $wallet->balance > 0 
            ? (($balance - $wallet->balance) / $wallet->balance) * 100 
            : 0;

        // Create history entry
        WalletBalanceHistory::create([
            'wallet_id' => $walletId,
            'balance' => $balance,
            'balance_usd' => $balanceUsd,
            'eth_price_usd' => $ethPriceUsd,
            'change_amount' => $changeAmount,
            'change_percentage' => $changePercentage,
            'snapshot_type' => $snapshotType,
            'snapshot_at' => now(),
        ]);

        // Update wallet
        return $wallet->updateBalance($balance, $balanceUsd);
    }

    /**
     * Delete a wallet (soft delete).
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $wallet = $this->find($id);
        
        if (!$wallet) {
            return false;
        }

        return $wallet->delete();
    }

    /**
     * Activate a wallet.
     * 
     * @param int $id
     * @return bool
     */
    public function activate(int $id): bool
    {
        return $this->update($id, ['is_active' => true]);
    }

    /**
     * Deactivate a wallet.
     * 
     * @param int $id
     * @return bool
     */
    public function deactivate(int $id): bool
    {
        return $this->update($id, ['is_active' => false]);
    }

    /**
     * Get wallet statistics for a user.
     * 
     * @param int $userId
     * @return array
     */
    public function getUserStats(int $userId): array
    {
        $wallets = $this->getUserWallets($userId);

        return [
            'total_wallets' => $wallets->count(),
            'active_wallets' => $wallets->where('is_active', true)->count(),
            'total_balance_eth' => $wallets->sum('balance'),
            'total_balance_usd' => $wallets->sum('balance_usd'),
            'networks' => $wallets->pluck('network')->unique()->values(),
        ];
    }

    /**
     * Get wallet with recent transactions.
     * 
     * @param int $id
     * @param int $transactionLimit
     * @return Wallet|null
     */
    public function getWithRecentTransactions(int $id, int $transactionLimit = 10): ?Wallet
    {
        return Wallet::with(['transactions' => function ($query) use ($transactionLimit) {
            $query->recent($transactionLimit);
        }])->find($id);
    }

    /**
     * Get wallet with balance history.
     * 
     * @param int $id
     * @param int $historyLimit
     * @return Wallet|null
     */
    public function getWithBalanceHistory(int $id, int $historyLimit = 30): ?Wallet
    {
        return Wallet::with(['balanceHistory' => function ($query) use ($historyLimit) {
            $query->recent($historyLimit);
        }])->find($id);
    }

    /**
     * Search wallets by name or address.
     * 
     * @param int $userId
     * @param string $search
     * @return Collection
     */
    public function search(int $userId, string $search): Collection
    {
        return Wallet::forUser($userId)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
