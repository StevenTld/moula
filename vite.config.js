import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/layout.css',
                'resources/css/home.css',
                'resources/css/auth.css',
                'resources/css/wallets-index.css',
                'resources/css/wallets-create.css',
                'resources/css/wallets-import.css',
                'resources/css/wallets-show.css',
                'resources/css/wallets-edit.css',
                'resources/css/walletconnect.css',
                'resources/css/profile.css',
                'resources/css/contracts-index.css',
                'resources/css/contracts-create.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
