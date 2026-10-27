import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './offers-search.js';

// Fermeture automatique des messages de confirmation (pas des erreurs).
document.querySelectorAll('.alert[data-auto-dismiss]').forEach((alert) => {
    window.setTimeout(() => {
        window.bootstrap?.Alert.getOrCreateInstance(alert)?.close();
    }, 6000);
});