function myFunction() {
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


//function checkComment(comment) {
//    // Esegui una semplice validazione per i caratteri
//    const regex = /^[a-zA-Z0-9\s.,!?()]*$/; // Permette lettere, numeri, spazi e punteggiatura base
//    return regex.test(comment) && comment.length <= 1160;
//}



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
    
    // Rimuove il messaggio di errore esistente, se presente
    if (existingError) {
        x.parentElement.removeChild(existingError);
    }

    // Crea il nodo del messaggio di errore
    const node = document.createElement("p");

    node.classList.add(errorClass);
    node.setAttribute("role", "alert");

    // Controlla se il campo è vuoto
    if (x.value.trim() === "") {
        node.innerHTML = 'Il campo <span lang="en">username</span> non può essere vuoto.';
        x.parentElement.appendChild(node);
        return false;
    }

    // Controlla la validità dello username
    if (!checkUsername(x.value)) {
        node.innerHTML = '<span lang="en">Username</span> non valido.';

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
/*
function validateComment() {
    console.log("validateComment");

    var comment = document.getElementById("comment");
    console.log("Comment element: ", comment); // Verifica se l'elemento è correttamente selezionato
    console.log("Valore del commento: ", comment.value); // Mostra il valore del commento

    var submitButton = document.getElementById("add_review_button");
    const errorClass = "error_text";

    const existingErrors = submitButton.parentElement.querySelectorAll(`.${errorClass}`);
    existingErrors.forEach(function(error) {
        error.remove(); // Rimuove tutti gli errori
    });

    const errorNode = document.createElement("p");
    errorNode.classList.add(errorClass);
    errorNode.setAttribute("role", "alert");

    let errorMessage = "";

    if (comment.value.trim() === "") {
        console.log("Il commento è vuoto");
        errorMessage += "Il commento della recensione non può essere vuoto. ";
    }

    if (!checkComment(comment.value)) {
        console.log("Il commento non è valido");
        errorMessage += "La recensione non è valida. Deve contenere al massimo 1160 caratteri e accettare solo caratteri alfanumerici e punteggiatura.";
    }

    if (errorMessage) {
        errorNode.textContent = errorMessage.trim();
        // Aggiunge l'errore sopra il bottone "Invia Recensione"
        submitButton.parentElement.insertBefore(errorNode, submitButton);
        return false;
    }

    console.log("Il commento è valido: ", comment.value);

    return true;
}

*/