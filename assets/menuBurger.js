const burgerBtn = document.querySelector(".burger-btn");
const navbar = document.querySelector(".navbar");

if (burgerBtn && navbar) {
    burgerBtn.addEventListener("click", () => {
        navbar.classList.toggle("active");
    });
}
