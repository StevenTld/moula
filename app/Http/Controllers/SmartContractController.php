<?php

namespace App\Http\Controllers;

use App\Models\SmartContract;
use App\Models\ContractInteraction;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Afficher la liste des contrats
     */
    public function index()
    {
        $contracts = Auth::user()->smartContracts()
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => Auth::user()->smartContracts()->count(),
            'verified' => Auth::user()->smartContracts()->where('is_verified', true)->count(),
            'interactions' => Auth::user()->contractInteractions()->count(),
            'chains' => Auth::user()->smartContracts()->distinct('chain')->count('chain'),
        ];

        return view('contracts.index', compact('contracts', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('contracts.create');
    }

    /**
     * Enregistrer un nouveau contrat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'chain' => 'required|string|in:base,baseSepolia,ethereum,sepolia',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:token,nft,defi,custom',
            'abi' => 'nullable|json',
        ]);

        // Si l'ABI n'est pas fourni, essayer de le récupérer
        if (empty($validated['abi'])) {
            $abi = $this->contractService->fetchAbiFromExplorer(
                $validated['address'],
                $validated['chain']
            );

            if (!$abi) {
                return back()
                    ->withInput()
                    ->withErrors(['abi' => 'Impossible de récupérer l\'ABI automatiquement. Veuillez le fournir manuellement.']);
            }

            $validated['abi'] = $abi;
            $validated['is_verified'] = true;
        } else {
            $validated['abi'] = json_decode($validated['abi'], true);
            $validated['is_verified'] = $this->contractService->isContractVerified(
                $validated['address'],
                $validated['chain']
            );
        }

        $contract = Auth::user()->smartContracts()->create($validated);

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Contrat ajouté avec succès !');
    }

    /**
     * Afficher les détails d'un contrat
     */
    public function show(SmartContract $contract)
    {
        // Vérifier que le contrat appartient à l'utilisateur connecté
        if ($contract->user_id !== Auth::id()) {
            abort(403);
        }

        $stats = $this->contractService->getContractStats($contract);
        
        $recentInteractions = $contract->interactions()
            ->with('wallet')
            ->latest()
            ->limit(10)
            ->get();

        $readFunctions = $contract->getReadFunctions();
        $writeFunctions = $contract->getWriteFunctions();

        return view('contracts.show', compact(
            'contract',
            'stats',
            'recentInteractions',
            'readFunctions',
            'writeFunctions'
        ));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(SmartContract $contract)
    {
        if ($contract->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('contracts.edit', compact('contract'));
    }

    /**
     * Mettre à jour un contrat
     */
    public function update(Request $request, SmartContract $contract)
    {
        if ($contract->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:token,nft,defi,custom',
        ]);

        $contract->update($validated);

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Contrat mis à jour avec succès !');
    }

    /**
     * Supprimer un contrat
     */
    public function destroy(SmartContract $contract)
    {
        if ($contract->user_id !== Auth::id()) {
            abort(403);
        }
        
        $contract->delete();

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Contrat supprimé avec succès !');
    }
}
