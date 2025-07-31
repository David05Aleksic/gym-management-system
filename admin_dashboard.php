<?php

    require_once "config.php";

    if(!isset($_SESSION['admin_id'])) {
        header("location: index.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];

    $sql = "SELECT username FROM admins WHERE admin_id = ?";
    $stmt_admin = $con->prepare($sql);
    $stmt_admin->bind_param("i", $admin_id);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    $admin_data = $result_admin->fetch_assoc();
    $username = $admin_data['username'] ?? "Unknown";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
</head>
<body>

<?php if(isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link text-white">
                        Logged in as <b> <?= htmlspecialchars($username); ?> </b>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light ms-3" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container mt-4">

    <h2>Members List</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th><th>Last Name</th><th>Email</th><th>Phone Number</th>
                <th>Trainer</th><th>Photo</th><th>Training Plan</th><th>Access Card</th><th>Created At</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $sql = "SELECT m.*, tp.name AS training_plan_name, 
                    CONCAT(t.first_name, ' ', t.last_name) AS trainer_name 
                    FROM members m 
                    LEFT JOIN training_plans tp ON m.training_plan_id = tp.plan_id 
                    LEFT JOIN trainers t ON m.trainer_id = t.trainer_id";
            $result = $con->query($sql);
            $members = $result->fetch_all(MYSQLI_ASSOC);

            foreach($members as $member):
            ?>
            <tr>
                <td><?= htmlspecialchars($member['first_name']) ?></td>
                <td><?= htmlspecialchars($member['last_name']) ?></td>
                <td><?= htmlspecialchars($member['email']) ?></td>
                <td><?= htmlspecialchars($member['phone_number']) ?></td>
                <td><?= $member['trainer_name'] ? htmlspecialchars($member['trainer_name']) : "No trainer" ?></td>
                <td>
                    <?php if(!empty($member['photo_path'])): ?>
                        <img src="<?= htmlspecialchars($member['photo_path']) ?>" style="width: 60px;" alt="Photo" />
                    <?php endif; ?>
                </td>
                <td><?= $member['training_plan_name'] ? htmlspecialchars($member['training_plan_name']) : "No plan" ?></td>
                <td>
                    <?php if(!empty($member['access_card_pdf_path'])): ?>
                        <a href="<?= htmlspecialchars($member['access_card_pdf_path']) ?>" target="_blank">Access Card</a>
                    <?php endif; ?>
                </td>
                <td><?= date("F jS, Y", strtotime($member['created_at'])) ?></td>
                <td>
                    <form action="delete_member.php" method="POST" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="member_id" value="<?= $member['member_id'] ?>">
                        <button class="btn btn-danger btn-sm">DELETE</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Trainers List</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>First Name</th><th>Last Name</th><th>Email</th><th>Phone Number</th><th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM trainers";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $trainers = $result->fetch_all(MYSQLI_ASSOC);

            foreach($trainers as $trainer):
            ?>
            <tr>
                <td><?= htmlspecialchars($trainer['first_name']) ?></td>
                <td><?= htmlspecialchars($trainer['last_name']) ?></td>
                <td><?= htmlspecialchars($trainer['email']) ?></td>
                <td><?= htmlspecialchars($trainer['phone_number']) ?></td>
                <td><?= date("F jS, Y", strtotime($trainer['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row mb-5">
        <div class="col-md-6">
            <h2>Register Member</h2>
            <form action="register_member.php" method="post" enctype="multipart/form-data">
                First Name: <input type="text" class="form-control" name="first_name" required><br>
                Last Name: <input type="text" class="form-control" name="last_name" required><br>
                Email: <input type="email" class="form-control" name="email" required><br>
                Phone Number: <input type="text" class="form-control" name="phone_number" required><br>
                Training Plan:
                <select name="training_plan_id" class="form-control" required>
                    <option value="" disabled selected>Select Training Plan</option>
                    <?php
                    $sql = "SELECT * FROM training_plans";
                    $plans = $con->query($sql)->fetch_all(MYSQLI_ASSOC);
                    foreach($plans as $plan) {
                        echo "<option value='". $plan['plan_id'] ."'>". htmlspecialchars($plan['name']) ."</option>";
                    }
                    ?>
                </select> <br>

                <input type="hidden" name="photo_path" id="photoPathInput">

                <div id="dropzone-upload" class="dropzone"></div>

                <input type="submit" class="btn btn-primary mt-3" value="Register Member">
            </form>
        </div>

        <div class="col-md-6">
            <h2>Register Trainer</h2>
            <form action="register_trainer.php" method="POST">
                First Name: <input type="text" class="form-control" name="first_name" required><br>
                Last Name: <input type="text" class="form-control" name="last_name" required><br>
                Email: <input type="email" class="form-control" name="email" required><br>
                Phone Number: <input type="text" class="form-control" name="phone_number" required><br>
                <input type="submit" class="btn btn-primary" value="Register Trainer">
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2>Assign Trainer to Member</h2>
            <form action="assign_trainer.php" method="POST">
                <label>Select Member</label>
                <select name="member" class="form-select" required>
                    <?php foreach($members as $member): ?>
                        <option value="<?= $member['member_id'] ?>">
                            <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select> <br>

                <label>Select Trainer</label>
                <select name="trainer" class="form-select" required>
                    <?php foreach($trainers as $trainer): ?>
                        <option value="<?= $trainer['trainer_id'] ?>">
                            <?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select> <br>

                <button type="submit" class="btn btn-primary">Assign Trainer</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
Dropzone.options.dropzoneUpload = {
    url: "upload_photo.php",
    paramName: "photo",
    maxFilesize: 20, 
    acceptedFiles: "image/*",
    init: function () {
        this.on("success", function (file, response) {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.success) {
                document.getElementById('photoPathInput').value = jsonResponse.photo_path;
            } else {
                console.error(jsonResponse.error);
            }
        });
    }
};
</script>

</body>
</html>
