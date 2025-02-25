<?php

$description = "Scopri caratteristiche, dettagli e recensioni di questa scarpa da corsa.";
$keywords = "scarpa,corsa,dettagli,recensioni,modello,valutazione,feedback,voto,"; 

require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}


$HTMLpage = file_get_contents('HTML/paginaSingola.html');
$breadcrumb_scarpa ="Pagina scarpa";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    header("Location: error404.php");
}

$queryScarpa = "SELECT * FROM SCARPA WHERE id = ?";
$scarpaResult = $connection->prepareAndExecute($queryScarpa, 'i', $id);
$scarpa = $scarpaResult[0] ?? null;

if (!$scarpa) {
    header("Location: error404.php");
}
$queryRecensioni = "SELECT r.username, r.voto, r.commento, u.ruolo 
                    FROM RECENSIONE r 
                    JOIN UTENTE u ON r.username = u.username
                    WHERE r.scarpa_id = ?";
$recensioni = $connection->prepareAndExecute($queryRecensioni, 'i', $id);

$queryMediaVoto = "SELECT AVG(r.voto) AS media_voto_utenti 
                   FROM RECENSIONE r
                   WHERE r.scarpa_id = ?";
$mediaVotoResult = $connection->prepareAndExecute($queryMediaVoto, 'i', $id);
$mediaVotoUtenti = $mediaVotoResult[0]['media_voto_utenti'] ?? 0;


$title = htmlspecialchars($scarpa['marca']).' '.htmlspecialchars($scarpa['nome']) . ' - CorsaIdeale';
$breadcrumb_scarpa = htmlspecialchars($scarpa['marca']).' '.htmlspecialchars($scarpa['nome']);
$keywords .= htmlspecialchars($scarpa['marca']) . ',' . htmlspecialchars($scarpa['nome']) . ',';
include "header.php";

$content = '
        <div class="shoe-main">

            <div class="shoe-image">
                <img src="assets/' . htmlspecialchars($scarpa['immagine']) . '" alt="" />
            </div>

            <div class="shoe-info">
                <div class="shoe-title">
                    <h2>' . htmlspecialchars($scarpa['marca']) . ' ' . htmlspecialchars($scarpa['nome']) . '</h2>
                    <ul class="shoe-details" aria-label="Dettagli della scarpa">
                        <li>Marca: ' . htmlspecialchars($scarpa['marca']) . '</li>
                        <li>Modello: ' . htmlspecialchars($scarpa['nome']) . '</li>
                        <li>Tipo: ' . htmlspecialchars($scarpa['tipo']) . '</li>
                        <li>
                            Valutazione Esperti: 
                            <img class="stars" src="assets/' . $scarpa['votoexp'] . '.png" alt="" />
                            <span>(' . number_format((float)$scarpa['votoexp'], 1, '.', '') . ')</span>
                        </li>
                        <li>Feedback: ' . htmlspecialchars($scarpa['feedback']) . '</li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="description-details"> 
            <div class="description-section">
                <h2>Descrizione</h2>
                <p id="textAreaDescription"> ' . htmlspecialchars($scarpa['descrizione']) . '</p>
                <textarea aria-describedby="textAreaDescription" name="description" id="descriptionArea" readonly class="description-text">' . htmlspecialchars($scarpa['descrizione']) . '</textarea>
            </div>
        </div>

        <div class="reviews-wrapper">
            <h2>Recensioni</h2>
                <div class="reviews-section">';

$hasUserReviewed = false;
if (isset($_SESSION['username'])) {
    foreach ($recensioni as $recensione) {
        if ($recensione['username'] === $_SESSION['username']) {
            $hasUserReviewed = true;
            break;
        }
    }
}

