<?php
$usertype = $_SESSION['user_type'];
$userid = $_SESSION['userid'];
if($usertype === 'staff'){
    $sqlNav = "SELECT * FROM staff WHERE id = $userid";
} elseif ($usertype === 'user'){
    $sqlNav = "SELECT * FROM user WHERE id = $userid";
}
$queryNav = mysqli_query($conn,$sqlNav);
$rowNAV = mysqli_fetch_array($queryNav);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css"/>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai+Looped:wght@100;200;300;400;500;600;700&family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            position: relative;
            font-family: "Prompt", sans-serif;
        }
        .link{
            padding-bottom: 4px;
        }
        .link a{
            text-decoration: none;
            padding: 6px;
            color: #007BFF;
            width: 100%;
            display: block;
            transition: 0.3s;
        }
        .link a:hover{
            padding: 6px;
            background-color: #7bbbff;
            color: white;
            border-radius: 5px;
        }
        .link a.active{
            padding: 6px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
        }
        .link a.active:hover{
            background-color: 007BFF;
            color: white;
        }
        .sidebar {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 220px;
            max-width: 300px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            padding: 1rem;
        }
        footer {
            text-align: center;
            font-size: 0.75rem;
            padding: 0.75rem 0;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        @media only screen and (max-width: 576px) {
            .sidebar {
                display: none;
                position: fixed;
                z-index: 1000;
                background-color: white;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar pt-4 px-2 shadow" id="sidebarUser">
        <div class="user d-block d-sm-none text-center fs-5">
            <p><?php echo $rowNAV['username'] ?></p>
        </div>
        <!-- MENU LINKS -->
        <div class="link">
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <ion-icon name="home-outline"></ion-icon>
                หน้าหลัก
            </a>
        </div>
        <div class="link">
            <a href="note.php" class="<?= basename($_SERVER['PHP_SELF']) == 'note.php' ? 'active' : '' ?>">
                <ion-icon name="file-tray-stacked-outline"></ion-icon>
                บันทึกการเรียน
            </a>
        </div>
        <div class="link">
            <a href="note_history.php" class="<?= basename($_SERVER['PHP_SELF']) == 'note_history.php' ? 'active' : '' ?>">
                <ion-icon name="person-outline"></ion-icon>
                ประวัติการบันทึก
            </a>
        </div>
        <div class="link mb-3">
            <a href="subject.php" class="<?= basename($_SERVER['PHP_SELF']) == 'subject.php' ? 'active' : '' ?>">
                <i class="bi bi-card-list"></i>
                รายวิชา
            </a>
        </div>
        <div>
            <form action="./backend/logout.php" method="post">
                <input class="btn btn-danger w-100 btn-sm d-block d-sm-none" type="submit" name="logout" value="ออกจากระบบ"></input>
            </form>
        </div>
        <footer>
            <hr class="my-1 w-100">
            <p class="mb-0">© 2568<br>
                <strong>ผู้พัฒนา:</strong> นางสาวธนภรณ์ จำปาทิพย์<br>
                <strong>ที่ปรึกษา:</strong> นายสิริพรชัย ศักดาประเสริฐ
            </p>
        </footer>
    </div>
</body>
</html>
<!-- JAVA SCRIPT -->
