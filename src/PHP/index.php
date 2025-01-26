<?php
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;

setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
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

include "header.php";
$HTMLpage = file_get_contents('../HTML/index.html');

$query = "SELECT * FROM SCARPA ORDER BY data_aggiunta DESC LIMIT 4";
$result = $connection->query($query);

$cardsHTML = "";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cardsHTML .= '
            <a name ="' . htmlspecialchars($row['id']) . '" href="paginaSingola.php?id=' . urlencode($row['id']) . '" class="card-link">
                <div class="card">
                <div class="img-card-container">
                    <img class="img-card" src="../assets/' . htmlspecialchars($row['immagine'])  . '" alt="immagine della scarpa ' . htmlspecialchars($row['marca']) . ' ' . htmlspecialchars($row['nome']) . '" />
                </div>
                    <div class="text-card">
                        <h3>' . htmlspecialchars($row['marca']) . ' ' . htmlspecialchars($row['nome']) . '</h3>
                        <p class="feedback">Feedback: ' . htmlspecialchars($row['feedback']) . '</p>
                        <p class="tipo">Tipo: ' . htmlspecialchars($row['tipo']) . '</p>

                        <form action="index.php#'. htmlspecialchars($row['id']).'" method="POST">
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

if (!empty($_SESSION['username'])) {
    $qFill = "SELECT * FROM likes WHERE username = '" . $_SESSION['username'] . "'"; 
    $result = $result = $connection->query($qFill);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $oldStr = 'value="' . htmlspecialchars($row['scarpa_id']) .'">{like}';
            $newStr = 'value="' . htmlspecialchars($row['scarpa_id']) .'"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon-filled">';
            $cardsHTML = str_replace($oldStr, $newStr, $cardsHTML);   
        }
    }
}
$cardsHTML = str_replace("{like}", '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon">', $cardsHTML);

$HTMLpage = str_replace("{last_released_shoes}", $cardsHTML, $HTMLpage);

echo $HTMLpage;
$connection->endDbConnection();
include "footer.php";
?>
