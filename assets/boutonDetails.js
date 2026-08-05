document.addEventListener("DOMContentLoaded", () => {
    const boutonsDetails = document.querySelectorAll(".btn-details");

    boutonsDetails.forEach((bouton) => {
        bouton.addEventListener("click", () => {
            const detailsDiv = bouton
                .closest(".carte")
                ?.querySelector(".menu-details");

            if (detailsDiv) {
                const isHidden = detailsDiv.classList.toggle("hide");

                bouton.textContent = isHidden
                    ? "Détails"
                    : "Masquer les détails";
            }
        });
    });
});
