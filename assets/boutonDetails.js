document.addEventListener("turbo:load", function () {
    // Utilisez "DOMContentLoaded" si vous n'avez plus Turbo
    // On sélectionne tous les boutons "Détails" de la page
    const boutonsDetails = document.querySelectorAll(".btn-details");

    boutonsDetails.forEach(function (bouton) {
        bouton.addEventListener("click", function (event) {
            // "this" représente le bouton cliqué.
            // On cherche la zone ".menu-details" qui est dans la même "carte" que ce bouton
            const carteParent = bouton.closest(".carte");
            if (!carteParent) return;
            const detailsDiv = carteParent.querySelector(".menu-details");
            if (!detailsDiv) return;

            // On ajoute ou on enlève la classe 'hide'
            detailsDiv.classList.toggle("hide");

            // Bonus : On change le texte du bouton en fonction de l'état
            if (detailsDiv.classList.contains("hide")) {
                this.textContent = "Détails";
            } else {
                this.textContent = "Masquer les détails";
            }
        });
    });
});
