<?php  
    include 'db.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $password = $_POST['password'];
        $department = $_POST['department'];
        $password_verify = $_POST['passwordV_IN'];

        $sqlCheck = "SELECT * FROM teacher WHERE password_match = '$password_verify'";
        $queryCheck = mysqli_query($conn, $sqlCheck);
        if(mysqli_num_rows($queryCheck) > 0){
            echo json_encode([
                "status" => "error",
                "data" => [
                    "name" => $name,
                    "lastname" => $lastname,
                    "password" => $password,
                    "department" => $department,
                    "passwordV_IN" => $password_verify
                ]
            ]);
        } else{
            $sql = "INSERT INTO teacher (name,lastname,password,branch_id,password_match) VALUE ('$name','$lastname','$password','$department','$password_verify')";
            $query = mysqli_query($conn,$sql);

            echo json_encode([
                "status" => "success",
                "data" => [
                    "name" => $name,
                    "lastname" => $lastname,
                    "password" => $password,
                    "department" => $department,
                    "passwordV_IN" => $password_verify
                ]
            ]);
        }
    }
?>