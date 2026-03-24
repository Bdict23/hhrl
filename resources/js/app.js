import './bootstrap';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';

// Make Swal globally available
window.Swal = Swal;

if (!window.Alpine) {
	window.Alpine = Alpine;
	Alpine.start();
}
