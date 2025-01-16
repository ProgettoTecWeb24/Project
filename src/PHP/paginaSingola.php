<?php
require_once('dbconnection.php');
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
            
            <div class="color-options">
                <h3>Colori Disponibili</h3>
                <div class="colors">';

foreach (explode(',', $scarpa['colori']) as $colore) {
    $content .= '<span class="color" style="background-color: ' . htmlspecialchars(trim($colore)) . ';"></span>';
}

$content .= '
                </div>
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
            <div class="add-review-section">
                <div class="add-review-wrapper">
                <button id="add-review-btn" onclick="openAddReviewForm()">+</button>
                    <span class="review-prompt">Lascia la tua Recensione!</span>
                </div>
                <div class="rating">
                <h3>Valutazione Utenti</h3>
                <img class="stars" src="../assets/' . round($mediaVotoUtenti) . '.png" alt="Valutazione Utenti">
            </div>
            </div>';
        } else {
            $content .= '
            <div class="add-review-section ">
                <div class="add-review-wrapper hidden">
                    <span class="review-prompt">Lascia la tua Recensione!</span>
                    <button id="add-review-btn">+</button>
                </div>
                <div class="rating">
                <h3>Valutazione Utenti</h3>
                <img class="stars" src="../assets/' . round($mediaVotoUtenti) . '.png" alt="Valutazione Utenti">
                <p>Media valutazioni utenti: ' . number_format($mediaVotoUtenti, 1) . '</p>
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
        </div>
        ';
    }
} else {
    $content .= '<p>Nessuna recensione disponibile.</p>';
}

$content .= '</div>
            </div>'; // Fine sezione recensioni

$HTMLpage = str_replace("{singlePage_content}", $content, $HTMLpage);

echo $HTMLpage;

$connection->endDbConnection();
include "footer.php";
?>
