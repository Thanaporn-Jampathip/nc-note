<?php
include('db.php');
if(isset($_GET['searchDataBranch'])){
    $branchID = $_GET['branch'];
    echo $branchID;

    $sql = "SELECT id,username FROM user WHERE branch_id = $branchID AND status != 'internship'";
    $query = mysqli_query($conn,$sql);
    
    $dataBranch = [];
    while($row = mysqli_fetch_assoc($query)){
        $dataBranch[] = [
            "id" => $row['id'],
            "username" => $row['username']
        ];
    }
    header('Location: ../index.php?id=' . $branchID);
    exit;
}
?>