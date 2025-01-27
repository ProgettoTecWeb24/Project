<?php
$location = 'Profilo Recensioni';

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

if (!empty($_SESSION['username'])) {
    $connection = new DbConnection();
    if (!$connection->startDbConnection()) {
        die("Connessione al database fallita.");
    }

    include "header.php";
    $HTMLpage = file_get_contents('../HTML/profiloRecensioni.html');

        // Sostituisce il nome utente nel template
        $HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

    // Aggiungo le funzionalità di modifica e eliminazione recensione
    if(isset($_POST['elimina'])){
        $qDelete = "DELETE FROM RECENSIONE WHERE username ='" . $_SESSION['username'] . "'AND scarpa_id ='" . $_POST['elimina'] . "'";
        $result = $connection->query($qDelete);
        header('Location: profiloRecensioni.php#reviews');
    }

    if(isset($_POST['modifica'])){
        $qModifica = "UPDATE RECENSIONE SET voto = '" . $_POST['rating'] . "', commento =? WHERE username ='" . $_SESSION['username'] . "'AND scarpa_id ='" . $_POST['modifica'] . "'";
        $modifica = $connection->prepareAndExecute($qModifica, 's', $_POST['newCommento']);
    }

    $qRecensioni = "SELECT r.username, r.voto, r.commento, r.scarpa_id, s.nome, s.immagine
                    FROM RECENSIONE r JOIN SCARPA s
                    WHERE r.scarpa_id = s.id AND r.username ='" . $_SESSION['username'] . "'";
    $recensioni = $connection->query($qRecensioni);

    $content = "";
    if (!empty($recensioni)) {
        foreach ($recensioni as $recensione) {
            $content .= '
            <a name=review' . htmlspecialchars($recensione['scarpa_id']) . '>
            <form action="profiloRecensioni.php#review'. htmlspecialchars($recensione['scarpa_id']).'" method="POST">
            <div class="review-profile">
                <div class="review-icon-shoes">
                    <img src="../assets/' . htmlspecialchars($recensione['immagine'])  . '" alt="' . htmlspecialchars($recensione['nome']) . '" />
                </div>
                <div class="review-info-profile">
                   
                    <div class="review-header">
                        <div class="review-left">
                            <span class="review-user">' . htmlspecialchars($recensione['nome']) . '</span>
                        </div>
                    <div>
                        <label for="rating">Valutazione:</label>
                        <select class="select-review-profile" name="rating" id="rating" required>';
                        if($recensione['voto'] == '1'){
                            $content .= '<option value="1" selected>1 Stella</option>
                            <option value="2">2 Stelle</option>
                            <option value="3">3 Stelle</option>
                            <option value="4">4 Stelle</option>
                            <option value="5">5 Stelle</option>';
                        }elseif($recensione['voto'] == '2'){
                            $content .= '<option value="1">1 Stella</option>
                            <option value="2" selected>2 Stelle</option>
                            <option value="3">3 Stelle</option>
                            <option value="4">4 Stelle</option>
                            <option value="5">5 Stelle</option>';
                        }elseif($recensione['voto'] == '3'){
                            $content .= '<option value="1">1 Stella</option>
                            <option value="2">2 Stelle</option>
                            <option value="3" selected>3 Stelle</option>
                            <option value="4">4 Stelle</option>
                            <option value="5">5 Stelle</option>';
                        }elseif($recensione['voto'] == '4'){
                            $content .= '<option value="1">1 Stella</option>
                            <option value="2">2 Stelle</option>
                            <option value="3">3 Stelle</option>
                            <option value="4" selected>4 Stelle</option>
                            <option value="5">5 Stelle</option>';
                        }elseif($recensione['voto'] == '5'){
                            $content .= '<option value="1">1 Stella</option>
                            <option value="2">2 Stelle</option>
                            <option value="3">3 Stelle</option>
                            <option value="4">4 Stelle</option>
                            <option value="5" selected>5 Stelle</option>';
                        }
                        $content .='</select>
                    </div>
                    </div>
                    <textarea class="inputRecensione" name="newCommento">' . htmlspecialchars($recensione['commento']) . '</textarea>
                    
                </div>
            <div class="div-coloum">
                <button type="submit" id="modifica" name="modifica" class="link-con-icona" value="' . htmlspecialchars($recensione['scarpa_id']) .'">
                    <img src="../assets/edit.svg" alt="modifica" class="icona-profilo" />
                </button>
                <button type="submit" id="elimina" name="elimina" class="link-con-icona" value="' . htmlspecialchars($recensione['scarpa_id']) .'">
                    <img src="../assets/delete.svg" alt="elimina" class="icona-profilo" />
                </button>
            </div>   
            </div>
            </form>
            </a>
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