<?php
include './backend/db.php';

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
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai+Looped:wght@100;200;300;400;500;600;700&family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    *{
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
        background-color: #7bbbff;
        color: white;
        border-radius: 5px;
    }
    .link a.active{
        background-color: #007BFF;
        color: white;
        border-radius: 5px;
    }
    .sidebar {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 220px;
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
</style>
<body>
    <div class="sidebar pt-4 px-2 shadow" id="sidebar">
        <div class="link">
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <ion-icon name="home-outline"></ion-icon> หน้าหลัก
            </a>
        </div>

        <div class="link">
            <a href="branch.php" class="<?= basename($_SERVER['PHP_SELF']) == 'branch.php' ? 'active' : '' ?>">
                <ion-icon name="bag-outline"></ion-icon> จัดการสาขา
            </a>
        </div>

        <div class="link">
            <a href="teacher.php" class="<?= basename($_SERVER['PHP_SELF']) == 'teacher.php' ? 'active' : '' ?>">
                <ion-icon name="person-outline"></ion-icon> ครู
            </a>
        </div>
        <!-- <div class="link">
            <a href="subject.php" class="<?= basename($_SERVER['PHP_SELF']) == 'subject.php' ? 'active' : '' ?>">
                <ion-icon name="newspaper-outline"></ion-icon> รายวิชา
            </a>
        </div> -->
        <div class="link">
            <a href="account.php" class="<?= basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : '' ?>">
                <ion-icon name="people-outline"></ion-icon> บัญชีห้องเรียน
            </a>
        </div>
        <div class="link">
            <a href="note.php" class="<?= basename($_SERVER['PHP_SELF']) == 'note.php' ? 'active' : '' ?>">
                <ion-icon name="file-tray-stacked-outline"></ion-icon> บันทึกการเรียน
            </a>
        </div>
        <div class="link">
            <a href="staff.php" class="<?= basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : '' ?>">
                <ion-icon name="person-outline"></ion-icon> บุคลากร
            </a>
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
