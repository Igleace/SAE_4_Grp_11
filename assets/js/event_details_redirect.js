const eventDivs = document.querySelectorAll('.event');

// Ajouter un gestionnaire de clic à chaque div
eventDivs.forEach(div => {
    div.addEventListener('click', (event) => {
        const eventId = div.getAttribute('event-id'); // Récupère l'ID de l'événement
        if (eventId) {
            const url = `/?page=event_details&id=${eventId}`;

            // Détecte le clic molette ou Ctrl/Cmd + clic
            if (event.ctrlKey || event.metaKey || event.button === 1) {
                window.open(url, '_blank'); // Ouvre dans un nouvel onglet
            } else {
                // Sinon, redirige normalement
                window.location.href = url;
            }
        }
    });

    // Empêche la sélection de texte sur le clic molette
    div.addEventListener('mousedown', (event) => {
        if (event.button === 1) {
            event.preventDefault(); // Empêche le comportement par défaut
        }
    });
});
