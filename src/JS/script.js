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