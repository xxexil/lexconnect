import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

console.log('Bootstrap.js loaded - starting Echo initialization');
console.log('Host:', window.location.hostname);
console.log('VITE_REVERB_APP_KEY:', import.meta.env.VITE_REVERB_APP_KEY);
console.log('VITE_REVERB_PORT:', import.meta.env.VITE_REVERB_PORT);

try {
    const currentProtocol = window.location.protocol === 'https:' ? 'https' : 'http';
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? currentProtocol;
    const forceTLS = reverbScheme === 'https' || window.location.protocol === 'https:';
    const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
    const reverbPort = import.meta.env.VITE_REVERB_PORT ?? (forceTLS ? 443 : 8080);

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: forceTLS,
        enabledTransports: forceTLS ? ['wss', 'ws'] : ['ws', 'wss'],
        disableStats: true,
        authorizer: (channel, options) => {
            return {
                authorize: (socketId, callback) => {
                    console.log('Authorizing channel:', channel.name, 'Socket ID:', socketId);
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    })
                    .then(response => {
                        console.log('Channel authorization successful:', response.data);
                        callback(false, response.data);
                    })
                    .catch(error => {
                        console.error('Channel authorization failed:', error);
                        callback(true, error);
                    });
                }
            };
        },
    });

    console.log('Echo initialized successfully:', window.Echo);

    // Add connection event listeners for debugging
    window.Echo.connector.pusher.connection.bind('connected', function() {
        console.log('WebSocket connected successfully!');
    });

    window.Echo.connector.pusher.connection.bind('disconnected', function() {
        console.log('WebSocket disconnected');
    });

    window.Echo.connector.pusher.connection.bind('error', function(error) {
        console.error('WebSocket connection error:', error);
    });

} catch (error) {
    console.error('Error initializing Echo:', error);
    window.Echo = null;
}
