<?php
include("db.php");
if(isset($_POST['branch_id'])){
    $branchID = (int)$_POST['branch_id'];

    $sql = "SELECT id,name,lastname FROM teacher WHERE branch_id = $branchID";
    $query = mysqli_query($conn, $sql);

    $data = [];
    while($row = mysqli_fetch_assoc($query)){
        $data[] = [
            "id" => $row["id"],
            "name" => $row["name"] . ' ' . $row["lastname"]
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>