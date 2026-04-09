// Sélectionner tous les formulaires avec la classe 'delete-media'
const deleteForms = document.getElementsByClassName('delete-media');

// Parcourir chaque formulaire et ajouter un listener
Array.from(deleteForms).forEach((form) => {
    const label = form.querySelector('label'); // Sélectionner le label à l'intérieur de ce formulaire

    if (label) {
        label.addEventListener('click', () => {
            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cet élément ?");
            if (confirmation) {
                form.submit();
            } 
        });
    }
});
