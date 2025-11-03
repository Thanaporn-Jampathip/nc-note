<?php
// เลือกสาขาก่อนผู้ใช้ หัวข้อ สถิติแต่ละห้อง / สาขา
include 'db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $branchID = $_POST['branchID'];

    $sql = "SELECT id,username FROM user WHERE branch_id = $branchID AND status != 'internship'";
    $query = mysqli_query($conn,$sql);
    $users = [];
    while($row = mysqli_fetch_assoc($query)){
        $users[] = [
            "id" => $row['id'],
            "username" => $row['username']
        ];
    }

    echo json_encode($users);
}
?>