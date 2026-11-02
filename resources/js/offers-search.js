/**
 * Module de recherche dynamique pour les offres d'emploi.
 * Gère la recherche AJAX, la pagination infinie et les filtres temps réel.
 */

// État global
const state = {
    currentPage: 1,
    isLoading: false,
    hasMore: true,
    debounceTimer: null,
    lastParams: {},
};

/**
 * Construit les paramètres de recherche depuis le formulaire
 */
function getSearchParams() {
    const form = document.getElementById('search-form');
    if (!form) return {};

    const formData = new FormData(form);
    const params = {};

    for (const [key, value] of formData.entries()) {
        if (value !== '') {
            params[key] = value;
        }
    }

    return params;
}

/**
 * Met à jour l'URL sans recharger la page
 */
function updateUrl(params) {
    const url = new URL(window.location.href);
    url.search = new URLSearchParams(params).toString();
    window.history.replaceState({}, '', url);
}

/**
 * Affiche/masque l'état de chargement
 */
function setLoading(loading) {
    const container = document.getElementById('offers-container');
    const submitBtn = document.getElementById('search-submit');

    if (container) {
        container.classList.toggle('offers-loading', loading);
    }

    if (submitBtn) {
        submitBtn.disabled = loading;
        const spinner = submitBtn.querySelector('.spinner-border');
        if (loading) {
            if (!spinner) {
                const newSpinner = document.createElement('span');
                newSpinner.className = 'spinner-border spinner-border-sm me-2';
                newSpinner.setAttribute('role', 'status');
                newSpinner.innerHTML = '<span class="visually-hidden">Chargement...</span>';
                submitBtn.prepend(newSpinner);
            }
        } else if (spinner) {
            spinner.remove();
        }
    }
}

/**
 * Rend les cartes d'offres dans le DOM
 */
function renderOffers(offers, append = false) {
    const grid = document.getElementById('offers-grid');
    const emptyState = document.getElementById('empty-state');
    const paginationContainer = document.getElementById('pagination-container');
    const totalBadge = document.getElementById('total-offers-badge');

    if (!grid) return;

    if (!append) {
        grid.innerHTML = '';
    }

    if (offers.length === 0 && !append) {
        grid.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        if (paginationContainer) paginationContainer.innerHTML = '';
    } else {
        grid.style.display = 'flex';
        if (emptyState) emptyState.style.display = 'none';

        offers.forEach(offer => {
            const card = createOfferCard(offer);
            grid.appendChild(card);
        });
    }

    // Mettre à jour le badge du total
    if (totalBadge && window.lastPagination) {
        totalBadge.innerHTML = `<span class="dot"></span> ${window.lastPagination.total} offres disponibles`;
    }
}

/**
 * Crée un élément DOM pour une carte d'offre
 */
function createOfferCard(offer) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4';

    const skillsHtml = offer.offer_skills && offer.offer_skills.length > 0
        ? offer.offer_skills.slice(0, 3).map(os => `<span class="skill-badge">${os.skill?.name || ''}</span>`).join('') +
          (offer.offer_skills.length > 3 ? `<span class="skill-badge">+${offer.offer_skills.length - 3}</span>` : '')
        : '';

    col.innerHTML = `
        <div class="offer-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <img src="/images/placeholder-company.svg" alt="${escapeHtml(offer.company)}" class="offer-logo">
                <div>
                    <h3 class="h6 fw-semibold mb-0">${escapeHtml(offer.title)}</h3>
                    <p class="small text-secondary mb-0">${escapeHtml(offer.company)} — ${escapeHtml(offer.location)}</p>
                </div>
            </div>
            <div class="mb-2">
                <span class="offer-badge">${escapeHtml(offer.contract_type)}</span>
            </div>
            ${skillsHtml ? `<div class="d-flex flex-wrap gap-1 mb-2">${skillsHtml}</div>` : ''}
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">
                    <i class="bi bi-calendar"></i>
                    ${formatDate(offer.published_at)}
                </span>
                <a href="/offers/${offer.id}" class="offer-link">Voir l'offre</a>
            </div>
        </div>
    `;

    return col;
}

/**
 * Rend la pagination
 */
