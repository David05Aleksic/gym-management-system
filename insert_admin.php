<?php

    require_once "config.php";

    $username = "admin2";
    $password = "admin123";

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
    $execute = $con -> prepare($sql);
    $execute->bind_param("ss", $username, $hashed_password);
    $execute->execute();
?>