<?php

/**
 * un petit script pour seulement verfier l'état de la session 
 * du client **/
    session_start();

    if (isset($_SESSION['role']) ) {
        echo $_SESSION['role'];
    }
    else {
        echo "guest";
    }
?>