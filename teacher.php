<?php
session_start();
include './backend/db.php';
$user = $_SESSION['user'];
if(!isset($_SESSION['user'])){
    header('location: login_user.php');
}

$perPage = 10; // จำนวนรายการต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // หน้าเริ่มต้น
$start = ($page - 1) * $perPage;

$sql = "SELECT teacher.id ,teacher.name ,teacher.lastname, teacher.password, teacher.branch_id,teacher.password_match ,branch.name AS branchName FROM teacher JOIN branch ON teacher.branch_id = branch.id ORDER by teacher.id ASC LIMIT $start, $perPage";
$query = mysqli_query($conn,$sql);

$sqlB = "SELECT id,name FROM branch";
$queryB = mysqli_query($conn,$sqlB);
//แปลงเป็นไทย ปี
$year = date('Y');
function Years($year) {
    return (string)($year + 543);
}
//ภาคเรียน
$term = date('n');
if($term >= 5 && $term <= 9){
    $term = 1;
}elseif($term >= 10){
    $term = 2;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเรียน / สอน - ครู</title>
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
        <?php include("component/sidebar.php")?>
        <div class="container-fluid m-2 p-4 border rounded-3"style="height: auto;">
            <h5>ครู</h5>
            <hr>
            <div class="d-flex justify-content-between">
                <p>รายชื่อครูที่สามารถเข้าใช้ได้<br>
                ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year) ?></p>

                <div class="d-flex align-items-start">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFormModal">เพิ่ม</button>
                </div>

                <div class="modal fade" id="addFormModal" tabindex="-1" aria-labelledby="addFormLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form action="" method="post" id="formInsertTeacher">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addFormModal">เพิ่มรายชื่อครู</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="">ชื่อจริง</label>
                                        <input type="text" name="nameIn" class="form-control" id="nameIn" placeholder="กรอกชื่อจริง" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">นามสกุล</label>
                                        <input type="text" name="lastnameIn"class="form-control" id="lastnameIn" placeholder="กรอกนามสกุล" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">สาขา/สามัญ</label>
                                        <select name="" id="selectBranch" class="form-select">
                                            <?php while($row = mysqli_fetch_array($queryB)){ ?>
                                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">รหัสผ่านบัญชี</label>
                                        <input type="password" name="passwordIn"class="form-control" id="passwordIn" placeholder="กรอกรหัสผ่าน" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">รหัสผ่านยืนยันเข้าสอน</label>
                                        <input type="password" name="passwordV_IN" class="form-control" id="passwordV_IN" placeholder="กรอกรหัสผ่าน" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100" name="add" value="เพิ่ม">เพิ่ม</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <h6 class="text-center">ตารางรายชื่อครูที่สามารถเข้าใช้ได้</h6><br>

            <table class="table table-bordered table-sm table-responsive" width = "100%">
                <tr class=" table table-info">
                    <th>ลำดับ</th>
                    <th>ชื่อ-สกุล</th>
                    <th>สาขา/สามัญ</th>
                    <th>รหัสยืนยันครูผู้สอน</th>
                    <th></th>
                </tr>
                <?php while($row = mysqli_fetch_array($query)){ ?>
                <tr>
                    <td ><?php echo $row['id'] ?></td>
                    <td><?php echo $row['name'] . ' ' . $row['lastname'] ?></td>
                    <td><?php echo $row['branchName'] ?></td>
                    <td><?php echo $row['password_match'] ?></td>
                    
                    <td class="d-flex justify-content-around">
                        <div>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editFormModal<?php echo $row['id'] ?>">แก้ไข</button>
                        </div>
                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="editFormModal<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="addFormLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- FORM EDIT -->
                                <form action="./backend/editTeacher.php" method="post" id="formEditTeacher">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editFormModal">แก้ไขรายชื่อครู</h5>
                                    </div>
                                    <div class="modal-body">
                                        
                                        <input type="hidden" value="<?php echo $row['id'] ?>" id="editTeacherID" name="teacherID">
                                        <div class="mb-3">
                                            <label for="">ชื่อจริง</label>
                                            <input type="text" name="nameEdit" class="form-control" id="nameEdit" value="<?php echo $row['name'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">นามสกุล</label>
                                            <input type="text" name="lastnameEdit"class="form-control" id="lastnameEdit"  value="<?php echo $row['lastname'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">สาขา</label>
                                            <select name="branch" id="selectBranchEdit" class="form-control">
                                                <option value="1 "<?php if($row['branch_id'] == '1') echo 'selected';?>>การบัญชี</option>
                                                <option value="2 "<?php if($row['branch_id'] == '2') echo 'selected';?>>การตลาด</option>
                                                <option value="3 "<?php if($row['branch_id'] == '3') echo 'selected';?>>การจัดการสำนักงาน</option>
                                                <option value="4 "<?php if($row['branch_id'] == '4') echo 'selected';?>>โลจิสติกส์</option>
                                                <option value="5 "<?php if($row['branch_id'] == '5') echo 'selected';?>>เทคโนโลยีธูรกิจดิจิทัล</option>
                                                <option value="6 "<?php if($row['branch_id'] == '6') echo 'selected';?>>ธุรกิจค้าปลีก</option>
                                                <option value="7 "<?php if($row['branch_id'] == '7') echo 'selected';?>>เทคโนโลยีสารสนเทศ</option>
                                                <option value="8 "<?php if($row['branch_id'] == '8') echo 'selected';?>>ภาษาต่างประเทศ</option>
                                                <option value="9 "<?php if($row['branch_id'] == '9') echo 'selected';?>>การโรงแรม</option>
                                                <option value="10 "<?php if($row['branch_id'] == '10') echo 'selected';?>>อาหารและโภชนาการ</option>
                                                <option value="11 "<?php if($row['branch_id'] == '11') echo 'selected';?>>คหกรรม</option>
                                                <option value="12 "<?php if($row['branch_id'] == '12') echo 'selected';?>>แฟชั่นและสิ่งทอ</option>
                                                <option value="13 "<?php if($row['branch_id'] == '13') echo 'selected';?>>การออกแบบ</option>
                                                <option value="14 "<?php if($row['branch_id'] == '14') echo 'selected';?>>ดิจิทัลกราฟิก</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">รหัสผ่านบัญชี</label>
                                            <input type="password" name="passwordEdit"class="form-control" id="passwordEdit" value="<?php echo $row['password'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">รหัสผ่านยืนยันเข้าสอน</label>
                                            <input type="password" name="passwordV_Edit"class="form-control" id="passwordV_Edit" value="<?php echo $row['password_match'] ?>" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-warning w-100">แก้ไข</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <form action="" method="post">
                        <input type="hidden" value="<?php echo $row['id'] ?>" name="teacherD_ID">
                        <button class="btn btn-sm btn-danger" value="ลบ"type="submit" name="teacherDelete">ลบ</button>
                    </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php 
                $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM teacher");
                $totalRow = mysqli_fetch_assoc($totalQuery);
                $totalPages = ceil($totalRow['total'] / $perPage);
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
<!-- javascript file here -->
<script src="assets/js/wanting.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

  $(document).on("submit",'#formInsertTeacher',function(e){
    e.preventDefault();
    let name = $('#nameIn').val()
    let password = $('#passwordIn').val()
    let department = $("#selectBranch").val()
    let lastname = $('#lastnameIn').val()
    let passwordV_IN = $('#passwordV_IN').val()
    let formData = new FormData();
    formData.append("name" , name)
    formData.append("lastname" , lastname)
    formData.append("password" , password)
    formData.append("department" , department)
    formData.append("passwordV_IN" , passwordV_IN)
    $.ajax({
      url:"./backend/insertTeacher.php", //ส่งข้อมูลไปที่ไหน
      type:"POST",
      data:formData,
      contentType: false,
      processData: false,
      dataType:"json",
      success: function(response) {
        if(response.status === "success") {
            Swal.fire({ 
            title: "เพิ่มสำเร็จ",
            icon: "success",
            confirmButtonText: "ปิด"
            }).then(() => {
            $('#addFormModal').modal('hide');
            location.reload();
            });
        } else {
            Swal.fire({
            title: "รหัสยืนยันซ้ำกับคนอื่น",
            icon: "error",
            confirmButtonText: "ปิด"
            });
        }
        }
    })  
  })

</script>
<!-- PHP HERE -->
<?php
if(isset($_POST['teacherDelete'])){
    $id = $_POST['teacherD_ID'];

    $sqlDelete = "DELETE FROM teacher WHERE id = $id";
    $queryDelete = mysqli_query($conn, $sqlDelete);

    if($queryDelete){
        echo '<script>
            Swal.fire({
            title: "ลบสำเร็จ",
            icon: "success",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="teacher.php";
            })
        </script>';
        }else{
            echo '<script>
            Swal.fire({
            title: "ลบไม่สำเร็จ",
            icon: "error",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="teacher.php";
            })
        </script>';
        }
}
?>