if (isset($_SESSION['username'])) {
    if ($hasUserReviewed) {
        $content .= '
            <div class="rating-wrapper">
            <div class="rating-section">
                <p>Valutazione Utenti</p>
                ' . (round($mediaVotoUtenti) > 0 ? '<img class="stars" src="assets/' . round($mediaVotoUtenti) . '.png" alt="" /><p>('. number_format((float)$mediaVotoUtenti, 1, '.', '') .')</p>' : '<p>Nessuna recensione disponibile.</p>') . '
            </div>
            <div class="add-review-section">
                <button type="button" id="modifica-btn" class="link-con-icona" onclick="openModal(\'edit-review-modal\')">
                    <img src="assets/edit.png" alt="modifica" class="icona-profilo" />
                </button>
                <button type="button" id="elimina-btn" class="link-con-icona" onclick="openModal(\'delete-review-modal\')">
                    <img src="assets/delete.png" alt="elimina" class="icona-profilo" />
                </button>
            </div>
            </div>
        ';
    } else {
        $content .= '
            <div class="rating-wrapper">
                <div class="rating-section">
                    <h3>Valutazione Utenti ('. number_format((float)$mediaVotoUtenti, 1, '.', '') .')</h3>
                    ' . (round($mediaVotoUtenti) > 0 ? '<img class="stars" src="assets/' . round($mediaVotoUtenti) . '.png" alt="" />' : '<p>Nessuna recensione disponibile.</p>') . '
                </div>
                <div class="add-review-section">
                <button id="add-review-btn" onclick="openModal(\'add-review-modal\')">+ <span class="visually-hidden">Aggiungi una recensione</span> </button>
                <span class="review-prompt">Lascia la tua Recensione!</span>
                </div>
            </div>
        ';
    }
} else {
    $content .= '
        <div class="add-review-section">
            <div class="rating-section">
                <h3>Valutazione Utenti ('. number_format((float)$mediaVotoUtenti, 1, '.', '') .')</h3>
                ' . (round($mediaVotoUtenti) > 0 ? '<img class="stars" src="assets/' . round($mediaVotoUtenti) . '.png" alt="" />' : '<p>Nessuna recensione disponibile.</p>') . '
            </div>
            <div class="add-review-section hidden">
            <button id="add-review-btn" 
                    onclick="openModal(\'add-review-modal\')" 
                    id="add-review-btn"> +
                <span class="visually-hidden">Aggiungi una recensione</span>
            </button>
                    <span class="review-prompt">Lascia la tua Recensione!</span>
                </div>
        </div>
    ';
}

$utenteVoto = "";
$utenteCommento = "";
if (isset($_SESSION['username'])) {
    foreach ($recensioni as $recensione) {
        if ($recensione['username'] === $_SESSION['username']) {
            $utenteVoto = $recensione['voto'];
            $utenteCommento = $recensione['commento'];
            break;
        }
    }
}

$content .= '
    <div id="edit-review-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal(\'delete-review-modal\')" aria-label="chiudi modale">&times;</span>
            <h2>Modifica la tua Recensione</h2>
            <form id="edit-review-form" action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '" method="POST">
                <input type="hidden" name="idscarpa" value="' . $id . '"/>
                <div class="input-add-scarpa">
                    <label for="edit-rating">Valutazione:</label>
                    <select class="sel-scarpa-admin" name="rating" id="edit-rating" required>
                        <option value="" disabled>-- Dai una valutazione --</option>
                        <option value="1" ' . ($utenteVoto == 1 ? 'selected' : '') . '>1 Stella</option>
                        <option value="2" ' . ($utenteVoto == 2 ? 'selected' : '') . '>2 Stelle</option>
                        <option value="3" ' . ($utenteVoto == 3 ? 'selected' : '') . '>3 Stelle</option>
                        <option value="4" ' . ($utenteVoto == 4 ? 'selected' : '') . '>4 Stelle</option>
                        <option value="5" ' . ($utenteVoto == 5 ? 'selected' : '') . '>5 Stelle</option>
                    </select>
                </div>
                <div class="input-add-scarpa">
                    <label for="commentEdit">Recensione:</label>
                    
                    <textarea name="commentEdit" id="commentEdit" rows="4" required>' . htmlspecialchars($utenteCommento) . '</textarea>
                </div>
                <button class="button" type="submit" name="edit">Modifica Recensione</button>
            </form>
        </div>
    </div>

    <div id="delete-review-modal" class="modal hidden">
        <div class="modal-content-delete">
            <div class="modal-header">
                <span class="close-btn" onclick="closeModal(\'delete-review-modal\')" aria-label="chiudi modale">&times;</span>
                <h2>Conferma Eliminazione</h2>
                <p>Sei sicuro di voler eliminare questa recensione?</p>
            </div>
            <form id="delete-review-form" action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '" method="POST">
                <input type="hidden" name="idscarpa" value="' . $id . '"/>
                <button class="button" type="submit" name="delete">Conferma</button>
            </form>
        </div>
    </div>
