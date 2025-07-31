<?php

    require_once "config.php";

    if(!isset($_SESSION['admin_id'])) {
        header("location: index.php");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />


</head>
<body>

    <?php if(isset($_SESSION['success_message'])) : ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
            echo $_SESSION['success_message']; 
            unset($_SESSION['success_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>

    <?php endif; ?>

    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <h2>Members List</h2>

                <a href="export.php?what=members" class="btn btn-success btn-sm">Export</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Trainer</th>
                            <th>Photo</th>
                            <th>Training Plan</th>
                            <th>Access Card</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            $sql = "SELECT members.*, 
                            training_plans.name AS training_plan_name,
                            CONCAT(trainers.first_name, ' ', trainers.last_name) AS trainer_name
                            FROM members 
                            LEFT JOIN training_plans ON members.training_plan_id = training_plans.plan_id
                            LEFT JOIN trainers ON members.trainer_id = trainers.trainer_id;";

                            $execute = $con->query($sql);

                            $results = $execute->fetch_all(MYSQLI_ASSOC);
                            $select_members = $results;
                            $select_trainers = $results;
                            
                            foreach($results as $result) : ?>

                                <tr>
                                    <td> <?php echo htmlspecialchars($result['first_name']); ?> </td>
                                    <td> <?php echo htmlspecialchars($result['last_name']); ?> </td>
                                    <td> <?php echo htmlspecialchars($result['email']); ?> </td>
                                    <td> <?php echo htmlspecialchars($result['phone_number']); ?> </td>
                                    <td> <?php
                                    if($result['trainer_name']) {
                                        echo htmlspecialchars($result['trainer_name']);
                                    } else {
                                        echo "No trainer";
                                    }
                                    ?> </td>
                                    <td><img style="width: 60px" src="<?php echo htmlspecialchars($result['photo_path']); ?>"></td>
                                    <td> <?php 
                                    if($result['training_plan_name']) {
                                        echo htmlspecialchars($result['training_plan_name']);
                                    } else {
                                        echo "No plan";
                                    }
                                    ?> </td>
                                    <td><a target="_blank" href="<?php echo htmlspecialchars($result['access_card_pdf_path']); ?>">Access Card</a></td>
                                    <td> <?php
                                        $created_at = strtotime($result['created_at']);
                                        $new_date = date("F, jS Y", $created_at); // F-mesec, j-dan S-nastavak Y-godina
                                        echo $new_date; 
                                     ?> </td>
                                    <td>
                                            
                                        <form action="delete_member.php" method="POST">
                                            <input type="hidden" name="member_id" value="<?php echo $result['member_id']; ?>">
                                            <button>DELETE</button>
                                        </form>
                                
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <div class="col-md-12">
                <h2>Trainers List</h2>

                <a href="export.php?what=trainers" class="btn btn-success btn-sm">Export</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            $sql = "SELECT * FROM trainers";
                            $execute = $con->prepare($sql);
                            $execute->execute();
                            
                            $result = $execute->get_result();
                            $results = $result->fetch_all(MYSQLI_ASSOC);

                            foreach($results as $result) : ?>

                            <td> <?php echo htmlspecialchars($result['first_name']); ?> </td>
                            <td> <?php echo htmlspecialchars($result['last_name']); ?> </td>
                            <td> <?php echo htmlspecialchars($result['email']); ?> </td>
                            <td> <?php echo htmlspecialchars($result['phone_number']); ?> </td>
                            <td> <?php echo date("F, jS Y", strtotime($result['created_at'])); ?> </td>

                            <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <h2>Register Member</h2>
                <form action="register_member.php" method="post" enctype="multipart/form-data">
                    First Name: <input type="text" class="form-control" name="first_name"><br>
                    Last Name: <input type="text" class="form-control" name="last_name"><br>
                    Email: <input type="email" class="form-control" name="email"><br>
                    Phone Number: <input type="text" class="form-control" name="phone_number"><br>
                    Training Plan:
                    <select name="training_plan_id" class="form-control">
                        <option value="" disabled selected>Training Plan</option>
                        <?php
                            $sql = "SELECT * FROM training_plans";
                            $execute = $con->query($sql);
                            $results = $execute->fetch_all(MYSQLI_ASSOC); //fetch_all se koristi kada treba vise podataka iz asocijativnog niza da insertujemo

                            foreach($results as $result) {

                                echo "<option value='". $result['plan_id'] ."'>" . $result['name'] . "</option>";
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
                    First Name: <input type="text" class="form-control" name="first_name"> <br>
                    Last Name: <input type="text" class="form-control" name="last_name"> <br>
                    Email: <input type="email" class="form-control" name="email"> <br>
                    Phone Number: <input type="text" class="form-control" name="phone_number"> <br>
                    <input type="submit" class="btn btn-primary" value="Register Trainer">
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h2>Assign Trainer to Member</h2>
                <form action="assign_trainer.php" method="POST">
                    <label for="">Select Member</label>
                    <select name="member" class="form-select">
                        <?php
                            foreach($select_members as $member) : ?>
                                <option value="<?php echo $member['member_id']; ?>">
                                    <?php echo htmlspecialchars($member['first_name']) . " " . htmlspecialchars($member['last_name']); ?>
                                </option>
                            <?php endforeach; 
                        ?>
                    </select> <br>
                    <label for="">Select Trainer</label>
                    <select name="trainer" class="form-select">
                        <?php
                            foreach($select_trainers as $trainer) : ?>
                                <option value="<?php echo $trainer['trainer_id']; ?>">
                                    <?php echo htmlspecialchars($trainer['first_name']) . " " . htmlspecialchars($trainer['last_name']); ?>
                                </option>
                            <?php endforeach; 
                        ?>
                    </select> <br>

                    <button type="submit" class="btn btn-primary">Assign Trainer</button>
                </form>
            </div>
        </div>
    </div>

    <?php $con->close(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <script>
        Dropzone.options.dropzoneUpload = {
            url: "upload_photo.php",
            paramName: "photo",
            maxFilesize: 20, //MB
            acceptedFiles: "image/*",
            init: function () {
                this.on("success", function (file, response) {
                    // Parse the JSON response
                    const jsonResponse = JSON.parse(response);
                    // Check if the file was uploaded successfully
                    if (jsonResponse.success) {
                        // Set the hidden input's value to the uploaded file's path
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