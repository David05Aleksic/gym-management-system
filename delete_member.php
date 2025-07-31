<?php

    require_once "config.php";
    
    if($_SERVER['REQUEST_METHOD'] === "POST") {
        $member_id = intval($_POST['member_id']);

        $sql = "DELETE FROM members WHERE member_id = ?";
        $execute = $con->prepare($sql);
        $execute->bind_param("i", $member_id);
        $message = "";

        //i izvrsava ga i proverava da li je izvrseno
        if($execute->execute()) {
            $message = "Member deleted";
        } else {
            $message = "Member is not deleted";
        }

        $_SESSION['success_message'] = $message;
        header('location: admin_dashboard.php');
        exit();
    }

?>