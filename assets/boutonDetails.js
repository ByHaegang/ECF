document.addEventListener("turbo:load", function () {
    const boutonsDetails = document.querySelectorAll(".btn-details");
    boutonsDetails.forEach(function (bouton) {
        bouton.addEventListener("click", function (event) {
            const carteParent = bouton.closest(".carte");
            if (!carteParent) return;
            const detailsDiv = carteParent.querySelector(".menu-details");
            if (!detailsDiv) return;
            detailsDiv.classList.toggle("hide");
            if (detailsDiv.classList.contains("hide")) {
                this.textContent = "Détails";
            } else {
                this.textContent = "Masquer les détails";
            }
        });
    });
});
