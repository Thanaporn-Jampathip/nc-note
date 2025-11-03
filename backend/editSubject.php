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
$id = $_POST['id'];
$subjectID = $_POST['subjectId'];
$subjectName = $_POST['subjectName'];

if(isset($_POST['teacher'])){
    $teacherID = $_POST['teacher'];
}
if(isset($_POST['userEdit'])){
    $userID = $_POST['userEdit'];
}

if(empty($teacherID) && empty($userID)){
    $sql = "UPDATE subject SET subID = '$subjectID', name = '$subjectName' WHERE id = $id";
}
elseif(!empty($teacherID) && empty($userID)){
    $sql = "UPDATE subject SET subID = '$subjectID', name = '$subjectName', teacher_id = '$teacherID' WHERE id = $id";
}
elseif(empty($teacherID) && !empty($userID)){
    $sql = "UPDATE subject SET subID = '$subjectID', name = '$subjectName', userID = '$userID' WHERE id = $id";
}else{
    $sql = "UPDATE subject SET subID = '$subjectID', name = '$subjectName', userID = '$userID', teacher_id = '$teacherID' WHERE id = $id";
}
$query = mysqli_query($conn,$sql);

if($query){
    echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="../subject.php";
            })
        </script>';
        }else{
            echo '<script>
            Swal.fire({
            title: "แก้ไขไม่สำเร็จ",
            icon: "error",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="../subject.php";
            })
        </script>';
        }
?>