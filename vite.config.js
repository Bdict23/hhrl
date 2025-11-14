import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',  
                'resources/js/components/restaurant/restaurant.js'
            ],
            refresh: true,
        }),
        vue(), // added
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
            // host: '192.168.4.176', // Replace with your local IP
             host: '127.0.0.1', // Replace with your local IP

        },
    },


});
