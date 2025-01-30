<?php
$title = "Chi siamo - CorsaIdeale";
$description = "CorsaIdeale: scopri consigli e recensioni dettagliate sulle migliori scarpe da corsa. Trova il modello perfetto grazie all'aiuto dei nostri esperti."; // 148 caratteri
$keywords = "corsa,scarpe,recensioni,running,trail,jogging,nike,adidas,asics"; // 63 caratteri
require_once('dbconnection.php');
session_start();

use Conn\DbConnection;
setlocale(LC_ALL, 'it_IT');

$connection = new DbConnection();
include "header.php";
$HTMLpage = file_get_contents('HTML/chisiamo.html');

echo $HTMLpage;
include "footer.php";
?>