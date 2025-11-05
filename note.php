<?php
session_start();
include './backend/db.php';
$userid = (int)$_SESSION['userid'];
$usertype = $_SESSION['user_type'];
$user = $_SESSION['user'];
if(!isset($_SESSION['user'])){
    header('location: login_user.php');
}

$sqlU = "SELECT * FROM user WHERE id = $userid";
$queryU = mysqli_query($conn,$sqlU);
$rowU = mysqli_fetch_array($queryU);

$sqlS = "SELECT * FROM subject WHERE userID = $userid";
$queryS = mysqli_query($conn,$sqlS);
//แปลงเฉพราะปี
$year = date('Y');
function Years($year) {
    return (string)($year + 543);
}
function convertToThaiDate($dateStr) {
    $thai_months = [
        "01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน",
        "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม",
        "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม"
    ];

    $timestamp = strtotime($dateStr);
    $day = date("d", $timestamp);
    $month = date("m", $timestamp);
    $year = date("Y", $timestamp) + 543;

    return "$day " . $thai_months[$month] . " $year";
}

$selectDate = isset($_GET['selectDate']) ? $_GET['selectDate'] : date('Y-m-d'); // เลือกดูตามวัน เนื่องจากดูจากวันอื่น

if($usertype === 'user'){
    $sqlNote = "
        SELECT record.id, record.begin_period,record.missStudentName, record.insteadTeacher,record.term,record.end_period, record.date,record.all_student, record.week,record.miss, record.note, user.username AS username, subject.subID AS subjectID, subject.name AS subjectName, teacher.name AS teacherName, t2.name AS insteadTeacherName, teacher.id AS teacherID, user.id AS userID
        FROM record
        JOIN user ON record.user_id = user.id
        JOIN subject ON record.subject_id = subject.id
        JOIN teacher ON subject.teacher_id = teacher.id
        LEFT JOIN teacher t2 ON record.insteadTeacher = t2.id
        WHERE record.user_id = $userid
        AND record.date = '$selectDate' 
        ORDER BY record.id DESC
        ";
    $queryN = mysqli_query($conn,$sqlNote);
}elseif($usertype === 'staff'){
    $sqlNoteStaff = "
        SELECT record.id, record.begin_period, record.end_period, record.date, record.week,record.user_id, user.username AS username, user.status AS status, subject.subID AS subjectID, subject.name AS subjectName
        FROM record
        JOIN user ON record.user_id = user.id
        JOIN subject ON record.subject_id = subject.id
        ORDER BY record.week + 0 ASC
        ";
    $queryNS = mysqli_query($conn,$sqlNoteStaff);
}
    $selectedUserID = $_GET['user_id'] ?? '';

    if (!$selectedUserID) {
    $minUserQuery = "SELECT id FROM user ORDER BY id ASC LIMIT 1";
    $minUserResult = $conn->query($minUserQuery);
        if ($minUserResult && $minUserResult->num_rows > 0) {
            $minUserRow = $minUserResult->fetch_assoc();
            $selectedUserID = $minUserRow['id'];
            header("Location: ?user_id=$selectedUserID");
            exit();
        }
    }

    $weekQuery = "SELECT DISTINCT week FROM record WHERE 1";
    if ($selectedUserID) {
        $weekQuery .= " AND user_id = '$selectedUserID'";
    }
    $weekQuery .= " ORDER BY week + 0 DESC";

    $weekResult = $conn->query($weekQuery);

    $selectedWeek = $_GET['week'] ?? null;

    if (!$selectedWeek && $weekResult->num_rows > 0) {
        $firstWeekRow = $weekResult->fetch_assoc();
        $selectedWeek = $firstWeekRow['week'];

        $weekResult->data_seek(0);
    }
    $dateQuery = "SELECT DISTINCT date FROM record WHERE week = '$selectedWeek'";
    if ($selectedUserID) {
        $dateQuery .= " AND user_id = '$selectedUserID'";
    }
    $dateQuery .= " ORDER BY date DESC";

    $dateResult = $conn->query($dateQuery);

    $selectedDate = $_GET['date'] ?? null;

    if (!$selectedDate && $dateResult->num_rows > 0) {
        $firstDateRow = $dateResult->fetch_assoc();
        $selectedDate = $firstDateRow['date'];
        $dateResult->data_seek(0);
    }   
    $subjectQuery = "SELECT subject_name, begin_period, end_period, miss, all_student ,note, teacher.name AS teacherName, teacher.id AS teacherID
                 FROM record 
                 JOIN teacher ON record.insteadTeacher = teacher.id
                 WHERE date = '$selectedDate' AND week = '$selectedWeek'";

    if ($selectedUserID) {
        $subjectQuery .= " AND user_id = '$selectedUserID'";
    }
    $subjectResult = $conn->query($subjectQuery);
    // ภาคเรียน
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
    <title>บันทึกการเรียน / สอน - บันทึกการเรียน</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
