<?php

    session_start();

    $db_host = 'localhost';
    $db_user = 'your_db_user';
    $db_pass = 'your_db_password';
    $db_name = 'your_db_name';

    $con = mysqli_connect($server_name, $db_username, $db_password, $db_name);

    if(!$con) {
        die("Failed to connect");
    }

?>