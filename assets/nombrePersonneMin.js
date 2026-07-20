document.addEventListener("DOMContentLoaded", function () {
    const selectMenu = document.getElementById("menu");
    const inputNombrePersonne = document.getElementById("nombrePersonne");

    if (
        selectMenu instanceof HTMLSelectElement &&
        inputNombrePersonne instanceof HTMLInputElement
    ) {
        selectMenu.addEventListener("change", function () {
            const selectedOption = selectMenu.options[selectMenu.selectedIndex];

            // On récupère le nouveau minimum (en string pour l'attribut min)
            const chiffreSecret =
                selectedOption.getAttribute("data-min") || "0";

            // 1. On met à jour la règle de validation
            inputNombrePersonne.min = chiffreSecret;

            // 2. NOUVEAU : On vérifie si la valeur actuelle est trop petite
            const currentValue = parseInt(inputNombrePersonne.value);
            const minValue = parseInt(chiffreSecret);

            // Si l'utilisateur a tapé quelque chose ET que ce nombre est inférieur au min
            if (!isNaN(currentValue) && currentValue < minValue) {
                // On force la valeur à monter au minimum requis
                inputNombrePersonne.value = minValue.toString();
            }
        });
    }
});
