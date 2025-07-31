<?php

    require_once 'config.php';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $member_id = intval($_POST['member_id']);
        $trainer_id = intval($_POST['trainer_id']);

        $sql = "UPDATE members SET trainer_id = ? WHERE member_id = ?";

        $execute = $con->prepare($sql);
        $execute->bind_param("ii", $trainer_id, $member_id);

        $execute->execute();

        $_SESSION['success_message'] = "Trainer is successfully assigned to Member!";
        header("location: admin_dashboard.php");
        exit();

    }

?>