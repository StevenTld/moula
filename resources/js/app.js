import './bootstrap';
import './walletconnect';
import { initContractService } from './contractService';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Initialiser le service de contrat quand le module walletconnect est prêt
if (window.WalletConnect?.wagmiConfig) {
    initContractService(window.WalletConnect.wagmiConfig);
}
