<?php
include("db.php");

if(isset($_POST['branch_id'])){
    $branchID_Edit = $_POST['branch_id'];

    $sql = "SELECT id,name,lastname FROM user WHERE branch_id = $branchID_Edit ";
    $query = mysqli_query($conn, $sql);

    $dataEdit = [];
    while($row = mysqli_fetch_assoc($query)){
        $dataEdit[] = [
            "id" => $row["id"],
            "name" => $row["name"] . ' ' . $row["lastname"]
        ];
    }
    echo json_encode($dataEdit);
}
?>