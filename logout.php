<?php

    require_once "config.php";

    session_unset();
    session_destroy();
    
    session_start();
    $_SESSION['message'] = "Successfully logged out.";

    header("Location: index.php");
    exit();

?>
