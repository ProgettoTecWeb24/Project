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

$HTMLpage = file_get_contents('../HTML/adminModificaLista.html');

if($connection->isAdmin($_SESSION["username"])){
    $lista_scarpe = "";
    $info = "";

    if(isset($_POST['submit'])){
        $idScarpa = intval($_POST['delete_id']);
        $shoe = $connection->getShoe($idScarpa);
        if($connection->deleteShoe($idScarpa)){
            $info = '<p class="success_text" id="info" role="alert">La scarpa '.$shoe["nome"].' è stata eliminata con successo</p>';
        }else{
            $info = '<p class="error_text" id="info" role="alert">Errore: eliminazione non possibile, la scarpa non esiste</p>';
        }
    }

    $lista_scarpe = "";
    $all_shoes = $connection->getAllShoes();

    $lista_scarpe .= '
        <h2>Ecco tutte le scarpe presenti in database</h2>
        <div class="table-wrapper-admin">
            <table class="table-admin-list">
                <thead>
                    <tr>
                        <th>Immagine</th>
                        <th>Nome</th>
                        <th class="hide-tablet">Marca</th>
                        <th class="hide-tablet">Descrizione</th>
                        <th class="hide-mobile">Tipo</th>
                        <th class="hide-mobile">Feedback</th>
                        <th></th>
                    </tr>
                </thead>
                    <tbody>
        ';
    foreach($all_shoes as $shoe){
        $lista_scarpe .= '
                    <tr>
                        <td><img src="../assets/'.$shoe["immagine"] .'" alt="immagine della scarpa '.$shoe["nome"] .'" class="scarpa-admin"></td>
                        <td>'.$shoe["nome"] .'</td>
                        <td class="hide-tablet">'.$shoe["marca"] .'</td>
                        <td class="hide-tablet">'.$shoe["descrizione"] .'</td>
                        <td class="hide-mobile">'.$shoe["tipo"] .'</td>
                        <td class="hide-mobile">'.
                            $shoe["feedback"].'</br>'.
                            $shoe["votoexp"].'
                        </td>
                        <td>
                            <a class="link-con-icona" name="modifica" href="adminModificaScarpa.php?mod=' . urlencode($shoe['id']) . '"><img src="../assets/edit.svg" alt="modifica" class="icona"></a>
                            <form action="adminModificaLista.php" class="form" method="POST">
                                <input type="hidden" name="delete_id" value="'.$shoe['id'].'">
                                <button type="submit" id="submit" name="submit" class="link-con-icona">
                                    <img src="../assets/delete.svg" alt="elimina" class="icona">
                                </button>
                            </form>
                        </td>
                    </tr>
            '; 
        }
         $lista_scarpe .= '
                    </tbody>
                </table>
            </div>
            '; 

    $HTMLpage = str_replace("{lista_scarpe}", $lista_scarpe, $HTMLpage);
    $HTMLpage = str_replace("{info}", $info, $HTMLpage);
    echo $HTMLpage;

}else{
    header("Location: ../HTML/error404.html");
}


?>