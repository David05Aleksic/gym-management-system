<?php

    require_once "config.php";
    
    if($_SERVER['REQUEST_METHOD'] === "POST") {
        $first_name = trim($_POST['first_name']);
        $first_name = preg_replace("/[^a-zA-ZšđčćžŠĐČĆŽ\s'-]/u", "", $first_name);

        $last_name = trim($_POST['last_name']);
        $last_name = preg_replace("/[^a-zA-ZšđčćžŠĐČĆŽ\s'-]/u", "", $last_name);

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email address";
            header("location: admin_dashboard.php");
            exit();
        }

        $phone_number = trim($_POST['phone_number']);
        $phone_number = preg_replace("/[^0-9+]/", "", $phone_number);

        $sql = "INSERT INTO trainers (first_name, last_name, email, phone_number)
        VALUES (?, ?, ?, ?)";
        $execute = $con->prepare($sql);
        $execute->bind_param("ssss", $first_name, $last_name, $email, $phone_number);
        $execute->execute();

        $_SESSION['success_message'] = "Trainer successfully added!";
        header("location: admin_dashboard.php");
        exit();
    }

?>