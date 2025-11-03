<?php
include 'db.php';
if(isset($_POST['branch_id'])){
    $branchID = $_POST['branch_id'];
    $sql = "SELECT user.username, user.id FROM user WHERE branch_id = $branchID AND status != 'internship'";
    $query = mysqli_query($conn, $sql);

    $data = [];
    while($row = mysqli_fetch_array($query)){
        $data[] = [
            "id" => $row["id"],
            "username" => $row["username"]
        ];
    }
    echo json_encode($data);
}

?>