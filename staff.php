<?php
session_start();
include './backend/db.php';
$user = $_SESSION['user'];
$userid = $_SESSION['userid'];

if (!isset($_SESSION['user'])) {
    header('location: login_staff.php');
}

$localUser = false;
if (isset($_SESSION['user']['username'])) {
    if ($_SESSION['user']['username'] === 'ธนภรณ์ จำปาทิพย์' || $_SESSION['user']['username'] === 'รองฝ่ายวิชาการ' || $_SESSION['user']['username'] === 'หัวหน้างานหลักสูตร') {
        $localUser = true;
    }
}

$sql = "SELECT id,username,password FROM staff WHERE username != 'ธนภรณ์ จำปาทิพย์'";
$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเรียน / สอน - บุคลากร</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include("component/navbar.php") ?>
    <div class="d-flex">
        <?php include("component/sidebar.php") ?>
        <div class="container-fluid m-2 p-4 border rounded-3" style="height: auto;">
            <h5>บุคลากร</h5>
            <hr>
            <div class="d-flex">
                <div>
                    <p class="mb-0">รายชื่อครูที่สามารถเข้าใช้งานได้</p>
                    <p class="mb-0">ภาคเรียนที่ 1 ปีการศึกษา 2568</p>
                </div>
                <!-- BUTTON ADD -->
                <?php if ($localUser) { ?>
                    <button class="btn btn-success ms-auto align-self-start" data-bs-toggle="modal"
                        data-bs-target="#addStaff" type="button">เพิ่ม</button>
                <?php } ?>
                <!-- MODAL ADD STAFF -->
                <div class="modal fade" id="addStaff" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="" method="" id="formAddStaff">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">เพิ่มบุคลากร</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>ชื่อ-สกุล</label>
                                        <input type="text" class="form-control" id="fullname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>รหัสผ่าน</label>
                                        <input type="password" class="form-control" id="password" required>
                                    </div>
                                    <button class="btn btn-success w-100" type="submit">เพิ่ม</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <h6 class="text-center">รายชื่อบุคลากรทั้งหมด</h6><br>
            <form action="" method="post">
                <table class="table table-bordered table-sm " width="auto">
                    <tr class=" table table-info">
                        <th>ลำดับ</th>
                        <th>ชื่อ-สกุล</th>
                        <?php
                        if (!$localUser) { ?>
                            <th>รหัสผ่าน</th>
                            <th></th>
                        <?php } ?>

                        <?php if ($localUser) { ?>
                            <th>รหัสผ่าน</th>
                            <th></th>
                        <?php } ?>
                    </tr>
                    <?php while ($row = mysqli_fetch_array($query)) { ?>
                        <tr>
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo $row['username'] ?></td>

                            <?php if (!$localUser && $row['id'] == $userid) { ?>
                                <td><?php echo $row['password'] ?></td>
                                <td>
                                    <!-- BUTTON EDIT USER LOCAL -->
                                    <button class="btn btn-warning btn-sm w-100" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editUserLocal<?php echo $row['id']; ?>">แก้ไข</button>
                                    <!-- MODAL EDIT USER LOCAL -->
                                    <div class="modal fade" id="editUserLocal<?php echo $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="" method="post">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">แก้ไขข้อมูลบุคลากร</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="userid" value="<?php echo $row['id'] ?>">
                                                        <div class="mb-3">
                                                            <label>ชื่อ-สกุล</label>
                                                            <input type="text" name="nameEditUserLocal" class="form-control"
                                                                value="<?php echo $row['username']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>รหัสผ่าน</label>
                                                            <input type="password" name="passwordEditUserLocal"
                                                                class="form-control" value="<?php echo $row['password'] ?>"
                                                                required>
                                                        </div>
                                                        <button type="submit" name="editUserLocal"
                                                            class="btn btn-warning w-100">แก้ไขข้อมูล</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            <?php } ?>

                            <?php if ($localUser) { ?>
                                <td><?php echo $row['password'] ?></td>
                                <td class="d-flex">
                                    <!-- BUTTON EDIT -->
                                    <button class="btn btn-warning btn-sm w-100 me-2" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editStaffModal<?php echo $row['id']; ?>">แก้ไข</button>
                                    <!-- MODAL EDIT -->
                                    <div class="modal fade" id="editStaffModal<?php echo $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="" method="post">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">แก้ไขข้อมูลบุคลากร</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="userid" value="<?php echo $row['id'] ?>">
                                                        <div class="mb-3">
                                                            <label>ชื่อ-สกุล</label>
                                                            <input type="text" name="nameEditUserLocal" class="form-control"
                                                                value="<?php echo $row['username']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>รหัสผ่าน</label>
                                                            <input type="password" name="passwordEditUserLocal"
                                                                class="form-control" value="<?php echo $row['password'] ?>"
                                                                required>
                                                        </div>
                                                        <button type="submit" name="editUserLocal"
                                                            class="btn btn-warning w-100">แก้ไขข้อมูล</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($localUser && $row['username'] != 'หัวหน้างานหลักสูตร' && $row['username'] != 'รองฝ่ายวิชาการ') { ?>
                                        <button class="btn btn-danger btn-sm w-100" type="submit" name="delete"
                                            value="<?php echo $row['id'] ?>">ลบ</button>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </table>
            </form>
        </div>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    $(document).on("submit", '#formAddStaff', function (e) {
        e.preventDefault();
        let fullname = $('#fullname').val();
        let password = $('#password').val();

        let formData = new FormData();
        formData.append("fullname", fullname);
        formData.append("password", password);

        $.ajax({
            url: "backend/insertStaff.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (alertS) {
                Swal.fire({
                    title: "เพิ่มสำเร็จ",
                    icon: "success",
                    timer: 1000,
                    didOpen: () => Swal.showLoading()
                }).then(() => {
                    $('#addStaff').modal('hide');
                    location.reload();
                })
            }
        })
    })
</script>
<?php
if (isset($_POST['edit'])) {
    $id = $_POST['userid'];
    $username = $_POST['nameEdit'];
    $password = $_POST['passwordEdit'];

    $sql = "UPDATE staff SET username = '$username', password = '$password' WHERE id = $id";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="staff.php";
            })
        </script>';
    } else {
        echo '<script>
            Swal.fire({
            title: "แก้ไขไม่สำเร็จ",
            icon: "error",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="staff.php";
            })
        </script>';
    }
} elseif (isset($_POST['editUserLocal'])) {
    $id = $_POST['userid'];
    $username = $_POST['nameEditUserLocal'];
    $password = $_POST['passwordEditUserLocal'];

    $sql = "UPDATE staff SET username = '$username', password = '$password' WHERE id = $id";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        echo '<script>
            Swal.fire({
            title: "แก้ไขสำเร็จ",
            icon: "success",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="staff.php";
            })
        </script>';
    } else {
        echo '<script>
            Swal.fire({
            title: "แก้ไขไม่สำเร็จ",
            icon: "error",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="staff.php";
            })
        </script>';
    }
}
if (isset($_POST['delete'])) {
    $id = $_POST['delete'];

    $sql = "DELETE FROM staff WHERE id = $id";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo '<script>
            Swal.fire({
            title: "ลบสำเร็จ",
            icon: "success",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="staff.php";
            })
        </script>';
    } else {
        echo '<script>
            Swal.fire({
            title: "ลบไม่สำเร็จ",
            icon: "error",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="staff.php";
            })
        </script>';
    }
}
?>