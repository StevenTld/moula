<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('smart_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nom du contrat (ex: "USDC Token", "My NFT Collection")
            $table->string('address'); // Adresse du contrat intelligent
            $table->string('chain')->default('base'); // Chaîne (base, baseSepolia, ethereum, etc.)
            $table->text('description')->nullable(); // Description du contrat
            $table->json('abi'); // ABI du contrat (au format JSON)
            $table->string('type')->default('custom'); // Type: token, nft, defi, custom
            $table->boolean('is_verified')->default(false); // Contrat vérifié ou non
            $table->string('explorer_url')->nullable(); // URL de l'explorateur (Etherscan, Basescan, etc.)
            $table->json('metadata')->nullable(); // Métadonnées supplémentaires (icône, tags, etc.)
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['user_id', 'chain']);
            $table->index('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_contracts');
    }
};
