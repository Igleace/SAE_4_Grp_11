document.querySelectorAll('.my-medias img, .general-medias img').forEach(image => {
    image.addEventListener('click', (event) => {
        // Vérifier si l'image appartient à un parent autre que #add-media
        if (!image.closest('form')) {
            // Ouvrir l'image dans un nouvel onglet
            window.open(image.src, '_blank');
        }
    });
});

