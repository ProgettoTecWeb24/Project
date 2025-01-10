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
    <div class="shoe-title">
        <h1>' . htmlspecialchars($scarpa['nome']) . '</h1>
        <h2>Modello: ' . htmlspecialchars($scarpa['nome']) . '</h2>
    </div>

    <div class="shoe-image">
        <img src="../assets/nike.png" alt="">
    </div>

    <div class="color-options">
        <h3>Colori Disponibili</h3>
        <div class="colors">
            <!-- Puoi aggiungere un array o un ciclo per gestire i colori dinamicamente -->
            <span class="color" style="background-color: orange;"></span>
            <span class="color" style="background-color: black;"></span>
            <span class="color" style="background-color: white;"></span>
        </div>
    </div>

    <div class="review-section">
        <h3>Recensioni</h3>
        <!-- Aggiungi logica per visualizzare recensioni reali dal database se presente -->
        <p>' . ($scarpa['feedback'] ? htmlspecialchars($scarpa['feedback']) : 'Nessuna recensione disponibile.') . '</p>
    </div>

    <div class="tabs">
        <button class="tab active" onclick="showTab(\'description\')">Descrizione</button>
        <button class="tab" onclick="showTab(\'details\')">Dettagli</button>
        <button class="tab" onclick="showTab(\'feedback\')">Feedback</button>
    </div>

    <div class="tab-content" id="description">
        <p>' . htmlspecialchars($scarpa['descrizione']) . '</p>
    </div>
    <div class="tab-content hidden" id="details">
        <p>Dettagli tecnici della scarpa...</p> <!-- Aggiungi dettagli tecnici se necessari -->
    </div>
    <div class="tab-content hidden" id="feedback">
        <p>' . ($scarpa['feedback'] ? htmlspecialchars($scarpa['feedback']) : 'Nessun feedback disponibile.') . '</p>
    </div>
';

$HTMLpage = str_replace("{singlePage_content}", $content, $HTMLpage);

echo $HTMLpage;
?>
