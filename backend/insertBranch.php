<?php  
    include 'db.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $name = $_POST['name'];
        $teacherID = $_POST['teacher'];

        $sqlBranch = "INSERT INTO branch (name,teacher_id) VALUES ('$name','$teacherID')";
        $queryBranch = mysqli_query($conn, $sqlBranch);
        echo json_encode([
            "data" => [
                "name" => $name,
                "teacher" => $teacherID
            ]
        ]);
    }
?>