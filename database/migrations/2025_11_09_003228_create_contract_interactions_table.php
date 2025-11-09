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
        Schema::create('contract_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('smart_contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('set null');
            $table->string('function_name'); // Nom de la fonction appelée
            $table->enum('type', ['read', 'write']); // Type d'interaction
            $table->json('parameters')->nullable(); // Paramètres passés à la fonction
            $table->text('result')->nullable(); // Résultat de l'appel (pour les read)
            $table->string('transaction_hash')->nullable(); // Hash de la transaction (pour les write)
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable(); // Message d'erreur si échec
            $table->string('gas_used')->nullable(); // Gas utilisé
            $table->string('gas_price')->nullable(); // Prix du gas
            $table->decimal('value', 36, 18)->default(0); // Valeur ETH/token envoyée
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['user_id', 'smart_contract_id']);
            $table->index('transaction_hash');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_interactions');
    }
};
