<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<?php
include 'db.php';
$id =  $_POST['id'];
$username = $_POST['usernameAccEdit'];
$password = $_POST['passwordEdit'];
$class = $_POST['classEdit'];
$room = $_POST['roomEdit'];
$branch = $_POST['branchEdit'];
$status = $_POST['statusEdit'];

if(empty($password)){
    $sql = "UPDATE user SET username = '$username',class = '$class', room = '$room', branch_id = '$branch', status = '$status' WHERE id = $id";
    $query = mysqli_query($conn,$sql);
}else{
    $sql = "UPDATE user SET username = '$username', password = '$password', class = '$class', room = '$room', branch_id = '$branch', status = '$status' WHERE id = $id";
    $query = mysqli_query($conn,$sql);
}

if($query){
        echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="../account.php";
            })
        </script>';
    }
?>