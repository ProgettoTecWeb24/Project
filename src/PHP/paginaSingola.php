<?php
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

$HTMLpage = file_get_contents('../HTML/paginaSingola.html');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM SCARPA WHERE id = ?";

$scarpa = $connection->prepareAndExecute($query, 'i', $id);  
$content = "";
$content .= '
    <!-- Contenitore principale per immagine e dettagli -->
    <div class="shoe-main">
        <!-- Contenitore immagine -->
        <div class="shoe-image">
            <img src="../assets/nike.png" alt="Nike Air Max">
        </div>

        <!-- Contenitore dettagli a destra -->
        <div class="shoe-info">
            <!-- Titolo e modello -->
            <div class="shoe-title">
                <h1>Nike Air Max</h1>
                <h2>Modello: Nike Air Max</h2>
            </div>

            <!-- Stelle esperti -->
            <div class="rating">
                <h3>Valutazione Esperti</h3>
                <img class="stars" src="../assets/1.png"/>
                <p>Nessun feedback disponibile.</p>
            </div>
            
            <!-- Stelle utenti -->
            <div class="rating">
                <h3>Valutazione Utenti</h3>
                <img class="stars" src="../assets/1.png"/>
            </div>

            <!-- Colori disponibili -->
            <div class="color-options">
                <h3>Colori Disponibili</h3>
                <div class="colors">
                    <span class="color" style="background-color: orange;"></span>
                    <span class="color" style="background-color: black;"></span>
                    <span class="color" style="background-color: white;"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Dettagli -->
    <div class="details-section">
        <h3>Dettagli</h3>
        <p>Dettagli tecnici della scarpa...</p>
    </div>

    <!-- Sezione Descrizione -->
    <div class="description-section">
        <h3>Descrizione</h3>
        <p>Scarpa comoda e versatile per ogni occasione.</p>
    </div>

    <div class="reviews-section">
    <h3>Recensioni</h3>
    <div class="review">
    <!-- Icona Rabbit -->
    <div class="review-icon">
        <img src="../assets/user.png" alt="Rabbit">
    </div>

    <!-- Info utente -->
    <div class="review-info">
        <div class="review-header">
            <div class="review-left">
                <span class="review-user">Mario Rossi</span>
                <img class="user-badge" src="../assets/rabbit.png" alt="User Icon">
            </div>

            <div class="review-stars">
                <img class="stars" src="../assets/1.png" alt="Valutazione">
            </div>
        </div>
        <div class="review-text">Ottime scarpe, molto comode per correre.</div>
    </div>
</div>
';




$HTMLpage = str_replace("{singlePage_content}", $content, $HTMLpage);

echo $HTMLpage;
?>
