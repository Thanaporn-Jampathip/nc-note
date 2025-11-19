<?php
session_start();
include './backend/db.php';

unset($_SESSION['week']);
unset($_SESSION['term']);

$userid = $_SESSION['userid'];
$user = $_SESSION['user'];
if (!isset($_SESSION['user'])) {
    header('location: login_user.php');
    exit();
}

function convertToThaiDate($dateStr)
{
    $thai_months = [
        "01" => "มกราคม",
        "02" => "กุมภาพันธ์",
        "03" => "มีนาคม",
        "04" => "เมษายน",
        "05" => "พฤษภาคม",
        "06" => "มิถุนายน",
        "07" => "กรกฎาคม",
        "08" => "สิงหาคม",
        "09" => "กันยายน",
        "10" => "ตุลาคม",
        "11" => "พฤศจิกายน",
        "12" => "ธันวาคม"
    ];

    $timestamp = strtotime($dateStr);
    $day = date("d", $timestamp);
    $month = date("m", $timestamp);
    $year = date("Y", $timestamp) + 543;

    return "$day " . $thai_months[$month] . " $year";
}

$year = date('Y');
function Years($year)
{
    return (string) ($year + 543);
}

// ตรวจสอบภาคเรียนอัตโนมัติ
$term = date('n');
if ($term >= 5 && $term <= 9) {
    $term = 1;
} elseif ($term >= 10) {
    $term = 2;
}

// ดึงข้อมูลบันทึกทั้งหมดของผู้ใช้
$sqlNote = "
    SELECT record.id, record.begin_period,record.term,record.missStudentName, record.insteadTeacher,
           record.end_period, record.date, record.week,record.miss,record.all_student, record.note,
           teacher.name AS teacherName, user.username AS username,
           subject.subID AS subjectID, subject.name AS subjectName,
           t2.name AS insteadTeacherName, teacher.id AS teacherID
    FROM record
    JOIN user ON record.user_id = user.id
    JOIN subject ON record.subject_id = subject.id
    JOIN teacher ON subject.teacher_id = teacher.id
    LEFT JOIN teacher t2 ON record.insteadTeacher = t2.id
    WHERE record.user_id = ?
    ORDER BY record.id DESC
";

$stmtNote = $conn->prepare($sqlNote);
$stmtNote->bind_param("i", $userid);
$stmtNote->execute();
$queryN = $stmtNote->get_result();

// ดึงข้อมูลสัปดาห์ที่มีบันทึก
$sqlWeekTerm = "SELECT DISTINCT week, term FROM record WHERE user_id = ? AND record.term = ? ORDER BY record.week + 0 ASC";
$stmtWeekTerm = $conn->prepare($sqlWeekTerm);
$stmtWeekTerm->bind_param("ii", $userid, $term);
$stmtWeekTerm->execute();
$queryWeekTerm = $stmtWeekTerm->get_result();

// ถึงข้อมูลจากการเลือกวันที่ 
$Date = null;
if (isset($_GET['searchDate'])) {
    $Date = $_GET['date'] ?? null;
}
$sqlData = "
    SELECT record.id, record.begin_period,record.term,record.missStudentName, record.insteadTeacher,
           record.end_period, record.date, record.week,record.miss,record.all_student, record.note,
           teacher.name AS teacherName, user.username AS username,
           subject.subID AS subjectID, subject.name AS subjectName,
           t2.name AS insteadTeacherName, teacher.id AS teacherID
    FROM record
    JOIN user ON record.user_id = user.id
    JOIN subject ON record.subject_id = subject.id
    JOIN teacher ON subject.teacher_id = teacher.id
    LEFT JOIN teacher t2 ON record.insteadTeacher = t2.id
    WHERE record.date = '$Date'
    ORDER BY record.id DESC
