<?php
include 'db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['fullname'];
    $password = $_POST['password'];

    $sql = "INSERT INTO staff (username,password) VALUES ('$username','$password')";
    $query = mysqli_query($conn,$sql);
    
    echo json_encode([
    "data" => [
        "fullname" => $username,
        "password" => $password
        ]
    ]);
}
?>