import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    // server: {
    //     host: '0.0.0.0', // Allow access from any IP
    //     port: 3000,      // Default Vite port; change if needed
    // },

    server: {
        host: '0.0.0.0', // Listen on all interfaces
        port: 5173,      // Default Vite port
        strictPort: true, // Ensures the port doesn't change
        hmr: {
            host: '192.168.1.159', // Replace with your local IP
        },
    },


});
