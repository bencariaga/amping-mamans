document.addEventListener("DOMContentLoaded", function () {
    const errorContainer = document.querySelector(".error-container");

    if (errorContainer) {
        errorContainer.style.opacity = "0";
        errorContainer.style.transform = "translateY(20px)";

        setTimeout(() => {
            errorContainer.style.transition = "all 0.5s ease-in-out";
            errorContainer.style.opacity = "1";
            errorContainer.style.transform = "translateY(0)";
        }, 100);
    }

    const buttons = document.querySelectorAll(".btn-primary");
    buttons.forEach((button) => {
        button.addEventListener("mouseenter", function () {
            this.style.transform = "translateY(-2px)";
        });

        button.addEventListener("mouseleave", function () {
            this.style.transform = "translateY(0)";
        });
    });
});