';


if (!empty($recensioni)) {
    if (isset($_SESSION['username'])) {
        foreach ($recensioni as $key => $recensione) {
            if ($recensione['username'] === $_SESSION['username']) {
                $userReview = $recensioni[$key];
                unset($recensioni[$key]);
                array_unshift($recensioni, $userReview);
                break;
            }
        }
    }

    foreach ($recensioni as $recensione) {
        $content .= '
        <div class="review">
            <div class="review-icon">
                <img src="assets/'. $recensione['ruolo'] .'-mini.png" alt="" />
            </div>
            <div class="review-info">
                <div class="review-header">
                    <div class="review-left">
                        <span class="review-user">' . htmlspecialchars($recensione['username']) . '</span>
                    </div>
                    <div class="review-stars">
                        <img class="stars" src="assets/' . $recensione['voto'] . '.png" alt="" />
                        <span class="review-rating">(' . number_format((float)$recensione['voto'], 1, '.', '') . ')</span>
                    </div>
                </div>
                <p id="areaCommento"> ' . htmlspecialchars($recensione['commento']) . ' </p>
                <textarea aria-describedby="areaCommento" class="review-text" readonly>' . htmlspecialchars($recensione['commento']) . '</textarea>
            </div>
        </div>';
    }
} else {
    $content .= '<p>Nessuna recensione disponibile.</p>';
}

$content .= '
    <div id="add-review-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal(\'delete-review-modal\')" aria-label="chiudi modale">&times;</span>
            <h2>Lascia una Recensione</h2>
            <form id="review-form" action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '" method="POST">
                <input type="hidden" name="idscarpa" id="idscarpa" value="' . $id . '"/>
                
                <div class="input-add-scarpa">
                    <label for="rating">Valutazione:</label>
                    <select class="sel-scarpa-admin" name="rating" id="rating" required>
                        <option value="" disabled selected>-- Dai una valutazione --</option>
                        <option value="1">1 Stella</option>
                        <option value="2">2 Stelle</option>
                        <option value="3">3 Stelle</option>
                        <option value="4">4 Stelle</option>
                        <option value="5">5 Stelle</option>
                    </select>
                </div>
                
                <div class="input-add-scarpa">
                    <label for="commentAdd">Recensione:</label>
                    <textarea name="commentAdd"  id="commentAdd" rows="4" required placeholder="Scrivi la tua recensione"></textarea>
                </div> 
                
                <button id="add_review_button"  type="submit" name="submit_add_review">Invia Recensione</button>
            </form>
        </div>
    </div>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_add_review'])) {
    $rating = $_POST['rating'] ?? '';
    $comment = $_POST['commentAdd'] ?? '';
    $scarpa_id = intval($_POST['idscarpa'] ?? 0);
    $username = $_SESSION['username'] ?? '';

    if (!empty($rating) && !empty($comment) && $scarpa_id > 0 && !empty($username)) {
        $reviewAdded = $connection->insertNewReview($username, $scarpa_id, $rating, sanitizeInput($comment));
        if ($reviewAdded) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $scarpa_id);
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $rating = $_POST['rating'] ?? '';
    $comment = $_POST['commentEdit'] ?? '';
    $scarpa_id = intval($_POST['idscarpa'] ?? 0);
    $username = $_SESSION['username'] ?? '';

    if (!empty($rating) && !empty($comment) && $scarpa_id > 0 && !empty($username)) {
        $reviewUpdated = $connection->updateReview($username, $scarpa_id, $rating, sanitizeInput($comment));
        if ($reviewUpdated) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $scarpa_id);
            exit();
        }
    }
}

if (isset($_POST['delete'])) {
    $scarpa_id = intval($_POST['idscarpa'] ?? 0);
    $username = $_SESSION['username'] ?? '';
    if ($connection->deleteReview($scarpa_id, $username)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $scarpa_id);
        exit();
    } else {
        $info = '<p class="error_text" id="info" role="alert">Errore: eliminazione non possibile, la scarpa non esiste</p>';
    }
}

$content .= '
    </div>
</div>';

$HTMLpage = str_replace("{breadcrumb_scarpa}", $breadcrumb_scarpa, $HTMLpage);
$HTMLpage = str_replace("{singlePage_content}", $content, $HTMLpage);

echo $HTMLpage;

$connection->endDbConnection();
include "footer.php";
?>
