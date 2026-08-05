document.addEventListener("DOMContentLoaded", () => {
    const boutonsModifier = document.querySelectorAll(".btn-modifier");

    boutonsModifier.forEach((bouton) => {
        bouton.addEventListener("click", () => {
            bouton.closest("tr")?.nextElementSibling?.classList.toggle("hide");
        });
    });
});
