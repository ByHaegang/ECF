document.addEventListener("click", function (event) {
    const target = event.target;
    if (!target || !(target instanceof HTMLElement)) return;

    const bouton = target.closest(".btn-details");
    if (!bouton) return;

    const carteParent = bouton.closest(".carte");
    if (!carteParent) return;

    const detailsDiv = carteParent.querySelector(".menu-details");
    if (!detailsDiv) return;

    detailsDiv.classList.toggle("hide");

    if (detailsDiv.classList.contains("hide")) {
        bouton.textContent = "Détails";
    } else {
        bouton.textContent = "Masquer les détails";
    }
});
