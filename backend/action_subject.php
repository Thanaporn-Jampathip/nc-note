<?php
// add subject
if (isset($_POST['addSubject'])) {
    $subjectId = $_POST['subjectID'];
    $subjectName = $_POST['subjectName'];
    $teacherID = $_POST['teacherID'];
    $userID = $_POST['userID'];

    $sql = "INSERT INTO subject (subID,name,userID,teacher_id) VALUE ('$subjectId','$subjectName','$userID','$teacherID')";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        echo '<script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "เพิ่มรายวิชาสำเร็จ",
            showConfirmButton: false,
            timer: 1500,
            scrollbarPadding: false
            }).then(() =>{
                window.location.href="subject.php"
            })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            position: "center",
            icon: "error",
            title: "เกิดข้อผิดพลาด",
            showConfirmButton: false,
            timer: 1500,
            scrollbarPadding: false
            }).then(() =>{
                window.location.href="subject.php"
            })
        </script>';
    }
}

// delete subject
if (isset($_POST['deleteSubject'])) {
    $subjectID = $_POST['subjectID'];

    $sqlDelete = "DELETE FROM subject WHERE id = '$subjectID'";
    $queryDelete = mysqli_query($conn, $sqlDelete);

    if ($queryDelete) {
        echo '<script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "ลบวิชาสำเร็จ",
            showConfirmButton: false,
            timer: 1500,
            scrollbarPadding: false
            }).then(() =>{
                window.location.href="subject.php"
            })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            position: "center",
            icon: "error",
            title: "เกิดข้อผิดพลาด",
            showConfirmButton: false,
            timer: 1500,
            scrollbarPadding: false
            }).then(() =>{
                window.location.href="subject.php"
            })
        </script>';
    }
}
?>