<?php
include 'db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nameAcc = $_POST['nameAcc'];
    $class = $_POST['class'];
    $branch = $_POST['branch'];
    $room = $_POST['room'];
    $password = $_POST['password'];
    $statusIN = $_POST['statusIN'];
    $sql = "INSERT INTO user (username,password,class,room,branch_id,status) VALUES ('$nameAcc','$password','$class','$room','$branch','$statusIN')";
    $query = mysqli_query($conn,$sql);
    
    echo json_encode([
        "data" => [
            "nameAcc" => $nameAcc,
            "class" => $class,
            "branch" => $branch,
            "room" => $room,
            "password" => $password,
            "statusIN" => $statusIN
            ]
        ]);
}
?>