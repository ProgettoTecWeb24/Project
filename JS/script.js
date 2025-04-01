function menuMobile() {
    const navbarMenu = document.getElementById("navbar-links");
    const toggleIcon = document.getElementById("menu-toggle").querySelector("img");

    navbarMenu.classList.toggle("active");


    if (navbarMenu.classList.contains("active")) {
        toggleIcon.src = "assets/close.png"; 
    } else {
        toggleIcon.src = "assets/menu.png"; 
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

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("hidden");
    }

    const overlay = document.createElement("div");
    overlay.classList.add("modal-overlay");
    overlay.onclick = function () {
        closeModal(modalId);
    };
    document.body.appendChild(overlay);

    document.body.classList.add("modal-open");
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden");
    }

    const overlay = document.querySelector(".modal-overlay");
    if (overlay) {
        overlay.remove();
    }

    document.body.classList.remove("modal-open");
}

function checkUsername(str) {
    var re = /^[a-z0-9_.]{1,15}$/; // massimo 15 caratteri e accetta solo caratteri alfanumerici minuscoli, _ e .
    return re.test(str);
}

function checkPassword(str) {
    var re = /^(?=.*\d).{5,}$/; // almeno 5 caratteri e almeno un numero
    return re.test(str);
}


function validateLogin(){
    if (!validateUsernameLogin()){
      return false;
    } else if (!validatePasswordLogin()){
      return false;
    }
    return true;
}

function validateRegister() {
    if (!validateUsername()) {
        return false;
    } else if (!validatePassword()) {
        return false;
    } else if (!validateConfirmPassword()) {
        return false;
    }
    return true;
}

function validateReview() {
    if (!validateComment()) {
        return false;
    } 
    return true;
}

function validateChangeUsername() {
    if (!validateUsername()) {
        return false;
    }
    return true;
}

function validateUsernameLogin() {
    var x = document.getElementById("username");
    const errorClass = "error_text";
    const existingError = x.parentElement.querySelector(`.${errorClass}`);

    if (existingError) {
        x.parentElement.removeChild(existingError);
    }

    const node = document.createElement("p");
    node.classList.add(errorClass);
    node.setAttribute("role", "alert");

    if (x.value.trim() === "") {
        const textnode = document.createTextNode('Il campo <span lang="en">username</span> non può essere vuoto.');
        node.appendChild(textnode);
        x.parentElement.appendChild(node);
        return false;   
    }

    return true;
}

function validatePasswordLogin() {
    var password = document.getElementById("password");
    const errorClass = "error_text";
    const existingError = password.parentElement.querySelector(`.${errorClass}`);

    if (existingError) {
        password.parentElement.removeChild(existingError);
    }

    const errorNode = document.createElement("p");
    errorNode.classList.add(errorClass);
    errorNode.setAttribute("role", "alert");

    if (password.value.trim() === "") {
        errorNode.textContent = "Il campo password non può essere vuoto.";
        password.parentElement.appendChild(errorNode);
        return false;
    }

    return true;
}
function validateUsername() {
    var x = document.getElementById("username");
    const errorClass = "error_text";
    const existingError = x.parentElement.querySelector(`.${errorClass}`);
    
    if (existingError) {
        x.parentElement.removeChild(existingError);
    }

    const node = document.createElement("p");

    node.classList.add(errorClass);
    node.setAttribute("role", "alert");

    if (x.value.trim() === "") {
        node.innerHTML = 'Il campo <span lang="en">username</span> non può essere vuoto.';
        x.parentElement.appendChild(node);
        return false;
    }

    if (!checkUsername(x.value)) {
        node.innerHTML = '<span lang="en">Username</span> non valido. Massimo 15 caratteri e accetta solo caratteri alfanumerici minuscoli, _ e .';

        x.parentElement.appendChild(node);
        return false;
    }

    return true; 
}

function validatePassword() {
    var password = document.getElementById("password");
    const errorClass = "error_text";
    const existingError = password.parentElement.querySelector(`.${errorClass}`);

    if (existingError) {
        password.parentElement.removeChild(existingError);
    }

    const errorNode = document.createElement("p");
    errorNode.classList.add(errorClass);
    errorNode.setAttribute("role", "alert");
    
    if (password.value.trim() === "") {
        errorNode.textContent = "Il campo password non può essere vuoto.";
        password.parentElement.appendChild(errorNode);
        return false;
    }
    
    if (!checkPassword(password.value)) {
        errorNode.textContent = "Password non valida. Almeno 5 caratteri e almeno un numero";
        password.parentElement.appendChild(errorNode);
        return false;
    }
    
    return true;
}

function validateConfirmPassword() {
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("confirm_password");
    const errorClass = "error_text";
    const existingError = confirmPassword.parentElement.querySelector(`.${errorClass}`);
    
    if (existingError) {
        confirmPassword.parentElement.removeChild(existingError);
    }

    const errorNode = document.createElement("p");
    errorNode.classList.add(errorClass);
    errorNode.setAttribute("role", "alert");
    
    if (confirmPassword.value.trim() === "") {
        errorNode.textContent = "Il campo di conferma password non può essere vuoto.";
        confirmPassword.parentElement.appendChild(errorNode);
        return false;
    }
    
    if (!checkPassword(confirmPassword.value)) {
        errorNode.textContent = "Password non valida. Almeno 5 caratteri e almeno un numero";
        confirmPassword.parentElement.appendChild(errorNode);
        return false;
    }
    
    if (password.value !== confirmPassword.value) {
        errorNode.textContent = "Le password non corrispondono.";
        confirmPassword.parentElement.appendChild(errorNode);
        return false;
    }
    
    return true;
}

function goToPage(url, newTab = false) {
    if (newTab) {
        window.open(url, '_blank'); 
    } else {
        window.location.href = url; 
    }
}

// eliminazione scarpe
function impostaIdEliminazione() {
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const deleteIdInput = form.querySelector('.delete-id');
            const deleteUsername = form.querySelector('.username');
            const deleteBtn = form.querySelector('.delete-button');
            deleteBtn.setAttribute('name', 'delete');
            if (deleteIdInput) {
                deleteIdInput.setAttribute('name', 'delete_id');
            }
            if (deleteUsername) {
                deleteUsername.setAttribute('name', 'username');
            }
        });
    });
}
function impostaModifica() {
    document.querySelectorAll('.link-con-icona-modifica').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const modifyBtn = form.querySelector('.link-con-icona-modifica');
            modifyBtn.setAttribute('name', 'modifica');
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    impostaIdEliminazione();
    impostaModifica();
});

