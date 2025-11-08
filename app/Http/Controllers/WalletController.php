<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Repositories\WalletRepository;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected WalletRepository $walletRepository;
    protected WalletService $walletService;

    public function __construct(WalletRepository $walletRepository, WalletService $walletService)
    {
        $this->walletRepository = $walletRepository;
        $this->walletService = $walletService;
    }

    /**
     * Display a listing of the user's wallets.
     */
    public function index()
    {
        $wallets = $this->walletRepository->getUserWallets(Auth::id());
        $stats = $this->walletRepository->getUserStats(Auth::id());

        return view('wallets.index', compact('wallets', 'stats'));
    }

    /**
     * Show the form for creating a new wallet.
     */
    public function create()
    {
        return view('wallets.create');
    }

    /**
     * Show the form for importing an existing wallet.
     */
    public function import()
    {
        return view('wallets.import');
    }

    /**
     * Store a newly created wallet.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'network' => 'required|in:base,base-sepolia',
            'description' => 'nullable|string|max:1000',
        ]);

        // Générer un nouveau wallet
        $walletData = $this->walletService->generateWallet();

        if (!$walletData['success']) {
            return back()->with('error', 'Erreur lors de la génération du wallet: ' . $walletData['error']);
        }

        // Obtenir la balance initiale
        $balanceData = $this->walletService->getBalance($walletData['address'], $request->network);
        $balance = $balanceData['success'] ? $balanceData['balance'] : 0;

        // Créer le wallet en base de données
        $wallet = $this->walletRepository->create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'address' => $walletData['address'],
            'private_key' => $walletData['private_key'], // Sera chiffré automatiquement
            'network' => $request->network,
            'balance' => $balance,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('wallets.show', $wallet->id)
            ->with('success', 'Wallet créé avec succès!')
            ->with('new_wallet', true);
    }

    /**
     * Store an imported wallet from private key.
     */
    public function storeImport(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'network' => 'required|in:base,base-sepolia',
            'private_key' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        // Importer le wallet depuis la clé privée
        $walletData = $this->walletService->importWallet($request->private_key);

        if (!$walletData['success']) {
            return back()
                ->withInput()
                ->with('error', $walletData['error']);
        }

        // Vérifier si l'adresse existe déjà pour cet utilisateur
        $existingWallet = $this->walletRepository->findByAddress($walletData['address'], Auth::id());
        if ($existingWallet) {
            return back()
                ->withInput()
                ->with('error', 'Ce wallet existe déjà dans votre liste (Adresse: ' . $walletData['address'] . ')');
        }

        // Obtenir la balance initiale
        $balanceData = $this->walletService->getBalance($walletData['address'], $request->network);
        $balance = $balanceData['success'] ? $balanceData['balance'] : 0;

        // Créer le wallet en base de données
        $wallet = $this->walletRepository->create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'address' => $walletData['address'],
            'private_key' => $walletData['private_key'],
            'network' => $request->network,
            'balance' => $balance,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('wallets.show', $wallet->id)
            ->with('success', 'Wallet importé avec succès!')
            ->with('imported_wallet', true);
    }

    /**
     * Display the specified wallet.
     */
    public function show(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $wallet->load(['transactions' => function ($query) {
            $query->recent(10);
        }]);

        return view('wallets.show', compact('wallet'));
    }

    /**
     * Update wallet balance from blockchain.
     */
    public function refreshBalance(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $balanceData = $this->walletService->getBalance($wallet->address, $wallet->network);

        if ($balanceData['success']) {
            $this->walletRepository->updateBalance(
                $wallet->id,
                $balanceData['balance'],
                null, // balance_usd sera calculé plus tard avec un prix API
                null,
                'manual'
            );

            return back()->with('success', 'Balance mise à jour: ' . $balanceData['balance'] . ' ETH');
        }

        return back()->with('error', 'Erreur lors de la mise à jour de la balance: ' . $balanceData['error']);
    }

    /**
     * Show the form for editing the specified wallet.
     */
    public function edit(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('wallets.edit', compact('wallet'));
    }

    /**
     * Update the specified wallet.
     */
    public function update(Request $request, Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $this->walletRepository->update($wallet->id, [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('wallets.show', $wallet->id)
            ->with('success', 'Wallet mis à jour avec succès!');
    }

    /**
     * Remove the specified wallet.
     */
    public function destroy(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $this->walletRepository->delete($wallet->id);

        return redirect()->route('wallets.index')
            ->with('success', 'Wallet supprimé avec succès!');
    }

    /**
     * Export wallet private key.
     */
    public function exportPrivateKey(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        // Retourner la clé privée déchiffrée
        return response()->json([
            'private_key' => $wallet->private_key,
            'address' => $wallet->address,
        ]);
    }
}
