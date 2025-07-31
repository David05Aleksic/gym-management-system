<?php

    require_once "config.php";
    require_once "fpdf/fpdf.php";

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

        $photo_path = htmlspecialchars($_POST['photo_path']);

        $training_plan_id = intval($_POST['training_plan_id']);
        $trainer_id = 0;
        $access_card_pdf_path = "";

        $sql = "INSERT INTO `members`
        (`first_name`, `last_name`, `email`, `phone_number`, `photo_path`, `training_plan_id`, `trainer_id`, `access_card_pdf_path`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $execute = $con -> prepare($sql);
        $execute->bind_param("sssssiis", $first_name, $last_name, $email, $phone_number, $photo_path, $training_plan_id, $trainer_id, $access_card_pdf_path);
        $execute->execute();

        $member_id = $con -> insert_id;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(40, 10, 'Access Card'); 
        $pdf->Ln();
        $pdf->Cell(40, 10,'Member ID: ' . $member_id); 
        $pdf->Ln();
        $pdf->Cell(40, 10,'Name: ' . $first_name . " " . $last_name);
        $pdf->Ln();
        $pdf->Cell(40, 10,'Email: ' . $email);
        $pdf->Ln();
        
        $filename = 'access_cards/access_card_' . $member_id . '.pdf'; 
        $pdf->Output('F', $filename);

        $sql = "UPDATE members SET access_card_pdf_path = '$filename' WHERE member_id = '$member_id'";
        $con->query($sql);
        $con->close();

        $_SESSION['success_message'] = 'Member is successfully registered!';
        header("location: admin_dashboard.php");
        exit();
    }
?>
