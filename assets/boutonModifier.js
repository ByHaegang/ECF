document.addEventListener("click", function (event) {
    const target = event.target;

    if (!target || !(target instanceof Element)) return;

    const bouton = target.closest(".btn-modifier");
    if (!bouton) return;

    const ligneCommande = bouton.closest("tr");
    if (!ligneCommande) return;

    const ligneFormulaire = ligneCommande.nextElementSibling;
    if (!ligneFormulaire) return;

    ligneFormulaire.classList.toggle("hide");
});
