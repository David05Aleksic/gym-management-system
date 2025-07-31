<?php 

    $photo = $_FILES['photo']; // ovaj naziv photo je onaj sto pise u js

    $photo_name = basename($photo['name']); // samo daje ime slike a ne putanju do slike

    $photo_path = 'member_photos/' . $photo_name; // cuvamo sliku korisnika u folder

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    $ext = pathinfo($photo_name, PATHINFO_EXTENSION); // ako se ova ekstenzija nalazi u nizu dozvoljena je

    if(in_array($ext, $allowed_ext) && $photo['size'] < 2000000) {
        move_uploaded_file($photo['tmp_name'], $photo_path);

        echo json_encode(['success' => true, 'photo_path' => $photo_path]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid file']);    
    }

?>