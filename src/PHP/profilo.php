<?php
$location = 'Profilo';

require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');
if (!empty($_SESSION['username'])) {

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}

if (isset($_POST['logout'])) {
    session_destroy();
    session_abort();
    header("Location: index.php");
    exit();
}

if(isset($_POST['likePress'])){
    if (!empty($_SESSION['username'])) {
        $qCheck = "SELECT * FROM likes WHERE scarpa_id ='" . $_POST['likePress'] ."' AND username = '" . $_SESSION['username'] . "'";
        $result = $connection->query($qCheck);
        if(mysqli_num_rows($result) === 0){
            $qLike = "INSERT INTO likes (username,scarpa_id,data_aggiunta) VALUES('" . $_SESSION['username'] . "','" . $_POST['likePress'] . "','" . date("Y/m/d") . "')";
            $result = $connection->query($qLike);
        }else{
            $qLike = "DELETE FROM likes WHERE username ='" . $_SESSION['username'] . "'AND scarpa_id ='" . $_POST['likePress'] . "'";
            $result = $connection->query($qLike);
        }
    }else{
        header('Location: accedi.php');
        die();
    }
}

if(isset($_POST['elimina'])){
        $qDelete = "DELETE FROM recensione WHERE username ='" . $_SESSION['username'] . "'AND scarpa_id ='" . $_POST['elimina'] . "'";
        $result = $connection->query($qDelete);
        header('Location: profilo.php#reviews');
}

if(isset($_POST['modifica'])){
    $qModifica = "UPDATE recensione SET voto = '" . $_POST['rating'] . "', commento =? WHERE username ='" . $_SESSION['username'] . "'AND scarpa_id ='" . $_POST['modifica'] . "'";
    $modifica = $connection->prepareAndExecute($qModifica, 's', $_POST['newCommento']);
    //$result = $connection->query($qModifica);
}

if(isset($_POST['salvaImpos'])){
    if(!empty($_POST['newUser'])){
        $qSalva = "UPDATE utente SET username = '" . $_POST['newUser'] . "', ruolo =? WHERE username ='" . $_SESSION['username'] . "'";
        $modifica = $connection->prepareAndExecute($qSalva, 's', $_POST['kmsett']);
        $_SESSION['username'] = $_POST['newUser'];
    }else{
        $qSalva = "UPDATE utente SET ruolo =? WHERE username ='" . $_SESSION['username'] . "'";
        $modifica = $connection->prepareAndExecute($qSalva, 's', $_POST['kmsett']);
    }
}




include "header.php";
$HTMLpage = file_get_contents('../HTML/profilo.html');

$HTMLpage = str_replace("{user}", $_SESSION['username'], $HTMLpage);

$qCategoria = "SELECT ruolo
                FROM utente
                WHERE username ='" . $_SESSION['username'] . "'";
$categorie = $connection->query($qCategoria);

if (!empty($categorie)) {
    foreach ($categorie as $categoria) {
        $HTMLpage = str_replace('<option value="'. $categoria['ruolo'] . '"', '<option value="'. $categoria['ruolo'] . '" selected', $HTMLpage);
    }
}

$qRecensioni = "SELECT r.username, r.voto, r.commento, r.scarpa_id, s.nome, s.immagine
                FROM RECENSIONE r JOIN scarpa s
                WHERE r.scarpa_id = s.id AND r.username ='" . $_SESSION['username'] . "'";
$recensioni = $connection->query($qRecensioni);



$query ="
SELECT *
FROM scarpa
WHERE id IN ( SELECT scarpa_id FROM likes WHERE username ='" . $_SESSION['username'] . "')";

//$query = "SELECT * FROM scarpa WHERE nome LIKE '%'";

if(!empty($_POST['ricercaLike'])){
    $query = "
SELECT *
FROM scarpa
WHERE id IN ( SELECT scarpa_id FROM 'likes' WHERE username = 'user') AND nome LIKE '%" . $_POST['ricercaLike'] . "%' ";
}
$result = $connection->query($query);

$cardsHTML = "";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cardsHTML .= '
            <a href="paginaSingola.php?id=' . urlencode($row['id']) . '" class="card-link">
                <div class="card">
                    <img class="img-card" src="../assets/' . htmlspecialchars($row['immagine'])  . '" alt="' . htmlspecialchars($row['nome']) . '">
                    <div class="text-card">
                        <h3>' . htmlspecialchars($row['nome']) . '</h3>
                        <p class="marca">Marca: ' . htmlspecialchars($row['marca']) . '</p>
                        <p class="modello">Modello: ' . htmlspecialchars($row['nome']) . '</p>
                        <p class="feedback">Feedback: ' . htmlspecialchars($row['feedback']) . '</p>
                        <form action="profilo.php#likes" method="POST">
                        <button class="like-button" type="submit" name="likePress" value="' . htmlspecialchars($row['id']) .'">{like}<path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </button>
                        </form>
                    </div>
                </div>
            </a>
        ';
    }
} else {
    $cardsHTML = "Errore nell'esecuzione della query.";
}

$HTMLpage = str_replace("{shoesliked}", $cardsHTML, $HTMLpage);

if (!empty($_SESSION['username'])) {
    $qFill = "SELECT * FROM likes WHERE username = '" . $_SESSION['username'] . "'"; 
    $result = $result = $connection->query($qFill);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $oldStr = 'value="' . $row['scarpa_id'] .'">{like}';
            $newStr = 'value="' . htmlspecialchars($row['scarpa_id']) .'"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon-filled">';
            $HTMLpage = str_replace($oldStr, $newStr, $HTMLpage);   
        }
    }
}
$HTMLpage = str_replace("{like}", '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon">', $HTMLpage);

$content = "";
if (!empty($recensioni)) {
    foreach ($recensioni as $recensione) {
        $content .= '
        <a name=review' . htmlspecialchars($recensione['scarpa_id']) . '>
        <form action="profilo.php#review'. htmlspecialchars($recensione['scarpa_id']).'" method="POST">
        <div class="review-profile">
            <div class="review-icon-shoes">
                <img src="../assets/' . htmlspecialchars($recensione['immagine'])  . '" alt="' . htmlspecialchars($recensione['nome']) . '">
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
                <img src="../assets/close.png" alt="modifica" class="icona-profilo">
            </button>
            <button type="submit" id="elimina" name="elimina" class="link-con-icona" value="' . htmlspecialchars($recensione['scarpa_id']) .'">
                <img src="../assets/close.png" alt="elimina" class="icona-profilo">
            </button>
        </div>   
        </div>
        </form>
        </a>
        </div>
        ';
    }
} else {
    $content .= '<p>Nessuna recensione disponibile.</p>';
}

$HTMLpage = str_replace("{yourreviews}", $content, $HTMLpage);

echo $HTMLpage;
$connection->endDbConnection();
include "footer.php";
}else{
    header('Location: accedi.php');
    die();
}
?>