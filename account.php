<?php
session_start();
include './backend/db.php';
$user = $_SESSION['user'];
if (!isset($_SESSION['user'])) {
    header('location: login_user.php');
}
// Page
$perPage = 10; // จำนวนรายการต่อหน้า
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // หน้าเริ่มต้น
$start = ($page - 1) * $perPage;

$sqlData = "SELECT user.id,user.username,user.password,user.class,user.room,user.status,user.branch_id , branch.name AS branchName FROM user JOIN branch ON user.branch_id = branch.id ORDER by user.id ASC LIMIT $start, $perPage ";
$queryData = mysqli_query($conn, $sqlData);

$sqlB = "SELECT * FROM branch";
$queryB = mysqli_query($conn, $sqlB);
//แปลงเป็นไทย ปี
$year = date('Y');
function Years($year) {
    return (string)($year + 543);
}
//ภาคเรียน
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
    <title>บันทึกการเรียน / สอน - บัญชีห้องเรียน</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

    </style>
</head>

<body>
    <?php include("component/navbar.php") ?>
    <div class="d-flex">
        <?php include("component/sidebar.php") ?>
        <div class="container-fluid m-2 p-4 border rounded-3" style="height: auto;">
            <h5>บัญชีห้องเรียน</h6>
                <hr>
                <div class="d-flex justify-content-between">
                    <p>รายชื่อครูที่สามารถเข้าใช้ได้<br>
                        ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year) ?></p>

                    <div class="d-flex align-items-start">
                        <button type="button" name="add" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addFormModal">เพิ่ม</button>
                    </div>

                    <div class="modal fade" id="addFormModal" tabindex="-1" aria-labelledby="addFormLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="" method="post" id="insertFormAcc">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addFormModal">เพิ่มบัญชีห้อง</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="mb-3">
                                                    <label for="">ชื่อบัญชีห้อง</label>
                                                    <input type="text" name="username" class="form-control"
                                                        id="nameAccIn" placeholder="กรอกชื่อบัญชีห้อง" required>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-3">
                                                    <label for="">ระดับชั้น</label>
                                                    <select name="" id="classIn" class="form-select">
                                                        <option value="ปวช.1">ปวช.1</option>
                                                        <option value="ปวช.2">ปวช.2</option>
                                                        <option value="ปวช.3">ปวช.3</option>
                                                        <option value="ปวส.1">ปวส.1</option>
                                                        <option value="ปวส.2">ปวส.2</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="mb-3">
                                                    <label for="">ห้อง</label>
                                                    <select name="" id="roomIn" class="form-select">
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">สถานะ</label>
                                            <select name="" id="statusIN" class="form-select">
                                                <option value="normally" selected>ปกติ</option>
                                                <option value="internship">ฝึกงาน</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">สาขา</label>
                                            <select name="" id="branchIn" class="form-select">
                                                <?php
                                                while ($branch = mysqli_fetch_array($queryB)) {
                                                    ?>
                                                    <option value="<?php echo $branch['id'] ?>">
                                                        <?php echo $branch['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">รหัสผ่านบัญชี</label>
                                            <input type="password" name="password" class="form-control" id="passwordIn"
                                                placeholder="กรอกรหัสผ่าน" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100" value="เพิ่ม">เพิ่ม</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="text-center">บัญชีห้องเรียนทั้งหมด</h6><br>

                <table class="table table-bordered table-sm " width="auto">
                    <tr class=" table table-info">
                        <th>ลำดับ</th>
                        <th>ชื่อบัญชีห้อง</th>
                        <th>รหัสผ่าน</th>
                        <th>สาขา</th>
                        <th>ระดับชั้น</th>
                        <th>ห้อง</th>
                        <th>สถานะ</th>
                        <th></th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($queryData)) {
                        ?>
                        <tr>
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo $row['username'] ?></td>
                            <td><?php echo $row['password'] ?></td>
                            <td><?php echo $row['branchName'] ?></td>
                            <td><?php echo $row['class'] ?></td>
                            <td><?php echo $row['room'] ?></td>
                            <td><?php
                            switch ($row['status']) {
                                case 'normally':
                                    echo '<span class="text-success">ปกติ</span>';
                                    break;
                                case 'internship':
                                    echo '<span class="text-danger">ฝึกงาน</span>';
                                    break;
                            }
                            ?></td>
                            <td class="d-flex justify-content-around">
                                <div>
                                    <button class="btn btn-sm btn-warning " data-bs-toggle="modal"
                                        data-bs-target="#editFormModal<?php echo $row['id'] ?>">แก้ไข</button>
                                </div>
                                <!-- MODAL EDIT -->
                                <div class="modal fade" id="editFormModal<?php echo $row['id'] ?>" tabindex="-1"
                                    aria-labelledby="addFormLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- FORM EDIT -->
                                            <form action="./backend/editAcc.php" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editFormModal">แก้ไขบัญชีห้อง</h5>

                                                </div>
                                                <div class="modal-body">

                                                    <input type="hidden" value="<?php echo $row['id'] ?>" name="id">
                                                    <div class="row">
                                                        <div class="col-7">
                                                            <div class="mb-3">
                                                                <label for="">ชื่อบัญชีห้อง</label>
                                                                <input type="text" name="usernameAccEdit"
                                                                    class="form-control"
                                                                    value="<?php echo $row['username'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="mb-3">
                                                                <label for="">ระดับชั้น</label>
                                                                <select name="classEdit" id="classIn" class="form-select">
                                                                    <option value="ปวช.1" <?php if ($row['class'] === 'ปวช.1')
                                                                        echo 'selected'; ?>>ปวช.1</option>
                                                                    <option value="ปวช.2" <?php if ($row['class'] === 'ปวช.2')
                                                                        echo 'selected'; ?>>ปวช.2</option>
                                                                    <option value="ปวช.3" <?php if ($row['class'] === 'ปวช.3')
                                                                        echo 'selected'; ?>>ปวช.3</option>
                                                                    <option value="ปวส.1" <?php if ($row['class'] === 'ปวส.1')
                                                                        echo 'selected'; ?>>ปวส.1</option>
                                                                    <option value="ปวส.2" <?php if ($row['class'] === 'ปวส.2')
                                                                        echo 'selected'; ?>>ปวส.2</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="mb-3">
                                                                <label for="">ห้อง</label>
                                                                <select name="roomEdit" id="roomIn" class="form-select">
                                                                    <option value="1" <?php if ($row['room'] == '1')
                                                                        echo 'selected'; ?>>1</option>
                                                                    <option value="2" <?php if ($row['room'] == '2')
                                                                        echo 'selected'; ?>>2</option>
                                                                    <option value="3" <?php if ($row['room'] == '3')
                                                                        echo 'selected'; ?>>3</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="">สาขา</label>
                                                        <select name="branchEdit" id="" class="form-select">
                                                            <?php mysqli_data_seek($queryB, 0);
                                                            while ($branchShow = mysqli_fetch_array($queryB)) {
                                                                ?>
                                                                <option value="<?php echo $branchShow['id'] ?>" <?php if ($row['branch_id'] == $branchShow['id'])
                                                                       echo 'selected'; ?>>
                                                                    <?php echo $branchShow['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="">สถานะ</label>
                                                        <select name="statusEdit" id="statusEdit" class="form-select">
                                                            <option value="normally" <?php if ($row['status'] == 'normally')
                                                                echo 'selected'; ?>>ปกติ</option>
                                                            <option value="internship" <?php if ($row['status'] == 'internship')
                                                                echo 'selected'; ?>>ฝึกงาน
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="">รหัสผ่านบัญชี</label>
                                                        <input type="password" name="passwordEdit" class="form-control"
                                                            placeholder="ใส่รหัสใหม่ (ถ้าต้องการเปลี่ยน)">
                                                    </div>
                                                    <button type="submit" class="btn btn-warning w-100">แก้ไข</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <form action="" method="post">
                                        <input type="hidden" value="<?php echo $row['id'] ?>" name="id">
                                        <button class="btn btn-sm btn-danger " value="ลบ" type="submit">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <?php
                $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM user");
                $totalRow = mysqli_fetch_assoc($totalQuery);
                $totalPages = ceil($totalRow['total'] / $perPage);
                // แสดงลิงก์
                echo "<div class='mt-3'>";
                for ($i = 1; $i <= $totalPages; $i++) {
                    $isActive = ($i == $page) ? 'btn-secondary active text-white' : 'btn-primary';
                    echo "<a href='?page=$i' class='btn btn-sm mx-1 $isActive'>$i</a>";
                }
                echo "</div>";
                ?>
        </div>
    </div>
</body>

</html>
<!-- javascript here -->
<script src="assets/js/wanting.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).on("submit", '#insertFormAcc', function (e) {
        e.preventDefault();
        let nameAcc = $('#nameAccIn').val();
        let classIn = $('#classIn').val();
        let branch = $('#branchIn').val();
        let room = $('#roomIn').val();
        let password = $('#passwordIn').val();
        let statusIN = $('#statusIN').val();

        let formData = new FormData();
        formData.append("nameAcc", nameAcc);
        formData.append("class", classIn);
        formData.append("branch", branch);
        formData.append("room", room);
        formData.append("password", password);
        formData.append("statusIN", statusIN);

        $.ajax({
            url: './backend/insertAccount.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (alertS) {
                Swal.fire({
                    title: "เพิ่มสำเร็จ",
                    icon: "success",
                    confirmButtonText: "ปิด",
                    draggable: true
                }).then(() => {
                    $('#insertFormAcc').modal('hide');
                    location.reload();
                })
            }
        })
    })
</script>
<!-- PHP here -->
<?php
if ($_POST) {
    $id = $_POST['id'];

    $sql = "DELETE FROM user WHERE id = $id";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        echo '<script>
            Swal.fire({
            title: "ลบสำเร็จ",
            icon: "success",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="account.php";
            })
        </script>';
    } else {
        echo '<script>
            Swal.fire({
            title: "ลบไม่สำเร็จ",
            icon: "error",
            confirmButtonText: "ปิด",
            draggable: true
            }).then(() =>{
                window.location.href="account.php";
            })
        </script>';
    }
}
?>