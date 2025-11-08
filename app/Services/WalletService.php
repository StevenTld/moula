<?php

namespace App\Services;

use Exception;
use kornrunner\Keccak;
use Elliptic\EC;

class WalletService
{
    /**
     * Generate a new Ethereum wallet (compatible with Base).
     * 
     * @return array
     */
    public function generateWallet(): array
    {
        try {
            // Générer une clé privée aléatoire (32 bytes = 64 hex chars)
            $privateKey = $this->generatePrivateKey();
            
            // Générer l'adresse publique depuis la clé privée
            $address = $this->privateKeyToAddress($privateKey);
            
            return [
                'success' => true,
                'address' => $address,
                'private_key' => $privateKey,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a random private key.
     * 
     * @return string
     */
    private function generatePrivateKey(): string
    {
        // Générer 32 bytes aléatoires cryptographiquement sûrs
        $privateKey = bin2hex(random_bytes(32));
        
        return '0x' . $privateKey;
    }

    /**
     * Convert private key to Ethereum address.
     * 
     * @param string $privateKey
     * @return string
     */
    private function privateKeyToAddress(string $privateKey): string
    {
        // Supprimer le préfixe 0x si présent
        $privateKey = str_replace('0x', '', $privateKey);
        
        // Obtenir la clé publique depuis la clé privée
        $publicKey = $this->privateKeyToPublicKey($privateKey);
        
        // Hash Keccak-256 de la clé publique
        $hash = Keccak::hash(hex2bin($publicKey), 256);
        
        // Prendre les 20 derniers bytes (40 caractères hex)
        $address = '0x' . substr($hash, -40);
        
        // Appliquer le checksum EIP-55
        return $this->toChecksumAddress($address);
    }

    /**
     * Get public key from private key using secp256k1.
     * 
     * @param string $privateKey
     * @return string
     */
    private function privateKeyToPublicKey(string $privateKey): string
    {
        // Utiliser la librairie Elliptic pour générer la clé publique
        $ec = new EC('secp256k1');
        
        // Créer une paire de clés depuis la clé privée
        $key = $ec->keyFromPrivate($privateKey, 'hex');
        
        // Obtenir la clé publique au format non compressé
        $publicKey = $key->getPublic(false, 'hex');
        
        // Retirer le premier byte (04) qui indique le format non compressé
        return substr($publicKey, 2);
    }

    /**
     * Apply EIP-55 checksum to address.
     * 
     * @param string $address
     * @return string
     */
    private function toChecksumAddress(string $address): string
    {
        $address = strtolower(str_replace('0x', '', $address));
        $hash = Keccak::hash($address, 256);
        $checksum = '0x';

        for ($i = 0; $i < strlen($address); $i++) {
            if (intval($hash[$i], 16) >= 8) {
                $checksum .= strtoupper($address[$i]);
            } else {
                $checksum .= $address[$i];
            }
        }

        return $checksum;
    }

    /**
     * Import wallet from private key.
     * 
     * @param string $privateKey
     * @return array
     */
    public function importWallet(string $privateKey): array
    {
        try {
            // Normaliser la clé privée
            $privateKey = trim($privateKey);
            
            // Ajouter 0x si absent
            if (!str_starts_with($privateKey, '0x')) {
                $privateKey = '0x' . $privateKey;
            }
            
            // Valider le format de la clé privée
            if (!$this->isValidPrivateKey($privateKey)) {
                return [
                    'success' => false,
                    'error' => 'Clé privée invalide. Elle doit contenir 64 caractères hexadécimaux.',
                ];
            }
            
            // Générer l'adresse depuis la clé privée
            $address = $this->privateKeyToAddress($privateKey);
            
            return [
                'success' => true,
                'address' => $address,
                'private_key' => $privateKey,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate if a private key is valid.
     * 
     * @param string $privateKey
     * @return bool
     */
    public function isValidPrivateKey(string $privateKey): bool
    {
        // Supprimer le préfixe 0x si présent
        $key = str_replace('0x', '', $privateKey);
        
        // Vérifier que c'est exactement 64 caractères hexadécimaux
        if (!preg_match('/^[a-fA-F0-9]{64}$/', $key)) {
            return false;
        }
        
        // Vérifier que ce n'est pas une clé nulle
        if ($key === str_repeat('0', 64)) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate if an address is a valid Ethereum address.
     * 
     * @param string $address
     * @return bool
     */
    public function isValidAddress(string $address): bool
    {
        // Vérifier le format de base
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
            return false;
        }

        return true;
    }

    /**
     * Get balance for an address using Base RPC.
     * 
     * @param string $address
     * @param string $network
     * @return array
     */
    public function getBalance(string $address, string $network = 'base'): array
    {
        try {
            $rpcUrl = $this->getRpcUrl($network);
            
            $response = $this->callRpc($rpcUrl, 'eth_getBalance', [$address, 'latest']);
            
            if (isset($response['result'])) {
                // Convertir de Wei (hex) vers ETH
                $balanceWei = hexdec($response['result']);
                $balanceEth = $balanceWei / 1e18;
                
                return [
                    'success' => true,
                    'balance' => $balanceEth,
                    'balance_wei' => $balanceWei,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to get balance',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get RPC URL for network.
     * 
     * @param string $network
     * @return string
     */
    private function getRpcUrl(string $network): string
    {
        $urls = [
            'base' => env('BASE_RPC_URL', 'https://mainnet.base.org'),
            'base-sepolia' => env('BASE_SEPOLIA_RPC_URL', 'https://sepolia.base.org'),
        ];

        return $urls[$network] ?? $urls['base'];
    }

    /**
     * Make RPC call to blockchain.
     * 
     * @param string $url
     * @param string $method
     * @param array $params
     * @return array
     */
    private function callRpc(string $url, string $method, array $params = []): array
    {
        $data = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => 1,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("RPC call failed with code: {$httpCode}");
        }

        return json_decode($response, true);
    }

    /**
     * Get transaction count (nonce) for an address.
     * 
     * @param string $address
     * @param string $network
     * @return array
     */
    public function getTransactionCount(string $address, string $network = 'base'): array
    {
        try {
            $rpcUrl = $this->getRpcUrl($network);
            $response = $this->callRpc($rpcUrl, 'eth_getTransactionCount', [$address, 'latest']);
            
            if (isset($response['result'])) {
                return [
                    'success' => true,
                    'count' => hexdec($response['result']),
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to get transaction count',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
