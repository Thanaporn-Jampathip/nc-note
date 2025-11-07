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
    $name = $_POST['nameEdit'];
    $lastname = $_POST['lastnameEdit'];
    $branch = $_POST['branch'];
    $password = $_POST['passwordEdit'];
    $id = $_POST['teacherID'];
    $password_verify = $_POST['passwordV_Edit'];

    $sqlCheck = "SELECT * FROM teacher WHERE password_match = '$password_verify' AND id != $id";
    $queryCheck = mysqli_query($conn, $sqlCheck);
    if(mysqli_num_rows($queryCheck) > 0){
        echo '<script>
            Swal.fire({
            title: "รหัสยืนยันนี้ถูกใช้แล้ว",
            icon: "error",
            timer: 1500,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="../teacher.php";
            })
        </script>';
    } else{
    $sql = "UPDATE teacher SET name = '$name', lastname = '$lastname', branch_id = '$branch', password = '$password', password_match = '$password_verify' WHERE id = $id";
    $query = mysqli_query($conn,$sql);

    if($query){
        echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            timer: 1500,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="../teacher.php";
            })
        </script>';
    }}
?>