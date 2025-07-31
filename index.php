<?php

    require_once "config.php"; 

    if(isset($_SESSION['admin_id'])) {
            header("Location: admin_dashboard.php");
            exit();
        }
    
    if($_SERVER['REQUEST_METHOD'] === "POST") {
        $username = trim($_POST['username']);
        $username = preg_replace("/[^a-zA-Z0-9]/", "", $username);
        $password = $_POST['password'];
        $sql = "SELECT admin_id, password FROM admins WHERE username = ?";

        $execute = $con->prepare($sql); 
        $execute->bind_param("s", $username); 
        $execute->execute();

        $results = $execute->get_result();

        if($results->num_rows == 1) {

            $admin = $results->fetch_assoc(); 
                
            if(password_verify($password, $admin["password"])) {
                $_SESSION['admin_id'] = $admin['admin_id'];

                $con->close();
                header("location: admin_dashboard.php");
            }
            else {
                $_SESSION['error'] = "Incorrect password"; 

                $con->close();
                header("location: index.php");
                exit();
            }
        }
        else {
            $_SESSION['error'] = "Incorrect username"; 

            $con->close();
            header("location: index.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="min-width: 350px;">
            <h3 class="text-center mb-4">Admin Login</h3>

            <?php

                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger text-center">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
            ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="d-grid">
                    <input type="submit" value="Login" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
