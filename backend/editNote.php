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
$id = $_POST['recordID'];
$subjectID = $_POST['editSubID'];
$subjectName = $_POST['editSubName'];
$start = $_POST['editStartID'];
$end = $_POST['editEndID'];
$week = $_POST['weeks'];
$miss = $_POST['miss'];
$allStudent = $_POST['allStudentEdit'];
$term = $_POST['term'];
$teacher = $_POST['insteadTeacher'] ?? null;
$missStudent = $_POST['missStudentName'] ?? null;

$arr = array_map('trim', explode(',', $missStudent));
$arr = array_filter($arr);
$missStudentName = implode(",\n ", $arr);


if (!empty($teacher) && !empty($missStudentName)) {
    $sql = "UPDATE record SET subject_id = '$subjectID', subject_name = '$subjectName', begin_period = '$start', end_period = '$end', date = NOW(), week = '$week', miss = '$miss', all_student  = '$allStudent', term = '$term', insteadTeacher = $teacher ,missStudentName = '$missStudentName' WHERE id = $id";
} elseif (!empty($teacher) && empty($missStudentName)) {
    $sql = "UPDATE record SET subject_id = '$subjectID', subject_name = '$subjectName', begin_period = '$start', end_period = '$end', date = NOW(), week = '$week', miss = '$miss', all_student  = '$allStudent', term = '$term', insteadTeacher = $teacher WHERE id = $id";
} elseif (empty($teacher) && !empty($missStudentName)) {
    $sql = "UPDATE record SET subject_id = '$subjectID', subject_name = '$subjectName', begin_period = '$start', end_period = '$end', date = NOW(), week = '$week', miss = '$miss', all_student  = '$allStudent', term = '$term', missStudentName = '$missStudentName' WHERE id = $id";
} else {
    $sql = "UPDATE record SET subject_id = '$subjectID', subject_name = '$subjectName', begin_period = '$start', end_period = '$end', date = NOW(), week = '$week', miss = '$miss', all_student  = '$allStudent', term = '$term'
    WHERE id = $id";
}

$query = mysqli_query($conn, $sql);

if ($query) {
    echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            timer: 1500,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="../note.php";
            })
        </script>';
} else {
    echo '<script>
            Swal.fire({
            title: "แก้ไขไม่สำเร็จ",
            icon: "error",
            timer: 1500,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="../note.php";
            })
        </script>';
}
?>