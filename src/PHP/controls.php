<?php

use Conn\DbConnection;

require_once('dbconnection.php');

    function sanitizeInput($input) {
        
        $input = strip_tags($input);
        $input = htmlentities($input, ENT_QUOTES, 'UTF-8'); 
        $input = stripslashes($input);
        $input = preg_replace('/\s+/', ' ', $input);
        $input = trim($input); 

        return $input;
    }
?>