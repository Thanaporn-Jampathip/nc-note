<?php  
    include 'db.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $subjectId = $_POST['subjectId'];
        $subjectName = $_POST['subjectName'];
        $teacher = $_POST['teacher'];
        $user = $_POST['user'];

        $sql = "INSERT INTO subject (subID,name,teacher_id,userID) VALUE ('$subjectId','$subjectName','$teacher','$user')";
        $query = mysqli_query($conn,$sql);

        echo json_encode([
        "data" => [
            "subjectId" => $subjectId,
            "subjectName" => $subjectName,
            "teacher" => $teacher,
            "user" => $user
            ]
        ]);
    }
?>