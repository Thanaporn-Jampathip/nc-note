<?php
include 'db.php';
if(isset($_POST['branch_id'])){
    $branchID = (int)$_POST['branch_id'];

    $sqlTeacher = "SELECT id,name,lastname FROM teacher WHERE branch_id = $branchID";
    $queryTeacher = mysqli_query($conn,$sqlTeacher);
    $teacher = [];
    while($row = mysqli_fetch_assoc($queryTeacher)){
            $teacher[] = [
                "id" => $row['id'],
                "name" => $row["name"] . ' ' . $row["lastname"]
            ];
        }

        echo json_encode($teacher);
}
?>