<?php
    session_start();
    include './backend/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - บันทึกการสอน</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai+Looped:wght@100;200;300;400;500;600;700&family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        body {
            font-family: "Prompt", sans-serif;
            background: linear-gradient(to bottom right, #0457f1ff, #A8EFFF);
            max-height: 100%;
            height: 100vh;
            color: white;
        }
        .head{
            text-align: center;
        }img{
            max-width: 100px;
            width: auto;
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            color: black;
        }

        .card-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-body {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container ">
        <img src="image/logo_nvc.png" alt="วิทยาลัยอาชีวศึกษานครปฐม" class="rounded mx-auto d-block my-3">
        <h5 class="head">วิทยาลัยอาชีวศึกษานครปฐม</h5>
        <h5 class="head">ภาคเรียนที่ 1 ปีการศึกษา 2568</h5>
        <h5 class="head">ระบบบันทึกการเรียน / การสอน</h5>

        <!-- PHP in action -->
        <form action="" method="post" class="d-flex justify-content-center">
            <div class="card mt-3 shadow-lg">
                <div class="card-header bg-primary">
                    <h5 class="head text-light">เข้าสู่ระบบ</h5>
                </div>
                <div class="card-body px-5 py-3">
                    <p class="text-center text-danger">สำหรับเจ้าหน้าที่ / ผู้บริหาร</p>
                    <div class="mb-3">
                        <label for="" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" name="usernameStaff" class="form-control"  required>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">รหัสผ่าน</label>
                        <input type="password" name="passwordStaff" class="form-control" required>
                    </div>

                    <input class="btn btn-primary w-100 my-2" type="submit" value="เข้าสู่ระบบ" name="login_staff"></input><br>
                    <a href="login_user.php" class="text-success">สำหรับผู้ใช้</a><br>
                    <a href="เอกสารคู่มือการใช้เว็บบันทึกการเรียนการสอน.pdf">คู่มือการใช้งานเว็บ (สำหรับเจ้าหน้าที่)</a>

                </div>
            </div>
        </form>
    </div>
</body>
</html>

<?php
if (isset($_POST['login_staff'])) {
    $username = $_POST['usernameStaff'];
    $password = $_POST['passwordStaff'];

    $stmt = $conn->prepare("SELECT * FROM staff WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;
        $_SESSION['userid'] = $user['id'];
        $_SESSION['user_type'] = 'staff';

        echo '<script>
            Swal.fire({
                title: "เข้าสู่ระบบสำเร็จ",
                icon: "success",
                confirmButtonText: "ปิด",
                timer: 1000,
                didOpen: () => Swal.showLoading()
            }).then(() => {
                window.location.href = "index.php";
            });
        </script>';

    } else {
        echo '<script>
            Swal.fire({
                title: "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง",
                icon: "error",
                confirmButtonText: "ปิด",
                timer: 1000,
                didOpen: () => Swal.showLoading()
            }).then(() => {
                window.location.href = "login_staff.php";
            });
        </script>';
    }
}

?>