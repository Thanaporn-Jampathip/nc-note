<?php
session_start();
include 'backend/db.php';
$userid = $_SESSION['userid'];
$user = $_SESSION['user'];
if (!isset($_SESSION['user'])) {
    header('location: login_user.php');
}

// fetch subject from userID
$sqlSubject = "
SELECT s.id, s.subID as subjectID , s.name as subjectName, CONCAT(t.name, ' ' ,t.lastname) AS teacherName
FROM subject s
JOIN teacher t ON s.teacher_id = t.id
WHERE s.userID = '$userid'
";
$querySubject = mysqli_query($conn,$sqlSubject);

$sqlTeacher = "SELECT teacher.id, CONCAT(teacher.name, ' ' ,teacher.lastname) AS teacher FROM teacher";
$queryTeacher = mysqli_query($conn, $sqlTeacher);

//แปลงเป็นไทย ปี
$year = date('Y');
function Years($year)
{
    return (string) ($year + 543);
}
//ภาคเเรียน
$term = date('n');
if ($term >= 5 && $term <= 9) {
    $term = 1;
} elseif ($term >= 10) {
    $term = 2;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>บันทึกการเรียน / สอน - รายวิชา</title>
    <style>
        h5 a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include("component/navbar.php") ?>
    <div class="d-flex">
        <?php include("component/sidebar_user.php") ?>
        <div class="container-fluid m-2 p-4 border rounded-3" style="height: auto;">
            <h5>รายวิชา</h5>
            <hr>
            <div class="d-flex justify-content-between">
                <p>รายวิชาทั้งหมด<br>
                    ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year) ?></p>

                <div class="d-flex ms-auto">
                    <div>
                        <div class="mb-3 d-flex ms-auto">
                            <button type="button" name="add" class="btn btn-success px-4" data-bs-toggle="modal"
                                data-bs-target="#addFormModal"><i class="bi bi-plus-circle"></i></button>
                        </div>
                    </div>
                </div>
                <div>
                </div>
                <!-- MODAL ADD -->
                <div class="modal fade" id="addFormModal" tabindex="-1" aria-labelledby="addFormLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Form -->
                            <form action="" method="post" id="subjectFormInsert">
                                <input type="hidden" name="userID" value="<?php echo $userid ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">เพิ่มรายวิชา</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="">รหัสวิชา</label>
                                        <input type="text" class="form-control" placeholder="กรอกรหัสวิชา" name="subjectID"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">ชื่อวิชา</label>
                                        <input type="text" class="form-control" placeholder="กรอกชื่อวิชา" name="subjectName"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="text-center">
                                                <h5 style="color: gray">---- เลือกครูผู้สอน ----</h5>
                                            </div>
                                            <div class="col">
                                                <label for="">เลือกสาขา</label>
                                                <select name="" id="branchIN_Teacher" class="form-select">
                                                    <option value="" selected disabled>เลือกสาขา</option>
                                                    <?php
                                                    $sqlB = "SELECT id,name FROM branch";
                                                    $queryB = mysqli_query($conn, $sqlB);
                                                    while ($row = mysqli_fetch_array($queryB)) {
                                                        ?>
                                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="">ครูผู้สอนรายวิชา</label><br>
                                                <select name="teacherID" id="teacherIN_Teacher"
                                                    class="form-select teacherIN_Teacher">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="addSubject" class="btn btn-success w-100" value="เพิ่ม">เพิ่ม</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="text-center">รายวิชาในเทอมนี้</h6><br>
            <table class="table table-bordered table-sm" width="auto">
                <tr class="table table-info">
                    <th>รหัสวิชา</th>
                    <th>ชื่อวิชา</th>
                    <th>ครูผู้สอน</th>
                    <th></th>
                </tr>
                <tr>
                    <?php
                        while ($row = mysqli_fetch_array($querySubject)) { ?>

                            <td><?php echo $row['subjectID'] ?></td>
                            <td><?php echo $row['subjectName'] ?></td>
                            <td><?php echo $row['teacherName'] ?></td>
                            <td class="d-flex justify-content-around">
                                <div>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editFormModal<?php echo $row['id'] ?>">แก้ไข</button>
                                </div>
                                <!-- MODAL EDTI -->
                                <div class="modal fade" id="editFormModal<?php echo $row['id'] ?>" tabindex="-1"
                                    aria-labelledby="addFormLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- FORM EDIT -->
                                            <form action="./backend/editSubject.php" method="post" id="formSubjectEdit">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editFormModal">แก้ไขรายวิชา</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" value="<?php echo $row['id'] ?>" name="id">
                                                    <div class="mb-3">
                                                        <label for="">รหัสวิชา</label>
                                                        <input type="text" name="subjectId" class="form-control"
                                                            value="<?php echo $row['subjectID'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="">ชื่อวิชา</label>
                                                        <input type="text" name="subjectName" class="form-control"
                                                            value="<?php echo $row['subjectName'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <div class="text-center">
                                                                <h5 style="color: gray">---- เลือกครูผู้สอน ----</h5>
                                                            </div>
                                                            <div class="col">
                                                                <label for="">เลือกสาขา</label>
                                                                <select name="" id="" class="form-select branchEdit_Teacher">
                                                                    <option value="" selected disabled>เลือกสาขา</option>
                                                                    <?php
                                                                    $sqlB = "SELECT * FROM branch";
                                                                    $queryB = mysqli_query($conn, $sqlB);
                                                                    while ($rowB = mysqli_fetch_array($queryB)) {
                                                                        ?>
                                                                        <option value="<?php echo $rowB['id'] ?>">
                                                                            <?php echo $rowB['name'] ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col">
                                                                <label for="">ครูผู้สอนรายวิชา</label><br>
                                                                <select name="teacher" id=""
                                                                    class="form-select teacherEdit_Teacher">

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-warning w-100">แก้ไข</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <form action="" method="post">
                                        <input type="hidden" value="<?php echo $row['id'] ?>" name="subjectID">
                                        <button class="btn btn-sm btn-danger" value="ลบ" type="submit"
                                            name="deleteSubject">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
            </table>
        </div>
    </div>
</body>

</html>
<!-- javascript here -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // fillter ครูจากสาขา form insert
    $(document).on('change', '#branchIN_Teacher', function () {
        let branchIN_Teacher = $(this).val();
        loadTeachers(branchIN_Teacher, $('#teacherIN_Teacher'));
    });

    // fillter ครูจากสาขา form edit
    $(document).on('change', '.branchEdit_Teacher', function () {
        const modal = $(this).closest('.modal');
        const branchID = $(this).val();
        const teacher = modal.find('.teacherEdit_Teacher');

        loadTeachers(branchID, teacher);
    });

    // ฟังก์ชันโหลดครู
    function loadTeachers(branchID, $teacher) {
        if (!branchID) return;
        $.ajax({
            url: "./backend/loadTeacherSubjectPage.php",
            type: "POST",
            data: { branch_id: branchID },
            dataType: "json",
            success: function (dataTeacher) {
                $teacher.empty().append('<option value="" selected disabled>-- เลือกครู --</option>');
                $.each(dataTeacher, function (index, teacher) {
                    $teacher.append(
                        '<option value="' + teacher.id + '">' + teacher.name + '</option>'
                    );
                });
            },
        });
    }


    // fillter ห้องจากการเลือกสาขา form insert
    $(document).on('change', '#branch', function () {
        let branchID_Insert = $(this).val();
        loadUser(branchID_Insert, $('#user'));
    });

    // fillter ห้องจากการเลือกสาขา form edit
    $(document).on('change', '.branchEdit', function () {
        const modal = $(this).closest('.modal');
        const branchID = $(this).val();
        const user = modal.find('.userEdit');

        loadUser(branchID, user);
    });

    function loadUser(branchID, $user) {
        if (!branchID) return;
        $.ajax({
            url: "./backend/loadUserSubjectPage.php",
            type: "POST",
            data: { branch_id: branchID },
            dataType: "json",
            success: function (data) {
                $user.empty().append('<option value="" selected disabled>-- เลือกห้อง --</option>');
                $.each(data, function (index, user) {
                    $user.append(
                        '<option value="' + user.id + '">' + user.username + '</option>'
                    );
                });
            },
        });
    }
</script>
<!-- PHP here -->
<?php include './backend/action_subject.php' ?>
<?php
if (isset($_POST['deleteSubject'])) {
    $id = $_POST['subjectId'];

    $sql = "DELETE FROM subject WHERE id = $id";
    $query = mysqli_query($conn, $sql);
}
?>