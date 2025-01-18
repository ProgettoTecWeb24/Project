<?php
require_once('dbconnection.php');
require_once('controls.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

include "header.php";
$HTMLpage = file_get_contents('../HTML/paginaSingola.html');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    die("ID scarpa non valido.");
}

$queryScarpa = "SELECT * FROM SCARPA WHERE id = ?";
$scarpaResult = $connection->prepareAndExecute($queryScarpa, 'i', $id);
$scarpa = $scarpaResult[0] ?? null;

if (!$scarpa) {
    die("Scarpa non trovata.");
}

$queryRecensioni = "SELECT r.username, r.voto, r.commento 
                    FROM RECENSIONE r 
                    WHERE r.scarpa_id = ?";
$recensioni = $connection->prepareAndExecute($queryRecensioni, 'i', $id);

$queryMediaVoto = "SELECT AVG(r.voto) AS media_voto_utenti 
                   FROM RECENSIONE r
                   WHERE r.scarpa_id = ?";
$mediaVotoResult = $connection->prepareAndExecute($queryMediaVoto, 'i', $id);
$mediaVotoUtenti = $mediaVotoResult[0]['media_voto_utenti'] ?? 0;


$content = '
    <div class="shoe-main">
        <div class="shoe-image">
            <img src="../assets/' . htmlspecialchars($scarpa['immagine']) . '" alt="' . htmlspecialchars($scarpa['nome']) . '">
        </div>
        <div class="shoe-info">
            <div class="shoe-title">
                <h1>' . htmlspecialchars($scarpa['nome']) . '</h1>
                <h2>Modello: ' . htmlspecialchars($scarpa['nome']) . '</h2>
            </div>
            <div class="rating">
                <h3>Valutazione Esperti</h3>
                <img class="stars" src="../assets/' . $scarpa['votoexp'] . '.png" alt="Valutazione Esperti">
                <p>' . htmlspecialchars($scarpa['feedback']) . '</p>
            </div>
        
        </div>
    </div>
    <div class="details-section">
        <h3>Dettagli</h3>
        <p>' . htmlspecialchars($scarpa['dettagli']) . '</p>
    </div>
    <div class="description-section">
        <h3>Descrizione</h3>
        <p>' . htmlspecialchars($scarpa['descrizione']) . '</p>
    </div>
    <div class="reviews-wrapper">
    <h3>Recensioni</h3>
    <div class="reviews-section">';

if (isset($_SESSION['username'])) {
    $content .= '
        <div class="rating-wrapper">
            <div class="rating-section">

                <h3>Valutazione Utenti</h3>
                ' . (round($mediaVotoUtenti) > 0 ? '<img class="stars" src="../assets/' . round($mediaVotoUtenti) . '.png" alt="Valutazione Utenti">' : '<p>Nessuna recensione disponibile.</p>') . '
            </div>
            <div class="add-review-section">
            
            
                <button id="add-review-btn" onclick="openAddReviewForm()">+</button>
                <span class="review-prompt">Lascia la tua Recensione!</span>

            </div>
        </div>
    ';
} else {
    $content .= '
    <div class="add-review-section">
    <div class="rating">
    <h3>Valutazione Utenti</h3>
    <div class="add-review-wrapper hidden">
        <button id="add-review-btn" onclick="openAddReviewForm()">+</button>
        <span class="review-prompt">Lascia la tua Recensione!</span>
    </div>
        <img class="stars" src="../assets/' . round($mediaVotoUtenti) . '.png" alt="Valutazione Utenti">
    </div>
    </div>';
}

if (!empty($recensioni)) {
    foreach ($recensioni as $recensione) {
        $content .= '
        <div class="review">
            <div class="review-icon">
                <img src="../assets/wolf-mini.png" alt="User Icon">
            </div>
            <div class="review-info">
                <div class="review-header">
                    <div class="review-left">
                        <span class="review-user">' . htmlspecialchars($recensione['username']) . '</span>
                    </div>
                    <div class="review-stars">
                        <img class="stars" src="../assets/' . $recensione['voto'] . '.png" alt="Voto Utente">
                    </div>
                </div>
                <div class="review-text">' . htmlspecialchars($recensione['commento']) . '</div>
            </div>
        </div>';
    }
} else {
    $content .= '<p>Nessuna recensione disponibile.</p>';
}

$content .= '
    <div id="add-review-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAddReviewForm()">&times;</span>
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
                    <label for="comment">Recensione:</label>
                    <textarea name="comment" id="comment" rows="4" required placeholder="Scrivi la tua recensione"></textarea>
                </div>
                
                <button class="button" type="submit" name="submit">Invia Recensione</button>
            </form>
        </div>
    </div>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $rating = $_POST['rating'] ?? '';
    $comment = $_POST['comment'] ?? '';
    $scarpa_id = intval($_POST['idscarpa'] ?? 0);
    $username = $_SESSION['username'] ?? '';

    if (!empty($rating) && !empty($comment) && $scarpa_id > 0 && !empty($username)) {
        $reviewAdded = $connection->insertNewReview($username, $scarpa_id, $rating, sanitizeInput($comment));
    }
}

$content .= '
    </div>
</div>';


$HTMLpage = str_replace("{singlePage_content}", $content, $HTMLpage);

echo $HTMLpage;

$connection->endDbConnection();
include "footer.php";
?>
