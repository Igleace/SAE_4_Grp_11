// Récupérer l'image et le formulaire
const imageLabel = document.querySelector('#open-gallery label');
const form2 = document.getElementById('open-gallery');

// Ajouter un événement de clic sur l'image
imageLabel.addEventListener('click', () => {
    form2.submit();
});
