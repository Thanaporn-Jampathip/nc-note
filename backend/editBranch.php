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
if(isset($_POST['edit'])){
    $id = $_POST['edit'];
    $name = $_POST['name'] ?? null;
    $headTeacher = $_POST['headTeacherEdit'] ?? null;

    if(!empty($name) && !empty($headTeacher ))
        $sqlEdit = "UPDATE branch SET name = '$name', teacher_id = '$headTeacher' WHERE id = $id";
    elseif(!empty($name) && empty($headTeacher)){
        $sqlEdit = "UPDATE branch SET name = '$name' WHERE id = $id";
    }elseif(empty($name) && !empty($headTeacher)){
        $sqlEdit = "UPDATE branch SET teacher_id = '$headTeacher' WHERE id = $id";
    }
    $queryEdit = mysqli_query($conn,$sqlEdit);
    if($queryEdit){
        echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="../branch.php";
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
                window.location.href="../branch.php";
            })
        </script>';
        }
}

?>