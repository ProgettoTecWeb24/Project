<?php

$title = "Le tue recensioni - CorsaIdeale";
$description = "Modifica o elimina le recensioni che hai lasciato sulle scarpe da corsa.";
$keywords = "recensioni,personali,scarpe,voto,valutazioni,commenti,profilo";

require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

if (!empty($_SESSION['username'])) {
    $connection = new DbConnection();
    if (!$connection->startDbConnection()) {
        die("Connessione al database fallita.");
    }

    include "header.php";
    $HTMLpage = file_get_contents('HTML/profiloRecensioni.html');

    // Sostituisce il nome utente nel template
    $HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

    // Modifica recensione
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifica'])) {
        $rating = $_POST['rating'] ?? '';
        $comment = $_POST['newCommento'] ?? '';
        $scarpa_id = intval($_POST['modifica'] ?? 0);
        $username = $_SESSION['username'] ?? '';

        if (!empty($rating) && !empty($comment) && $scarpa_id > 0 && !empty($username)) {
            $reviewUpdated = $connection->updateReview($username, $scarpa_id, $rating, sanitizeInput($comment));
            if ($reviewUpdated) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $scarpa_id);
                exit();
            }
        }
    }

    // Eliminazione recensione
    if(isset($_POST['delete'])){
        $idScarpa = intval($_POST['delete_id']);
        $username = $_SESSION['username'] ?? '';
        if($connection->deleteReview($idScarpa, $username)){
            $info = '<p class="success_text" id="info" role="alert">La recensione è stata eliminata con successo</p>';
        }else{
            $info = '<p class="error_text" id="info" role="alert">Errore: eliminazione non possibile, la scarpa non esiste</p>';
        }
    }

    $qRecensioni = "SELECT r.username, r.voto, r.commento, r.scarpa_id, s.nome, s.marca, s.immagine
                    FROM RECENSIONE r 
                    JOIN SCARPA s ON r.scarpa_id = s.id
                    WHERE r.username ='" . $_SESSION['username'] . "'";
    $recensioni = $connection->query($qRecensioni);

    $content = "";
    if ($recensioni) {
        if ($recensioni->num_rows == 0) {
            $HTMLpage = str_replace('<div class="reviews-wrapper">', '<div class="error-cards">', $HTMLpage);
            $content .= '<div class="error-card">  <p class="tipo">Non sono presenti recensioni.</p> </div>';
        } else {
            foreach ($recensioni as $recensione) {
                $content .= '
                <a id=review' . htmlspecialchars($recensione['scarpa_id']) . '></a>
                <form action="profiloRecensioni.php#review' . htmlspecialchars($recensione['scarpa_id']) . '" method="POST">
                    <div class="review-profile">
                        <div class="review-icon-shoes">
                            <img src="assets/' . htmlspecialchars($recensione['immagine']) . '" alt="Immagine della scarpa ' . htmlspecialchars($recensione['nome']) . '" />
                        </div>
                        <div class="review-info-profile">
                            <div class="review-header">
                                <div class="review-left">
                                    <span class="review-user">' . htmlspecialchars($recensione['marca']) . ' ' . htmlspecialchars($recensione['nome']) . '</span>
                                </div>
                                <div>
                                    <label for="rating' . $recensione['scarpa_id'] . '">Valutazione:</label>
                                    <select class="select-review-profile" name="rating" id="rating' . $recensione['scarpa_id'] . '">';
                for ($i = 1; $i <= 5; $i++) {
                    $selected = ($recensione['voto'] == $i) ? 'selected' : '';
                    $content .= '<option value="' . $i . '" ' . $selected . '>' . $i . ' Stella' . ($i > 1 ? 'e' : '') . '</option>';
                }
                $content .= '</select>
                                </div>
                            </div>
                            <textarea class="inputRecensione" name="newCommento">' . htmlspecialchars($recensione['commento']) . '</textarea>
                        </div>
                        <div class="add-review-section">
                            <button type="submit" id="modifica' . $recensione['scarpa_id'] . '" name="modifica" class="link-con-icona" value="' . htmlspecialchars($recensione['scarpa_id']) . '">
                                <img src="assets/edit.svg" alt="modifica" class="icona-profilo  " />
                            </button>
                            <button type="button" aria-label="Elimina scarpa '.$recensione["nome"] .'" class="link-con-icona" name="delete-shoe-'.$recensione["scarpa_id"].'" onclick="openModal(\'delete-shoe-admin-modal-'.$recensione["scarpa_id"].'\')">
                                    <img src="assets/delete.svg" alt="elimina" class="icona-profilo" />
                            </button>
                            <div id="delete-shoe-admin-modal-'.$recensione["scarpa_id"].'" class="delete-admin-modal hidden">
                                <div class="modal-content-delete">
                                    <div class="modal-header">
                                        <span class="close-btn" onclick="closeModal(\'delete-shoe-admin-modal-'.$recensione["scarpa_id"].'\')">&times;</span>
                                        <h2>Conferma Eliminazione</h2>
                                        <p>Sei sicuro di voler eliminare questa scarpa?</p>
                                    </div>
                                    <form id="delete-review-admin-form-'.$recensione["scarpa_id"].'" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                                        <input type="hidden" class="delete-id" value="'.$recensione["scarpa_id"].'"/>
                                        <button aria-label="Conferma eliminazione '.$recensione["nome"].'" class="delete-button" type="submit">Conferma</button>
                                    </form>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    </div>
                    <textarea class="inputRecensione" name="newCommento" aria-label="testo della recensione scarpa '. htmlspecialchars($recensione['nome']).'">' . htmlspecialchars($recensione['commento']) . '</textarea>
                    
                </div>
            <div class="add-review-section">
                <button type="submit" id="modifica' . $recensione['scarpa_id'] . '" name="modifica" class="link-con-icona" value="' . htmlspecialchars($recensione['scarpa_id']) .'">
                    <img src="assets/edit.svg" alt="modifica" class="icona-profilo" />
                </button>
                <button type="submit" id="elimina' . $recensione['scarpa_id'] . '" name="elimina" class="link-con-icona" value="' . htmlspecialchars($recensione['scarpa_id']) .'">
                    <img src="assets/delete.svg" alt="elimina" class="icona-profilo" />
                </button>
            </div>   
            </div>
            </form>
            
            ';
        }
    } else {
        $content .= '<p>Nessuna recensione disponibile.</p>';
    }




    
    $HTMLpage = str_replace("{yourreviews}", $content, $HTMLpage);
    echo $HTMLpage;

    $connection->endDbConnection();
    include "footer.php";
} else {
    header('Location: accedi.php');
    die();
}
?>
