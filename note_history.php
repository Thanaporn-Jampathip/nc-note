<?php 
session_start();
include './backend/db.php';
$userid = $_SESSION['userid'];
$user = $_SESSION['user'];
if(!isset($_SESSION['user'])){
    header('location: login_user.php');
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

$year = date('Y');
function Years($year) {
    return (string)($year + 543);
}

$term = date('n');
if($term >= 5 && $term <= 9){
    $term = 1;
}elseif($term >= 10){
    $term = 2;
}

$sqlNote = "
    SELECT record.id, record.begin_period,record.term,record.missStudentName, record.insteadTeacher ,record.end_period, record.date, record.week,record.miss,record.all_student, record.note, teacher.name AS teacherName, user.username AS username, subject.subID AS subjectID, subject.name AS subjectName,t2.name AS insteadTeacherName, teacher.id AS teacherID
    FROM record
    JOIN user ON record.user_id = user.id
    JOIN subject ON record.subject_id = subject.id
    JOIN teacher ON subject.teacher_id = teacher.id
    LEFT JOIN teacher t2 ON record.insteadTeacher = t2.id
    WHERE record.user_id = $userid
    ORDER BY record.id DESC
    ";
$queryN = mysqli_query($conn,$sqlNote);

$sqlWeek = "SELECT DISTINCT week FROM record WHERE user_id = $userid AND record.term = $term ORDER BY record.week + 0 ASC";
$queryWeek = mysqli_query($conn,$sqlWeek);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเรียน / สอน - ประวัติบันทึกการเรียน</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
</head>
<style>
    html, body {
        overflow-x: hidden;
    }
    @media only screen and (max-width: 576px) {
        .topic {
            display: flex;
            flex-direction: column;
        }

        .weeks_topic form {
            width: 100%;            /* ให้ form กว้างสุด */
        }

        .weeks_topic form select {
            width: 100%;            /* ให้ select กว้างสุด */
            box-sizing: border-box; /* ป้องกัน padding ทำให้ล้น */
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
        <div class="container m-2 p-4 border rounded-3"style="height: auto; width: 100%">
            <h5>ประวัติบันทึกการเรียน / การสอน</h5>
            <hr>
            <div class="topic d-flex justify-content-between">
                <p>ประวัติบันทึกการเรียน / การสอน ประจำสัปดาห์<br>
                ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year); ?></p>

                <div class="weeks_topic d-flex align-items-start flex-wrap">
                    <p class="pe-3 mb-0">สัปดาห์ที่</p>
                    
                    <form method="get" action="">
                    <select name="weeks" class="form-select" style="width: auto" onchange="this.form.submit()">
                        <option value="" selected disabled>เลือก</option>
                        <?php while($rowWeek = mysqli_fetch_array($queryWeek)){ ?>
                            <option value="<?php echo $rowWeek['week'] ?>"><?php echo $rowWeek['week'] ?></option>
                        <?php } ?>
                    </select>
                    </form>
                </div>
            </div>
            <h6 class="text-center">ประวัติบันทึก</h6><br>
            <div class="table-responsive ">
                <table class="table table-bordered table-sm" width="100%">
                    <tr class="table table-info">
                        <th>ชื่อบัญชีห้อง</th>
                        <th>รหัสวิชา</th>
                        <th>ชื่อวิชา</th>
                        <th>เริ่มคาบ-สุดคาบ</th>
                        <th class="text-danger">ขาดเรียน</th>
                        <th class="text-danger">รายชื่อนักเรียนที่ขาดเรียน</th>
                        <th class="text-success">มาทั้งหมด</th>
                        <th>ครูผู้สอน</th>
                        <th>เวลาบันทึก</th>
                        <th>สัปดาห์</th>
                        <th class="text-danger">หมายเหตุ</th>
                        <th>ภาคเรียนที่</th>
                    </tr>
                    <?php
                    $selectWeek = null;
                    if(isset($_GET['weeks'])){
                    $selectWeek = $_GET['weeks'];
                    }
                        mysqli_data_seek($queryN,0);
                        while($rowN = mysqli_fetch_assoc($queryN)){
                            if($rowN['week'] == $selectWeek){
                                $missStudent = $rowN['miss'];
                                $allStudent = $rowN['all_student'];
                                $studentCome = $allStudent - $missStudent;
                        ?>
                    <tr>
                        <td><?php echo $rowN['username'] ?></td>
                        <td><?php echo $rowN['subjectID'] ?></td>
                        <td><?php echo $rowN['subjectName'] ?></td>
                        <td><?php echo $rowN['begin_period'] . ' -  ' . $rowN['end_period'] ?></td>
                        <td><?php echo $rowN['miss'] ?></td>
                        <td><?php echo nl2br($rowN['missStudentName']) ?></td>
                        <td><?php echo $studentCome ?></td>
                        <td><?php echo $rowN['teacherName'] ?></td>
                        <td><?php echo convertToThaiDate($rowN['date']); ?></td>
                        <td><?php echo $rowN['week'] ?></td>
                        <td><?php
                        if($rowN['note'] == 'เข้าสอนปกติ'){
                            echo '<span class="text-success">เข้าสอนปกติ</span>';
                        }elseif($rowN['note'] == 'สอนแทน'){
                            echo '<span class="text-warning">ครูสอนแทน</span>';
                        }
                        ?><br>
                        <?php
                        if($rowN['note'] === 'สอนแทน' && !empty($rowN['insteadTeacherName'])) {
                            echo $rowN['insteadTeacherName'];
                        }
                        ?>
                        </td>
                        <td><?php echo $rowN['term'] . " / " . Years($year); ?></td>
                    </tr>
                    <?php }} ?>
                </table>
            </div>
        </div>
</body>
</html>
<!-- JS HERE -->
<script>

</script>
<!-- PHP HERE -->
 <?php
 if($term == '2'){
    $sqlDeleteTerm1 = "DELETE FROM record WHERE term = '1'";
    mysqli_query($conn, $sqlDeleteTerm1);
 }elseif($term == '1'){
    $sqlDeleteTerm2 = "DELETE FROM record WHERE term = '2'";
    mysqli_query($conn, $sqlDeleteTerm2);
 }

 ?>