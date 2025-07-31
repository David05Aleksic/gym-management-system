<?php

    require_once 'config.php';

    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $member_id = intval($_POST['member']);
        $trainer_id = intval($_POST['trainer']);

        $sql = "UPDATE members SET trainer_id = ? WHERE member_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $trainer_id, $member_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Trainer is successfully assigned to Member!";
        } else {
            $_SESSION['success_message'] = "Failed to assign trainer: " . $stmt->error;
        }

        header("Location: admin_dashboard.php");
        exit();
}