function renderPagination(pagination) {
    const container = document.getElementById('pagination-container');
    if (!container) return;

    if (!pagination.has_more_pages) {
        container.innerHTML = '';
        return;
    }

    container.innerHTML = `
        <nav aria-label="Pagination offres">
            <ul class="pagination">
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}" aria-label="Précédent">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item active"><span class="page-link">${pagination.current_page}</span></li>
                <li class="page-item ${!pagination.has_more_pages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}" aria-label="Suivant">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    `;

    // Ajouter les écouteurs sur les liens de pagination
    container.querySelectorAll('[data-page]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const page = parseInt(link.dataset.page);
            if (page > 0 && page !== state.currentPage) {
                loadPage(page);
            }
        });
    });
}

/**
 * Charge une page de résultats via AJAX
 */
async function loadPage(page = 1, append = false) {
    if (state.isLoading) return;

    const params = { ...getSearchParams(), page };
    state.isLoading = true;
    setLoading(true);

    try {
        const response = await fetch(`${window.OffersConfig.searchUrl}?${new URLSearchParams(params).toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        state.currentPage = data.pagination.current_page;
        state.hasMore = data.pagination.has_more_pages;
        window.lastPagination = data.pagination;

        renderOffers(data.offers, append);
        renderPagination(data.pagination);
        updateUrl(params);

    } catch (error) {
        console.error('Erreur chargement offres:', error);
        // Fallback : recharger la page complète
        window.location.href = `${window.OffersConfig.indexUrl}?${new URLSearchParams(params).toString()}`;
    } finally {
        state.isLoading = false;
        setLoading(false);
    }
}

/**
 * Déclenche une recherche avec debounce
 */
function debouncedSearch() {
    clearTimeout(state.debounceTimer);
    state.debounceTimer = setTimeout(() => {
        const params = getSearchParams();

        // Ne recharger que si les paramètres ont changé
        if (JSON.stringify(params) !== JSON.stringify(state.lastParams)) {
            state.lastParams = params;
            loadPage(1, false);
        }
    }, 300);
}

/**
 * Réinitialise tous les filtres
 */
function resetFilters() {
    const form = document.getElementById('search-form');
    if (form) {
        form.reset();
        state.lastParams = {};
        loadPage(1, false);
    }
}

/**
 * Échappe les caractères HTML pour éviter XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

/**
 * Formate une date en "il y a X temps"
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'à l\'instant';
    if (diffMins < 60) return `il y a ${diffMins} min`;
    if (diffHours < 24) return `il y a ${diffHours} h`;
    if (diffDays < 30) return `il y a ${diffDays} j`;
    return date.toLocaleDateString('fr-FR');
}

/**
 * Initialise les écouteurs d'événements
 */
function initEventListeners() {
    const form = document.getElementById('search-form');
    const toggleBtn = document.getElementById('toggle-advanced-filters');
    const advancedFilters = document.getElementById('advanced-filters');
    const resetBtn = document.getElementById('reset-filters');

    // Soumission du formulaire (recherche classique)
    if (form) {
        form.addEventListener('submit', e => {
            // Laisser le formulaire se soumettre normalement pour la première recherche
            // Les recherches suivantes seront gérées par AJAX via les événements change/input
        });
    }

    // Recherche temps réel sur les inputs
    const searchInputs = ['search-input', 'location-input', 'contract-type-input', 'date-from-input', 'date-to-input', 'sort-input'];
    searchInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', debouncedSearch);
            el.addEventListener('change', debouncedSearch);
        }
    });

    // Toggle filtres avancés
    if (toggleBtn && advancedFilters) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = advancedFilters.style.display === 'none' || advancedFilters.style.display === '';
            advancedFilters.style.display = isHidden ? 'flex' : 'none';
            toggleBtn.innerHTML = isHidden
                ? '<i class="bi bi-sliders"></i><span>Masquer filtres</span>'
                : '<i class="bi bi-sliders"></i><span>Filtres avancés</span>';
        });
    }

    // Réinitialiser filtres
    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }

    // Gestion du scroll infini (optionnel)
    let scrollTimeout = null;
    window.addEventListener('scroll', () => {
        if (!state.hasMore || state.isLoading) return;

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const docHeight = document.documentElement.scrollHeight;

            // Charger la page suivante quand on arrive à 80% de la page
            if (scrollTop + windowHeight >= docHeight * 0.8) {
                loadPage(state.currentPage + 1, true);
            }
        }, 100);
    });
}

/**
 * Initialisation au chargement de la page
 */
document.addEventListener('DOMContentLoaded', () => {
    initEventListeners();

    // Initialiser lastParams avec les valeurs actuelles
    state.lastParams = getSearchParams();
    state.currentPage = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
});

// Exposer pour tests
window.OffersSearch = {
    loadPage,
    debouncedSearch,
    resetFilters,
    getSearchParams,
};