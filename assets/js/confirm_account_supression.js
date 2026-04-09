document.addEventListener("DOMContentLoaded", function () {
    const confirmCheckbox = document.getElementById("confirmCheckbox");
    const confirmDelete = document.getElementById("confirmDelete");

    // Écouteur d'événement pour la case à cocher
    confirmCheckbox.addEventListener("change", function () {
        if (confirmCheckbox.checked) {
            confirmDelete.disabled = false; // Active le bouton
            confirmDelete.classList.add("enabled"); // Ajoute une classe pour le style
        } else {
            confirmDelete.disabled = true; // Désactive le bouton
            confirmDelete.classList.remove("enabled"); // Retire la classe
        }
    });
});
