<?php
require "db.php";
$errors = [];
$old_data = [];

if (empty($_POST['name'])) {
    $errors['name'] = "name is required";
} else {
    $old_data['name'] = $_POST['name'];
}

if (empty($_POST['email'])) {
    $errors['email'] = "email is required";
} else {
    $old_data['email'] = $_POST['email'];
}

if (empty($_POST['password'])) {
    $errors['password'] = "Password is required";
}

if (empty($_POST['conpassword'])) {
    $errors['conpassword'] = "conpassword is required";
}

if (empty($_POST['ext'])) {
    $errors['ext'] = "extention is required";
} else {
    $old_data['ext'] = $_POST['ext'];
}

if (empty($_FILES['pic']['tmp_name'])) {
    $errors['pic'] = "Image is required";
} else {
    $ext = pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION);
    if (!in_array($ext, ["jpg", "jpeg", "png"])) {
        $errors['pic'] = "Only JPG, JPEG, PNG files are allowed";
    }
}

if ($errors) {
    $errors = json_encode($errors);
    $url = "Location: index.php?errors={$errors}";
    if ($old_data) {
        $old_data = json_encode($old_data);
        $url .= "&old_data={$old_data}";
    }
    header($url);
} else {

    $image_time=time();
    $temp_name = $_FILES['pic']['tmp_name'];
    $image_name = $_FILES['pic']['name'];
    $image_path = "images/{$image_time}.{$ext}";
    $saved = move_uploaded_file($temp_name, $image_path);


    $obj=new Database;
    $obj->insert($db, "users",array( "name"=>$_POST['name'], "email"=>$_POST['email'], "password"=>$_POST['password'], "roomno"=>$_POST['roomno'], "ext"=>$_POST['ext'], "pic"=>$image_path));
}
