<?php



require_once('dbconnection.php');
session_start();

$location = 'Lista scarpe';
use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
if (!$connection->startDbConnection()) {
    die("Connessione al database fallita.");
}



include "header.php";
$HTMLpage = file_get_contents('../HTML/lista.html');

$query = "SELECT * FROM scarpa WHERE nome LIKE '%'";


if(!empty($_POST['nomescarpa'])){
    $query = "SELECT * FROM scarpa WHERE nome LIKE '%" . $_POST['nomescarpa'] . "%' ";
    $HTMLpage = str_replace('value=""', 'value="' . $_POST['nomescarpa'] . '" selected', $HTMLpage);

}

if (!empty($_POST['marca']) AND $_POST['marca'] != 'all') {
    $query = $query . "AND marca = '" . $_POST['marca']. "' ";
    $HTMLpage = str_replace('value="' . $_POST['marca'] . '"', 'value="' . $_POST['marca'] . '" selected', $HTMLpage);

}

if (!empty($_POST['tipo']) AND $_POST['tipo'] != 'all') {
    $query = $query . "AND tipo = '" . $_POST['tipo']. "' ";
    $HTMLpage = str_replace('value="' . $_POST['tipo'] . '"', 'value="' . $_POST['tipo'] . '" selected', $HTMLpage);
}

if (!empty($_POST['ordina']) AND $_POST['ordina'] != 'ordStand') {
    $HTMLpage = str_replace('value="' . $_POST['ordina'] . '"', 'value="' . $_POST['ordina'] . '" selected', $HTMLpage);
    if($_POST['ordina'] == "nomeCres"){
        $query = $query . "ORDER BY nome ASC ";
    }elseif($_POST['ordina'] == "nomeDesc"){
        $query = $query . "ORDER BY nome DESC ";
    }elseif($_POST['ordina'] == "votoCres"){
        $query = $query . "ORDER BY votoexp ASC ";
    }elseif($_POST['ordina'] == "votoDesc"){
        $query = $query . "ORDER BY votoexp DESC ";
    }
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

//echo $query;



$result = $connection->query($query);

$cardsHTML = "";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cardsHTML .= '
            <a name ="' . htmlspecialchars($row['id']) . '" href="paginaSingola.php?id=' . urlencode($row['id']) . '" class="card-link">
                <div class="card">
                <div class="img-card-container">
                    <img class="img-card" src="../assets/' . htmlspecialchars($row['immagine'])  . '" alt="' . htmlspecialchars($row['nome']) . '" />
                </div>
                    <div class="text-card">
                        <h3>' . htmlspecialchars($row['nome']) . '</h3>
                        <p class="marca">Marca: ' . htmlspecialchars($row['marca']) . '</p>
                        <p class="modello">Modello: ' . htmlspecialchars($row['nome']) . '</p>
                        <p class="feedback">Feedback: ' . htmlspecialchars($row['feedback']) . '</p>
                        <form action="lista.php#'. htmlspecialchars($row['id']).'" method="POST">
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

$HTMLpage = str_replace("{shoes}", $cardsHTML, $HTMLpage);

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

echo $HTMLpage;

$connection->endDbConnection();

include "footer.php";
?>