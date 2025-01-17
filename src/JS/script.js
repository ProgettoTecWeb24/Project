function myFunction() {
    const navbarMenu = document.getElementById("navbar-menu");
    const toggleIcon = document.getElementById("menu-toggle").querySelector("img");

    navbarMenu.classList.toggle("active");

    
    if (navbarMenu.classList.contains("active")) {
        toggleIcon.src = "../assets/close.png"; 
    } else {
        toggleIcon.src = "../assets/menu.png"; 
    }

}

function openAddReviewForm() {
    const modal = document.getElementById("add-review-modal");
    if (modal) {
        modal.classList.remove("hidden");
    }

    const overlay = document.createElement("div");
    overlay.classList.add("modal-overlay");
    overlay.onclick = closeAddReviewForm;
    document.body.appendChild(overlay);

    document.body.classList.add("modal-open");
}

function closeAddReviewForm() {
    const modal = document.getElementById("add-review-modal");
    if (modal) {
        modal.classList.add("hidden");
    }

    const overlay = document.querySelector(".modal-overlay");
    if (overlay) {
        overlay.remove();
    }

    document.body.classList.remove("modal-open");
}
