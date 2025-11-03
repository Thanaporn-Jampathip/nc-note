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
if(isset($_POST['user'])){
    $username = $_POST['user'];

    $sql = "SELECT id,username FROM user WHERE username = '$username'";
    $query = mysqli_query($conn,$sql);
    if(mysqli_num_rows($query) == 1){
        while($row = mysqli_fetch_array($query)){
            $id = $row['id'];
        }
        echo '<script>window.location.href="../subject.php?id=' . $id . '";</script>';
    }
    else{
        echo '<script>
            Swal.fire({
            title: "ไม่พบชื่อผู้ใช้นี้",
            icon: "error",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="../subject.php";
            })
        </script>';
    }
}

?>