// Imports
import { requestGET } from './ajax.js';
import { getToggleStatus } from './toggle.js';

// DOM Elements
const table = document.getElementById('table');
const tbody = table.getElementsByTagName('tbody')[0];
const toggle_boutique = document.getElementById('toggle_boutique');
const toggle_grades = document.getElementById('toggle_grades');
const toggle_events = document.getElementById('toggle_events');
const userSearch = document.getElementById('userSearch');

// Values
const default_data = await requestGET('/purchase.php');

default_data.forEach(item => {
    const nom = item.nom_utilisateur ?? 'N/A';
    const prenom = item.prenom_membre ?? 'N/A';
    item.user = `${nom.toUpperCase()} ${prenom}`;
});

// Load data
function loadData() {
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

    let data = default_data;

    if (!getToggleStatus(toggle_boutique)) {
        data = data.filter(item => item.type_transaction !== 'Commande');
    }

    if (!getToggleStatus(toggle_grades)) {
        data = data.filter(item => item.type_transaction !== 'Adhesion');
    }

    if (!getToggleStatus(toggle_events)) {
        data = data.filter(item => item.type_transaction !== 'Inscription');
    }

    if (userSearch.value !== '') {
        data = data.filter(item =>
            (item.user ?? '').toUpperCase().includes(userSearch.value.toUpperCase())
        );
    }

    data.forEach(item => {
        const row = document.createElement('tr');

        const typeCell = document.createElement('td');
        typeCell.textContent = item.type_transaction ?? '';
        row.appendChild(typeCell);

        const elementCell = document.createElement('td');
        elementCell.textContent = item.element ?? '';
        row.appendChild(elementCell);

        const utilisateurCell = document.createElement('td');
        utilisateurCell.textContent = item.user ?? '';
        row.appendChild(utilisateurCell);

        const dateCell = document.createElement('td');
        dateCell.textContent = (item.date_transaction ?? '').split(' ')[0];
        row.appendChild(dateCell);

        const quantiteCell = document.createElement('td');
        quantiteCell.textContent = item.quantite ?? '';
        row.appendChild(quantiteCell);

        const prixCell = document.createElement('td');
        prixCell.textContent = `${parseFloat(item.montant ?? 0).toFixed(2)} €`;
        row.appendChild(prixCell);

        const paiementCell = document.createElement('td');
        paiementCell.textContent = item.mode_paiement ?? '';
        row.appendChild(paiementCell);

        tbody.appendChild(row);
    });
}

// Initial load
loadData();

// Events
toggle_boutique.addEventListener('click', loadData);
toggle_grades.addEventListener('click', loadData);
toggle_events.addEventListener('click', loadData);
userSearch.addEventListener('input', loadData);