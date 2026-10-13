import * as bootstrap from 'bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Exponer Bootstrap globalmente para que los scripts inline lo usen
window.bootstrap = bootstrap;

// Tu JavaScript personalizado
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bootstrap cargado');
    
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});