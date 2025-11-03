<?php
include("db.php");
if(isset($_POST['branch_id'])){
    $branchID = $_POST['branch_id'];

    $sql = "SELECT id,name,lastname FROM teacher WHERE branch_id = $branchID";
    $query = mysqli_query($conn, $sql);

    $dataTeacher = [];
    while($row = mysqli_fetch_assoc($query)){
        $dataTeacher[] = [
            "id" => $row["id"],
            "name" => $row["name"] . ' ' . $row["lastname"]
        ];
    }
    echo json_encode($dataTeacher);
}
?>