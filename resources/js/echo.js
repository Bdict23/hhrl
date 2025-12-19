import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// expose Pusher to window (laravel-echo needs this)
window.Pusher = Pusher;
// Pusher.logToConsole = true;  // <- enable logs

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
});

// console.log('Echo initialized, setting up listener...');

// Subscribe to channel
const channel = window.Echo.channel('units');
// console.log('Channel created:', channel);

// Wait for subscription to be ready, then bind
const bindListener = () => {
  if (channel.subscription && channel.subscription.bind) {
    // console.log('Binding to RemoteActionTriggered event...');
    
    channel.subscription.bind('RemoteActionTriggered', (data) => {
      // console.log('✅ Event received from Pusher:', data);
      
      // current user id (set from blade)
      const currentUserId = window.Laravel?.userId ?? null;
      
      // skip initiator if you want (optional)
      if (currentUserId && data.initiator_id && Number(currentUserId) === Number(data.initiator_id)) {
        // console.log('Skipping event - same user initiated');
        return;
      }

      // emit to Livewire listeners
      // console.log('Dispatching to Livewire:', data);
      
      // Use a slight delay to ensure Livewire is ready
      setTimeout(() => {
        if (typeof Livewire !== 'undefined') {
          // Use dispatch for Livewire v3
          if (typeof Livewire.dispatch === 'function') {
            Livewire.dispatch('RemoteActionTriggered', data);
          } else if (typeof Livewire.emit === 'function') {
            // Fallback for Livewire v2
            Livewire.emit('RemoteActionTriggered', data);
          } else {
            // console.error('Neither Livewire.dispatch nor Livewire.emit is available');
          }
        } else {
          // console.error('Livewire is not available yet');
        }
      }, 100);
    });
    
    // console.log('✅ Listener attached successfully');
  } else {
    // console.log('Subscription not ready, retrying in 100ms...');
    setTimeout(bindListener, 100);
  }
};

bindListener();
