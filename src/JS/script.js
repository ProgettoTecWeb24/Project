
function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.add('hidden'));
    document.getElementById(tabId).classList.remove('hidden');

    const buttons = document.querySelectorAll('.tab');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}
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