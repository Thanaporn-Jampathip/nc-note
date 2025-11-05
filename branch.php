<?php
session_start();
include 'backend/db.php';

$sqlBranch = "SELECT branch.id, branch.name, CONCAT(teacher.name, ' ' ,teacher.lastname) AS teacherName 
              FROM branch 
              JOIN teacher ON branch.teacher_id = teacher.id";
$queryBranch = mysqli_query($conn, $sqlBranch);

// ตรวจจาก session ของคนที่ล็อกอิน
$LocalUser = false;
$allowedUsers = [
    'ธนภรณ์ จำปาทิพย์',
    'สิริพรชัย ศักดาประเสริฐ',
    'พันทิพา พานิชสุโข'
];
$allowedPasswords = [
    '347336yt',
    '111',
    '222'
];

if (isset($_SESSION['user']['username'], $_SESSION['user']['password'])) {
    if (in_array($_SESSION['user']['username'], $allowedUsers) && in_array($_SESSION['user']['password'], $allowedPasswords)) {
        $LocalUser = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเรียน / สอน - จัดการสาขา</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <?php include 'component/navbar.php'; ?>

    <div class="d-flex">
        <?php 
        if ($usertype == 'staff') {
            include './component/sidebar.php';
        } else {
            include './component/sidebar_user.php';
        }
        ?>

        <div class="container-fluid m-2 p-4 border rounded-3" style="width: 100%">
            <h5>จัดการสาขา</h5>
            <hr>

            <!-- Add Branch Button -->
            <div class="d-flex mb-3">
                <p>รายชื่อสาขา และหัวหน้าแต่ละสาขา</p>
                <?php if($LocalUser) { ?>
                <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                    เพิ่ม
                </button>
                <?php } ?>
            </div>

            <!-- MODAL ADD BRANCH -->
            <div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="formAddBranch" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title">เพิ่มบัญชีห้อง</h5>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>ชื่อสาขา</label>
                                    <input type="text" id="name" class="form-control" placeholder="ใส่ชื่อสาขา" required>
                                </div>
                                <div class="mb-3">
                                    <label style="font-size: 14pt; color: gray;">----------------------- หัวหน้าสาขา -----------------------</label>
                                    <div class="row">
                                        <div class="col">
                                            <label>สาขา</label>
                                            <select id="branchAdd" class="form-select">
                                                <option value="" selected disabled>--- เลือกสาขา ---</option>
                                                <?php while ($rowBranch = mysqli_fetch_array($queryBranch)) { ?>
                                                    <option value="<?php echo $rowBranch['id'] ?>"><?php echo $rowBranch['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>รายชื่อครู</label>
                                            <select id="headTeacher" class="form-select">
                                                <option value="" selected disabled>--- กรุณาเลือกสาขาก่อน ---</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success w-100">เพิ่มสาขา</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <h6 class="text-center">สาขาทั้งหมด</h6>
            <br>
            <table class="table table-bordered mt-2 table-sm">
                <thead class="table-info">
                    <tr>
                        <th>ลำดับ</th>
                        <th>ชื่อสาขา</th>
                        <th>หัวหน้าสาขา</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT branch.id, branch.name, CONCAT(teacher.name, ' ', teacher.lastname) AS teacherName 
                            FROM branch 
                            JOIN teacher ON branch.teacher_id = teacher.id";
                    $query = mysqli_query($conn, $sql);

                    while ($row = mysqli_fetch_array($query)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['teacherName']; ?></td>
                            <td class="d-flex justify-content-around">
                                <!-- EDIT BUTTON -->
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editBranchModal<?php echo $row['id']; ?>">แก้ไข</button>

                                <!-- MODAL EDIT -->
                                <div class="modal fade" id="editBranchModal<?php echo $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="backend/editBranch.php" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">แก้ไขข้อมูลสาขา</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>ชื่อสาขา</label>
                                                        <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label style="font-size: 14pt; color: gray;">----------------------- หัวหน้าสาขา -----------------------</label>
                                                        <div class="row">
                                                            <div class="col">
                                                                <label>สาขา</label>
                                                                <select class="form-select branchEdit">
                                                                    <option value="" selected disabled>--- เลือกสาขา ---</option>
                                                                    <?php
                                                                    mysqli_data_seek($queryBranch, 0);
                                                                    while ($rowBranch = mysqli_fetch_array($queryBranch)) { ?>
                                                                        <option value="<?php echo $rowBranch['id']; ?>"><?php echo $rowBranch['name']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col">
                                                                <label>รายชื่อครู</label>
                                                                <select name="headTeacherEdit" class="form-select headTeacherEdit">
                                                                    <option value="" selected disabled>--- กรุณาเลือกสาขาก่อน ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" name="edit" value="<?php echo $row['id']; ?>" class="btn btn-warning w-100">แก้ไขข้อมูล</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- DELETE BUTTON -->
                                <?php if($LocalUser) { ?>
                                    <form method="post" class="ms-2">
                                        <button type="submit" name="delete" value="<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">ลบ</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ADD BRANCH AJAX
        $(document).on("submit", '#formAddBranch', function (e) {
            e.preventDefault();
            let name = $('#name').val()
            let teacher = $('#headTeacher').val()
            let formData = new FormData();
            formData.append("name", name)
            formData.append("teacher", teacher)

            $.ajax({
                url: "./backend/insertBranch.php",
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
                        $('#addBranchModal').modal('hide');
                        location.reload();
                    });
                }
            })
        })

        $(document).on('change', '#branchAdd', function () {
            let branchID_Insert = $(this).val();
            loadTeachers(branchID_Insert, $('#headTeacher'));
        });

        $(document).on('change', '.branchEdit', function () {
            const modal = $(this).closest('.modal');
            const branchID = $(this).val();
            const teacherSelect = modal.find('.headTeacherEdit');
            loadTeachers(branchID, teacherSelect);
        });

        function loadTeachers(branchID, $teacherSelect) {
            if (!branchID) return;
            $.ajax({
                url: "./backend/selectTeacherPageBranch.php",
                type: "POST",
                data: { branch_id: branchID },
                dataType: "json",
                success: function (data) {
                    $teacherSelect.empty().append('<option value="" selected disabled>--- เลือกครู ---</option>');
                    $.each(data, function (index, teacher) {
                        $teacherSelect.append('<option value="' + teacher.id + '">' + teacher.name + '</option>');
                    });
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        }
    </script>

<?php
// DELETE BRANCH
if (isset($_POST['delete'])) {
    $id = (int)$_POST['delete'];
    $sqlDelete = "DELETE FROM branch WHERE id = $id";
    $queryDelete = mysqli_query($conn, $sqlDelete);

    if ($queryDelete) {
        echo '<script>
            Swal.fire({
                title: "ลบสำเร็จ",
                icon: "success",
                timer: 1000,
                didOpen: () => Swal.showLoading()
            }).then(() => { window.location.href="branch.php"; });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                title: "ลบไม่สำเร็จ",
                icon: "error",
                timer: 1000,
                didOpen: () => Swal.showLoading()
            }).then(() => { window.location.href="branch.php"; });
        </script>';
    }
}
?>