";
$queryData = mysqli_query($conn, $sqlData);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเรียน / สอน - ประวัติบันทึกการเรียน</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<style>
    html,
    body {
        overflow-x: hidden;
    }

    @media only screen and (min-width: 576px) {
        .form {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

    }

    @media only screen and (max-width: 576px) {
        .topic {
            display: flex;
            flex-direction: column;
        }

        .weeks_topic {
            width: 100%;
            margin-bottom: 10px;
        }

        .weeks_topic form {
            width: 100%;
        }

        .weeks_topic form select {
            width: 100%;
            box-sizing: border-box;
        }

        .weeks_topic select {
            width: 100% !important;
        }

        .searchWeekTerm {
            margin-top: 2;
        }
    }


    table tr {
        white-space: nowrap;
    }
</style>

<body>
    <?php include("component/navbar.php") ?>
    <div class="d-flex">
        <?php
        if ($usertype == 'staff') {
            include './component/sidebar.php';
        } else {
            include './component/sidebar_user.php';
        }
        ?>
        <div class="container m-2 p-4 border rounded-3" style="height: auto; width: 100%">
            <h5>ประวัติบันทึกการเรียน / การสอน</h5>
            <hr>
            <div class="topic d-flex justify-content-between">
                <p>ประวัติบันทึกการเรียน / การสอน ประจำสัปดาห์<br>
                    ภาคเรียนที่ <?php echo $term ?> ปีการศึกษา <?php echo Years($year); ?></p>

                <div class="form">
                    <form method="get" action="" id="searchDataFromWeekTerm" class="mb-3">
                        <div class="search d-flex justify-content-center mb-3">
                            <!-- Select Week -->
                            <div class="weeks_topic text-center ms-auto pe-2">
                                <label for="" class="form-label">สัปดาห์</label>
                                <select name="weeks" class="form-select" style="width: auto" id="weeks" required>
                                    <option value="" selected disabled>-- เลือก --</option>
                                    <?php while ($rowWeek = mysqli_fetch_array($queryWeekTerm)) { ?>
                                        <option value="<?php echo $rowWeek['week'] ?>">
                                            <?php echo $rowWeek['week'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- Select Term -->
                            <div class="weeks_topic text-center">
                                <label for="" class="form-label">ภาคเรียนที่</label>
                                <select name="terms" class="form-select" style="width: auto" id="terms" required>
                                    <option value="" selected disabled>-- เลือก --</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="search"
                            class="searchWeekTerm btn btn-primary btn-sm w-100">ค้นหาเฉพาะสัปดาห์และเทอม</button>
                    </form>
                    <?php
                    $week = $_GET['weeks'] ?? null;
                    $term = $_GET['terms'] ?? null;

                    if ($week && $term) {
                        ?>
                        <div class="my-3">
                            <form action="" method="get" class="w-100">
                                <label class="form-label d-flex justify-content-center">วันที่</label>
                                <select name="date" class="form-select" required>
                                    <option value="" selected disabled>-- เลือก --</option>
                                    <?php
                                    $sqlFitterDate = "SELECT DISTINCT date FROM record WHERE week = '$week' AND term = '$term'";
                                    $queryFitterDate = mysqli_query($conn, $sqlFitterDate);

                                    while ($rowDate = mysqli_fetch_array($queryFitterDate)) {
                                        ?>
                                        <option value="<?php echo $rowDate['date'] ?>">
                                            <?php echo convertToThaiDate($rowDate['date']) ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <button class="btn btn-sm btn-primary w-100 mt-3" name="searchDate" type="submit">
                                    ค้นหา
                                </button>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <h5 class="text-center mb-4">ประวัติบันทึก</h5>
            <!-- ประวัติบันทึกทั้งหมดที่เลือกจาก สัปดาห์และเทอมที่เลือก -->
            <div class="table-responsive">
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
                    if (isset($_GET['weeks']) && isset($_GET['terms'])) {
                        $selectWeek = null;
                        $selectTerm = null;
                        if (isset($_GET['search'])) {
                            $selectWeek = $_GET['weeks'];
                            $selectTerm = $_GET['terms'];
                            //Check Date With Week and Term
                            $sqlCheck = "SELECT week, term FROM record WHERE term = '$selectTerm' AND week = '$selectWeek'";
                            $queryCheck = mysqli_query($conn, $sqlCheck);
                            if (mysqli_num_rows($queryCheck) > 0) {
                                mysqli_data_seek($queryN, 0);
                                while ($rowN = mysqli_fetch_assoc($queryN)) {
                                    if (
                                        $rowN['week'] == $selectWeek && $rowN['term'] == $selectTerm
                                    ) {

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
                                            <td>
                                                <?php
                                                if ($rowN['note'] == 'เข้าสอนปกติ') {
                                                    echo '<span class="text-success">เข้าสอนปกติ</span>';
                                                } elseif ($rowN['note'] == 'สอนแทน') {
                                                    echo '<span class="text-warning">ครูสอนแทน</span><br>';
                                                    if (!empty($rowN['insteadTeacherName'])) {
                                                        echo $rowN['insteadTeacherName'];
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $rowN['term'] . " / " . Years($year); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            } else {
                                echo '<script>
                                Swal.fire({
                                title: "ไม่พบข้อมูล",
                                icon: "error",
                                timer: 1000,
                                didOpen: () => Swal.showLoading()
                                }).then(() =>{
                                    window.location.href="note_history.php";
                                })
                            </script>';
                            }
                        }
                    } elseif (isset($_GET['date'])) {
                        while ($rowData = mysqli_fetch_array($queryData)) {
                            $missStudent = $rowData['miss'];
                            $allStudent = $rowData['all_student'];
                            $studentCome = $allStudent - $missStudent;
                            ?>
                            <tr>
                                <td><?php echo $rowData['username'] ?></td>
                                <td><?php echo $rowData['subjectID'] ?></td>
                                <td><?php echo $rowData['subjectName'] ?></td>
                                <td><?php echo $rowData['begin_period'] . ' - ' . $rowData['end_period'] ?></td>
                                <td><?php echo $rowData['miss'] ?></td>
                                <td><?php echo nl2br($rowData['missStudentName']) ?></td>
                                <td><?php echo $studentCome ?></td>
                                <td><?php echo $rowData['teacherName'] ?></td>
                                <td><?php echo convertToThaiDate($rowData['date']); ?></td>
                                <td><?php echo $rowData['week'] ?></td>
                                <td>
                                    <?php
                                    if ($rowData['note'] == 'เข้าสอนปกติ') {
                                        echo '<span class="text-success">เข้าสอนปกติ</span>';
                                    } elseif ($rowData['note'] == 'สอนแทน') {
                                        echo '<span class="text-warning">ครูสอนแทน</span><br>';
                                        if (!empty($rowData['insteadTeacherName'])) {
                                            echo $rowData['insteadTeacherName'];
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $rowData['term'] . " / " . Years($year); ?></td>
                            </tr>
                        <?php }
                    } ?>
                </table>
            </div>

</body>

</html>