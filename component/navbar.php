<?php
$usertype = $_SESSION['user_type'];
$userid = $_SESSION['userid'];
if ($usertype === 'staff') {
    $sqlNav = "SELECT * FROM staff WHERE id = $userid";
} elseif ($usertype === 'user') {
    $sqlNav = "SELECT * FROM user WHERE id = $userid";
}
$queryNav = mysqli_query($conn, $sqlNav);
$rowNAV = mysqli_fetch_array($queryNav);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai+Looped:wght@100;200;300;400;500;600;700&family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Prompt", sans-serif;
        }
        .navbar{
            box-shadow: 1px 1px 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media only screen and (max-width: 576px) {

            .username,
            .ham_sidebar {
                display: none;

            }

            img {
                margin-left: 1rem;
                width: 15%;
            }

            .navbar-brand {
                display: flex;
                align-items: center;
                color: red;
            }


            .ham_sidebar {
                display: inline-block;
                cursor: pointer;
                margin-left: 4px;
            }

            .bar1,
            .bar2,
            .bar3 {
                width: 1.8rem;
                height: 3px;
                background-color: white;
                margin: 6px 0;
                transition: 0.4s;
            }

            /* Rotate first bar */
            .change .bar1 {
                transform: translate(0, 11px) rotate(-45deg);
            }

            /* Fade out the second bar */
            .change .bar2 {
                opacity: 0;
            }

            /* Rotate last bar */
            .change .bar3 {
                transform: translate(0, -11px) rotate(45deg);
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand w-100 bg-primary px-2" style="background: linear-gradient(to bottom, #0414f1ff, #0396ffff);">

        <div class="ham_sidebar" onclick="hamburger(this)" id="hamburgerIcon">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div>
        <a class="navbar-brand" href="index.php">
            <img src="image/logo_nvc.png" width="5%" class="d-inline-block align-top" alt="วิทยาลัยอาชีวศึกษานครปฐม">
            <span class="text-white fs-6 ms-2">ระบบบันทึกการเรียน / การสอน</span>
        </a>
        <div class="ms-auto">
            <!-- DROPDOWN -->
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">

                        <button class="username btn dropdown-toggle text-white fs-6" data-bs-toggle="dropdown"
                            aria-expanded="false"><?php echo $rowNAV['username'] ?></button>

                        <ul class="dropdown-menu dropdown-menu-light dropdown-menu-end px-2">
                            <li class="mb-2 w-100"><span class="ps-2"><?php echo $rowNAV['username'] ?></span></li>
                            <?php if ($usertype === 'staff') { ?>
                                <li class="text-center">---- เจ้าหน้าที่ ----</li>
                                <hr>
                            <?php } elseif ($usertype === 'user') { ?>
                                <li class="text-center">---- ผู้ใช้งาน ----</li>
                                <hr>
                            <?php } ?>
                            <form action="./backend/logout.php" method="post">
                                <input class="btn btn-danger w-100 btn-sm" type="submit" name="logout"
                                    value="ออกจากระบบ"></input>
                            </form>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</body>

</html>
<!-- JS HERE -->
<script>
    function hamburger(x) {
        x.classList.toggle("change");
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('hamburgerIcon');
        const sidebar = document.getElementById('sidebarUser');

        toggleBtn.addEventListener('click', function () {
            if (sidebar.style.display === 'block') {
                sidebar.style.display = 'none'; // ซ่อน
            } else {
                sidebar.style.display = 'block'; // แสดง
            }
        });
    });

</script>