</head>
<style>
    html, body {
        overflow-x: hidden;
    }
    @media only screen and (max-width: 500px){
            .buttonInTable {
                display: flex;
                flex-direction: column; /* ปุ่มจะเรียงจากบนลงล่าง */
                gap: 5px; /* เว้นระยะห่างระหว่างปุ่ม (เลือกใส่) */
            }

            .buttonInTable button {
                width: 100%; /* ทำให้ปุ่มยืดเต็ม container */
            }
        }
    table tr{
        white-space: nowrap;
    }
</style>
<body>
    <?php include("component/navbar.php") ?>
    <div class="d-flex">
        <?php if($usertype == 'staff'){
                include './component/sidebar.php';
            }else{
                include './component/sidebar_user.php';
            } 
        ?>
        <!-- STAFF PAGE -->
        <?php if($usertype == 'staff'){ ?>
        <div class="container-fluid m-2 p-4 border rounded-3"style="height: auto;">
            <h5>บันทึกการเรียน / การสอน</h5>
            <hr>
            <div class="d-flex justify-content-between">
                <p>บันทึกการเรียน / การสอน ประจำสัปดาห์<br>
                ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year); ?></p>
            </div>
            <h6 class="text-center">ประวัติรายการบันทึกประจำสัปดาห์</h6><br>
            <div class="card p-4 border rounded-3 shadow-sm w-100">
                <div class="row">
                <!-- ซ้ายสุด: เลือกผู้ใช้งาน -->
                    <div class="col-md-2">
                        <!-- search user -->
                    <h5>เลือกผู้ใช้งาน</h5>
                    <input type="text" id="searchUser" class="form-control mb-2" placeholder="ค้นหาตามสาขา">

                    <ul class="list-group" id="userList">
                        <?php
                        $userQuery = "SELECT id, username FROM user WHERE status != 'internship' ORDER BY id ASC";
                        $userResult = $conn->query($userQuery);

                        while ($userRow = $userResult->fetch_assoc()) {
                            $isUserActive = ($userRow['id'] == $selectedUserID);
                        ?>
                            <li class="list-group-item <?= $isUserActive ? 'active' : '' ?>">
                                <a href="?user_id=<?= $userRow['id'] ?>&week=<?= $selectedWeek ?>&date=<?= $selectedDate ?>" 
                                class="text-decoration-none <?= $isUserActive ? 'text-white' : 'text-dark' ?>">
                                    <?= $userRow['username'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    </div>

                    <!-- เลือกสัปดาห์ -->
                    <div class="col-md-2">
                        <h5>เลือกสัปดาห์</h5>
                        <ul class="list-group">
                            <?php while ($weekRow = $weekResult->fetch_assoc()) { 
                                $isActive = ($weekRow['week'] == $selectedWeek); ?>
                                <li class="list-group-item <?= $isActive ? 'active' : '' ?>">
                                    <a href="?week=<?= $weekRow['week'] ?>&user_id=<?= $selectedUserID ?>" 
                                    class="text-decoration-none <?= $isActive ? 'text-white' : 'text-dark' ?>">
                                        สัปดาห์ที่ <?= $weekRow['week'] ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <!-- เลือกวันที่ -->
                    <div class="col-md-2">
                        <h5>เลือกวันที่</h5>
                        <ul class="list-group">
                            <?php
                            $allDateQuery = "SELECT DISTINCT date FROM record WHERE week = '$selectedWeek'";
                            if ($selectedUserID) {
                                $allDateQuery .= " AND user_id = '$selectedUserID'";
                            }
                            $allDateQuery .= " ORDER BY date DESC";
                            $allDateResult = $conn->query($allDateQuery);
                            while ($dateRow = $allDateResult->fetch_assoc()) { 
                                $isActiveDate = ($dateRow['date'] == $selectedDate); ?>
                                <li class="list-group-item <?= $isActiveDate ? 'active' : '' ?>">
                                    <a href="?week=<?= $selectedWeek ?>&date=<?= $dateRow['date'] ?>&user_id=<?= $selectedUserID ?>" 
                                    class="text-decoration-none <?= $isActiveDate ? 'text-white' : 'text-dark' ?>">
                                        <?= convertToThaiDate($dateRow['date']) ?>

                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <!-- รายละเอียดวิชาเรียน -->
                    <div class="col-md-6">
                        <h5>รายละเอียด</h5>
                        <?php if ($subjectResult->num_rows > 0) { ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ชื่อวิชา</th>
                                        <th>เวลาเริ่ม</th>
                                        <th>เวลาสิ้นสุด</th>
                                        <th class="text-danger">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($subjectRow = $subjectResult->fetch_assoc()) { 
                                        ?>
                                        <tr>
                                            <td><?= $subjectRow['subject_name'] ?></td>
                                            <td><?= $subjectRow['begin_period'] ?></td>
                                            <td><?= $subjectRow['end_period'] ?></td>
                                            <td><?php
                                            if($subjectRow['note'] == 'เข้าสอนปกติ'){
                                                echo '<span class="text-success">ครูเข้าสอนปกติ<span>';
                                            }elseif($subjectRow['note'] == 'สอนแทน'){
                                                echo '<span class="text-warning">สอนแทนโดย</span>';
                                            }
                                            ?><br>
                                            <?php
                                            if($subjectRow['note'] === 'สอนแทน' && !empty($subjectRow['teacherName'])) {
                                                echo $subjectRow['teacherName'];
                                            }
                                            ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p>ไม่มีวิชาเรียนในวันที่นี้</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }elseif ($usertype == 'user'){ ?>
    <!-- USER PAGE -->
    <dive class="container m-2 p-4 border rounded-3"style="height: auto;">
            <h5>บันทึกการเรียน / การสอน</h5>
            <hr>
            <div class="d-flex justify-content-between">
                <p>ฟอร์มบันทึกการเรียน / การสอน<br>
                ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year); ?></p>

                <div class="d-flex align-items-start">

                    <!-- BUTTON ADD -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#saveFormModal">บันทึก</button>                
                </div>
            </div>
            <!-- MODAL ADD -->
            <div class="modal fade" id="saveFormModal" tabindex="-1" aria-labelledby="addFormLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- FORM ADD -->
                        <form action="" method="post" id="insertNoteForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editFormModal">บันทึกการเรียน / การสอน</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" value="<?php echo $rowU['id'] ?>" id="userID">
                                <div class="row">
                                    <div class="col-4 mb-3">
                                        <label for="">รหัสวิชา</label>
                                        <select name="subjectID" id="subjectID" class="form-select" required>
                                            <option value="" selected disabled>-- เลือกรหัสวิชา --</option>
                                            <?php
                                            while($rowID = mysqli_fetch_array($queryS)){?>
                                                <option value="<?php echo $rowID['id'] ?>"><?php echo $rowID['subID'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="">ชื่อรายวิชา</label>
                                        <select name="subjectName" id="subjectName" class="form-select" required>
                                            <option value="" selected disabled>-- เลือกชื่อวิชา --</option>
                                            <?php 
                                            mysqli_data_seek($queryS, 0);
                                            while($rowName = mysqli_fetch_array($queryS)){ ?>
                                                <option value="<?php echo $rowName['name'] ?>"><?php echo $rowName['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="">เริ่มคาบ</label>
                                            <select name="start" id="startID" class="form-control" required>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label for="">สุดคาบ</label>
                                            <select name="end" id="endID" class="form-control" required>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="">ขาดเรียนกี่คน <br><span style="font-size: 14px; color: gray;">(ถ้าไม่มีไม่ต้องใส่)</span></label>
                                            <input type="int" class="form-control" id="miss" placeholder="จำนวณเต็ม">
                                        </div>
                                        <div class="col">
                                            <label for="">นักเรียนทั้งหมด</label>
                                            <input type="int" class="form-control" id="all" placeholder="จำนวณเต็ม" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="">รายชื่อนักเรียนที่ขาดเรียน <span style="font-size: 14px; color: gray;">(ถ้าไม่มีขาดไม่ต้องกรอก)</span></label>
                                    <input type="text" id="missStudentName" class="form-control" placeholder="กรอกชื่อนักเรียนที่ขาดเรียน">
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="">สัปดาห์</label>
                                            <select name="weeks" id="weeks" class="form-control" required>
                                                <option value="" selected disabled>สป.ที่เท่าไหร่</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="" >ภาคเรียนที่</label>
                                            <?php
                                                $term = date('n');
                                                if($term >= 5 && $term <= 9){
                                                    $term = 1;
                                                }elseif($term >= 10){
                                                    $term = 2;
                                                }
                                            ?>
                                            <input name="term" id="term" class="form-control" value="<?php echo $term  . " / " . Years($year); ?>" required readonly>
                                            </input>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="" >ยืนยันรหัสครู</label>
                                            <input type="password" class="form-control" id="passwordTeacherV_IN" placeholder="รหัสยืนยันเข้าสอน" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="" class="text-danger">หมายเหตุ</label><span style="font-size: 14px; color: gray;"> (กรณีที่ครูประจำคาบลา หรือมีการสอนแทน)</span>
                                    <select name="" id="notation" class="form-select">
                                        <option value="เข้าสอนปกติ" selected>เข้าสอนปกติ</option>
                                        <option value="สอนแทน">สอนแทน</option>
                                    </select>
                                </div>
                                <div class="row" id="instead" style="display: none;">
                                    <div class="col-5">
                                        <div class="mb-3">
                                            <label for="">สาขา</label>
                                            <select name="" id="selectBranch" class="form-select" required>
                                                <?php
                                                $sql = "SELECT id,name FROM branch";
                                                $query = mysqli_query($conn, $sql);
                                                while($row = mysqli_fetch_array($query)){
                                                ?>
                                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="">ครูสอนแทน<span style="font-size: 14px; color: gray;"> (กรุณาเลือกสาขาก่อน)</span></label>
                                            <select name="insteadTeacher" id="selectTeacher" class="form-select">
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- DATA -->
            <h6 class="text-center">รายการบันทึก</h6><br>

            <div class="table-responsive" >
                <table class="table table-bordered table-sm">
                    <tr class=" table table-info">
                        <th>ชื่อบัญชีห้อง</th>
                        <th>รหัสวิชา</th>
                        <th>ชื่อวิชา</th>
                        <th>เริ่มคาบ-สุดคาบ</th>
                        <th class="text-danger">ขาดเรียนกี่คน</th>
                        <th class="text-danger">รายชื่อขาดเรียน</th>
                        <th class="text-success">มาทั้งหมด</th>
                        <th>ครูผู้สอน</th>
                        <th>เวลาบันทึก</th>
                        <th>สัปดาห์</th>
                        <th class="text-danger">หมายเหตุ</th>
                        <th>ภาคเรียนที่</th>
                        <th></th>
                    </tr>
                    <?php  
                        $sql = "SELECT name,lastname,id FROM teacher";
                        $query = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($query);
                        while($rowN = mysqli_fetch_array($queryN)){
                            $missStudent = $rowN['miss'];
                            $allStudent = $rowN['all_student'];
                            $studentCome = $allStudent - $missStudent;
                        ?>
                    <tr>
                        <td><?php echo $rowN['username'] ?></td>
                        <td><?php echo $rowN['subjectID'] ?></td>
                        <td><?php echo $rowN['subjectName'] ?></td>
                        <td><?php echo $rowN['begin_period'] . ' - ' . $rowN['end_period'] ?></td>
                        <td><?php echo $rowN['miss'] ?></td>
                        <td><?php echo nl2br($rowN['missStudentName']) ?></td>
                        <td><?php echo $studentCome ?></td>
                        <td><?php echo $rowN['teacherName'] ?></td>
                        <td><?php echo convertToThaiDate($rowN['date']); ?></td>
                        <td><?php echo $rowN['week'] ?></td>
                        <td><?php 
                        if($rowN['note'] === 'เข้าสอนปกติ'){
                            echo '<span class="text-success">เข้าสอนปกติ</span>';
                        }elseif($rowN['note'] == 'สอนแทน'){
                            echo '<span class="text-warning">ครูสอนแทน</span>';
                        }
                        ?><br>
                        <?php
                        if($rowN['note'] === 'สอนแทน' && !empty($rowN['insteadTeacherName'])) {
                            echo $rowN['insteadTeacherName'];
                        }
                        ?></td>
                        <td><?php echo $rowN['term'] . " / " . Years($year)?></td>
                        <td class="buttonInTable d-flex justify-content-around ">
                            <div class="">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editNoteUserModal<?php echo $rowN['id'] ?>">แก้ไข</button>
                            </div>
                            <!-- EDIT MODAL -->
                            <div class="modal fade" id="editNoteUserModal<?php echo $rowN['id'] ?>" tabindex="-1" aria-labelledby="addFormLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- FORM EDIT -->
                                    <form action="./backend/editNote.php" method="post" id="editNoteForm">
                                        <div class="modal-header d-flex">
                                            <div>
                                                <h5 class="modal-title" id="editFormModal">แก้ไขบันทึก</h5>
                                            </div>
                                            <div class="ms-auto pe-2">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                        </div>
                                        
                                        <div class="modal-body">
                                            <input type="hidden" value="<?php echo $rowN['id'] ?>" name="recordID" id="recordID">
                                            <input type="hidden" value="<?php echo $rowN['userID'] ?>" id="userID">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label for="">รหัสวิชา</label>
                                                    <select name="editSubID" id="editSubID" class="form-control">
                                                        <?php 
                                                        $sql = "SELECT id,subID FROM subject WHERE userID = $userid";
                                                        $query = mysqli_query($conn, $sql);
                                                        while($rowSubID = mysqli_fetch_assoc($query)){ ?>
                                                            <option value="<?php echo $rowSubID['id'] ?>"<?php if($rowN['subjectID'] == $rowSubID['subID']) echo ' selected'; ?>>
                                                                <?php echo $rowSubID['subID']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="">ชื่อรายวิชา</label>
                                                    <select name="editSubName" id="editSubName" class="form-control">
                                                        <?php 
                                                        $sql = "SELECT name FROM subject WHERE userID = $userid";
                                                        $query = mysqli_query($conn, $sql);
                                                        while($rowSubName = mysqli_fetch_assoc($query)){?>
                                                            <option value="<?php echo $rowSubName['name'] ?>"<?php if($rowN['subjectName'] == $rowSubName['name']) echo 'selected'; ?>><?php echo $rowSubName['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="mb-3">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <label for="">เริ่มคาบ</label>
                                                        <select name="editStartID" id="editStartID" class="form-control">
                                                            <option value="1"<?php if($rowN['begin_period'] == '1') echo 'selected'; ?>>1</option>
                                                            <option value="2"<?php if($rowN['begin_period'] == '2') echo 'selected'; ?>>2</option>
                                                            <option value="3"<?php if($rowN['begin_period'] == '3') echo 'selected'; ?>>3</option>
                                                            <option value="4"<?php if($rowN['begin_period'] == '4') echo 'selected'; ?>>4</option>
                                                            <option value="5"<?php if($rowN['begin_period'] == '5') echo 'selected'; ?>>5</option>
                                                            <option value="6"<?php if($rowN['begin_period'] == '6') echo 'selected'; ?>>6</option>
                                                            <option value="7"<?php if($rowN['begin_period'] == '7') echo 'selected'; ?>>7</option>
                                                            <option value="8"<?php if($rowN['begin_period'] == '8') echo 'selected'; ?>>8</option>
                                                            <option value="9"<?php if($rowN['begin_period'] == '9') echo 'selected'; ?>>9</option>
                                                            <option value="10"<?php if($rowN['begin_period'] == '10') echo 'selected'; ?>>10</option>
                                                            <option value="11"<?php if($rowN['begin_period'] == '11') echo 'selected'; ?>>11</option>
                                                            <option value="12"<?php if($rowN['begin_period'] == '12') echo 'selected'; ?>>12</option>
                                                            <option value="13"<?php if($rowN['begin_period'] == '13') echo 'selected'; ?>>13</option>
                                                        </select>
                                                    </div>  
                                                    <div class="col-2">
                                                        <label for="">สุดคาบ</label>
                                                        <select name="editEndID" id="editEndID" class="form-control">
                                                            <option value="1"<?php if($rowN['end_period'] == '1') echo 'selected'; ?>>1</option>
                                                            <option value="2"<?php if($rowN['end_period'] == '2') echo 'selected'; ?>>2</option>
                                                            <option value="3"<?php if($rowN['end_period'] == '3') echo 'selected'; ?>>3</option>
                                                            <option value="4"<?php if($rowN['end_period'] == '4') echo 'selected'; ?>>4</option>
                                                            <option value="5"<?php if($rowN['end_period'] == '5') echo 'selected'; ?>>5</option>
                                                            <option value="6"<?php if($rowN['end_period'] == '6') echo 'selected'; ?>>6</option>
                                                            <option value="7"<?php if($rowN['end_period'] == '7') echo 'selected'; ?>>7</option>
                                                            <option value="8"<?php if($rowN['end_period'] == '8') echo 'selected'; ?>>8</option>
                                                            <option value="9"<?php if($rowN['end_period'] == '9') echo 'selected'; ?>>9</option>
                                                            <option value="10"<?php if($rowN['end_period'] == '10') echo 'selected'; ?>>10</option>
                                                            <option value="11"<?php if($rowN['end_period'] == '11') echo 'selected'; ?>>11</option>
                                                            <option value="12"<?php if($rowN['end_period'] == '12') echo 'selected'; ?>>12</option>
                                                            <option value="13"<?php if($rowN['end_period'] == '13') echo 'selected'; ?>>13</option>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label for="">ขาดเรียนกี่คน</label>
                                                        <input type="int" value="<?php echo $rowN['miss'] ?>" name="miss" class="form-control">
                                                    </div>
                                                    <div class="col">
                                                        <label for="">ทั้งหมดกี่คน</label>
                                                        <input type="int" value="<?php echo $rowN['all_student'] ?>" name="allStudentEdit" class="form-control">
                                                    </div>
                                                </div>        
                                            </div>
                                            <div class="mb-3">
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="">ภาคเรียนที่</label>
                                                        <input type="text" name="term" class="form-control" readonly value="<?php echo $term . " / " . Years($year) ?>">
                                                    </div>
                                                    <div class="col">
                                                        <label for="">สัปดาห์</label>
                                                        <select name="weeks" id="weeks" class="form-control">
                                                            <option value="1"<?php if($rowN['week'] == '1') echo 'selected' ?>>1</option>
                                                            <option value="2" <?php if($rowN['week'] == '2') echo 'selected' ?>>2</option>
                                                            <option value="3" <?php if($rowN['week'] == '3') echo 'selected' ?>>3</option>
                                                            <option value="4" <?php if($rowN['week'] == '4') echo 'selected' ?>>4</option>
                                                            <option value="5" <?php if($rowN['week'] == '5') echo 'selected' ?>>5</option>
                                                            <option value="6" <?php if($rowN['week'] == '6') echo 'selected' ?>>6</option>
                                                            <option value="7" <?php if($rowN['week'] == '7') echo 'selected' ?>>7</option>
                                                            <option value="8" <?php if($rowN['week'] == '8') echo 'selected' ?>>8</option>
                                                            <option value="9" <?php if($rowN['week'] == '9') echo 'selected' ?>>9</option>
                                                            <option value="10" <?php if($rowN['week'] == '10') echo 'selected' ?>>10</option>
                                                            <option value="11" <?php if($rowN['week'] == '11') echo 'selected' ?>>11</option>
                                                            <option value="12" <?php if($rowN['week'] == '12') echo 'selected' ?>>12</option>
                                                            <option value="13" <?php if($rowN['week'] == '13') echo 'selected' ?>>13</option>
                                                            <option value="14" <?php if($rowN['week'] == '14') echo 'selected' ?>>14</option>
                                                            <option value="15" <?php if($rowN['week'] == '15') echo 'selected' ?>>15</option>
                                                            <option value="16" <?php if($rowN['week'] == '16') echo 'selected' ?>>16</option>
                                                            <option value="17" <?php if($rowN['week'] == '17') echo 'selected' ?>>17</option>
                                                            <option value="18" <?php if($rowN['week'] == '18') echo 'selected' ?>>18</option>
                                                        </select>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                            <!-- ถ้ามีชื่อนักเรียนขาด -->
                                            <div class="mb-3">
                                                <label for="">รายชื่อนักเรียนที่ขาดเรียน<br><span style="font-size: 14px; color: gray;">(หากเผลอใส่ในวันที่มาเรียนครบ กรอกคำว่า <span style="color: red;">"ไม่มี"</span> )</span></label>
                                                <input type="int" value="<?php echo $rowN['missStudentName'] ?>" name="missStudentName" class="form-control">
                                            </div>

                                            <!-- ถ้ามีการสอนแทน -->
                                            <?php
                                            if($rowN['note'] == 'สอนแทน'){
                                            ?>
                                            <h5 class="text-danger">แก้ไขรายชื่อครูสอนแทน</h5>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="">สาขา</label>
                                                        <select id="branchEdit" class="form-select branchEdit">
                                                            <?php
                                                                $sql = "SELECT id,name FROM branch";
                                                                $query = mysqli_query($conn,$sql);
                                                                while($row = mysqli_fetch_array($query)){
                                                            ?>
                                                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label for="">ครูสอนแทน</label>
                                                        <select name="insteadTeacher" id="teacherEdit" class="form-select teacherEdit">
                                                            
                                                        </select>
                                                    </div>
                                                </div>  
                                            </div>
                                            <?php } ?>
                                            <button class="btn btn-warning w-100" type="submit">แก้ไข</button>
                                            
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div>
                            <form action="" method="post" class="">
                                <input type="hidden" value="<?php echo $rowN['id'] ?>" name="recID">
                                <button class="btn btn-sm btn-danger" type="submit" name="delete">ลบ</button>
                            </form>
                        </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
<?php } ?>
</body>
</html>

<!-- JS HERE -->
<script src="assets/js/wanting.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const searchInput = document.getElementById('searchUser');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const listItems = document.querySelectorAll('#userList li');

            listItems.forEach(function (item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    };


    // insert note
    $(document).on("submit", '#insertNoteForm', function(e){
    e.preventDefault();
    let userID = $('#userID').val();
    let subjectID = $('#subjectID').val();
    let subjectName = $('#subjectName').val();
    let start = $('#startID').val();
    let end = $('#endID').val();
    let week = $('#weeks').val();
    let passwordVerifyTeacher = $('#passwordTeacherV_IN').val();
    let note = $('#notation').val();
    let miss = $('#miss').val();
    let missStudent = $('#missStudentName').val();
    let all = $('#all').val();
    let term = $('#term').val();
    let insteadTeacher = $('#selectTeacher').val();

    let formData = new FormData();

    formData.append('userID' , userID);
    formData.append('subjectID' , subjectID);
    formData.append('subjectName' , subjectName);
    formData.append('start' , start);
    formData.append('end' , end);
    formData.append('week' , week);
    formData.append('passwordVerifyTeacher' , passwordVerifyTeacher);
    formData.append('note' , note);
    formData.append('miss' , miss);
    formData.append('all' , all);
    formData.append('term' , term);
    
    if(missStudent){
        formData.append('missStudent' , missStudent);
    }else{
        formData.append('missStudent' , '');
    }

    if(note === 'สอนแทน' && insteadTeacher){
        formData.append('insteadTeacher', insteadTeacher);
    } else {
        formData.append('insteadTeacher', '');
    }

        $.ajax({
            url: "./backend/insertNote.php",
            type: "POST",
            data:formData,
            contentType: false,
            processData: false,
            dataType: "json",
            // Check Password Teacher
            success: function(alertS){
                if(alertS.status === 'ไม่มีครูคนไหนใช้รหัสนี้'){
                    Swal.fire({
                    title: "ไม่มีครูคนไหนใช้รหัสนี้",
                    icon: "error",
                    timer: 1500,
                    didOpen: () => Swal.showLoading()
                    }).then(() =>{
                        $('#saveFormModal').modal('hide');
                        location.reload();
                    });
                    }else {
                        Swal.fire({
                        title: "เพิ่มสำเร็จ",
                        icon: "success",
                        timer: 1500,
                        didOpen: () => Swal.showLoading()
                        }).then(() =>{
                            $('#saveFormModal').modal('hide');
                            location.reload();
                        });
                    };
                }
            });
        });
        // กดค่าสอนแทน จะขึ้นให้เลือกสาขา
        $('#notation').on('change', function () {
            if ($(this).val() === 'สอนแทน') {
            $('#instead').slideDown();
            } else {
            $('#instead').slideUp();
            }
        });

// เมื่อเลือกสาขา (insert)
$(document).on('change', '#selectBranch', function () {
    let branchID_Insert = $(this).val();
    console.log("เลือกสาขา (insert):", branchID_Insert);
    loadTeachers(branchID_Insert, $('#selectTeacher'));
});

// เมื่อเลือกสาขา (edit แต่ละ modal)
$(document).on('change', '.branchEdit', function () {
    const modal = $(this).closest('.modal');
    const branchID = $(this).val();
    const teacherSelect = modal.find('.teacherEdit');

    console.log("เลือกสาขา (Edit):", branchID);
    loadTeachers(branchID, teacherSelect);
});

// ฟังก์ชันโหลดครู
function loadTeachers(branchID, $teacherSelect) {
    if (!branchID) return;
    $.ajax({
        url: "./backend/selectTeacherFromBranch.php",
        type: "POST",
        data: { branch_id: branchID },
        dataType: "json",
        success: function (data) {
            $teacherSelect.empty().append('<option value="" selected disabled>-- เลือกครู --</option>');
            $.each(data, function (index, teacher) {
                $teacherSelect.append(
                    '<option value="' + teacher.id + '">' + teacher.name + '</option>'
                );
            });
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}
</script>
<?php
if(isset($_POST['delete'])) {
    $id = $_POST['recID'];
    
    $sql = "DELETE FROM record WHERE id = $id";
    $query = mysqli_query($conn,$sql);

    if($query){
        echo '<script>
            Swal.fire({
            title: "ลบสำเร็จ",
            icon: "success",
            timer: 1000,
            didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="note.php";
            })
        </script>';
    }
}
?>