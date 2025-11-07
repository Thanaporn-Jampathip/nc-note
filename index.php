<?php
session_start();
include 'backend/db.php';
$userid = $_SESSION['userid'];
$user = $_SESSION['user'];
$usertype = $_SESSION['user_type'];
if (!isset($_SESSION['user'])) {
    header('location: login_user.php');
}

// แปลงเป็นไทย วัน เดือน ปี
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
// แปลงเป็นไทย ปี
$year = date('Y');
function Years($year) {
    return (string)($year + 543);
}

$sql = 'SELECT name FROM branch WHERE name != "สามัญสัมพันธ์"';
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['name'];
}
$branchs = json_encode($labels);

// CHART USER SIDE
$sqlChartUser = "SELECT * , subject.name AS subjectName 
    FROM record 
    JOIN subject ON record.subject_id = subject.id 
    WHERE user_id = $userid AND date = CURDATE() 
    ORDER BY record.id ASC";

$queryChartUser = mysqli_query($conn, $sqlChartUser);
$dataMiss = [];
$dataAll = [];
$subject = [];
while ($rowChartUser = mysqli_fetch_assoc($queryChartUser)) {
    $subject[] = $rowChartUser["subjectName"];
    $dataMiss[] = $rowChartUser['miss'];
    $dataAll[] = $rowChartUser['all_student'];
}

$totallStudent = array_map(function ($miss, $all) {
    return $all - $miss;
}, $dataMiss, $dataAll);
$subjectName = json_encode($subject);
$miss = json_encode($dataMiss);
$all = json_encode($dataAll);
$totallStudent = json_encode($totallStudent);

// CHART STAFF SIDE
$branchCount = [];
$sqlChartCount = "SELECT * ,branch.id AS branchID , branch.name AS branchName FROM user JOIN branch ON user.branch_id = branch.id WHERE status != 'internship'";
$queryChartCount = mysqli_query($conn, $sqlChartCount);
while ($rowChartCount = mysqli_fetch_array($queryChartCount)) {
    $branch = $rowChartCount["branch_id"];
    if (!isset($branchCount[$branch])) {
        $branchCount[$branch] = 0;
    }
    $branchCount[$branch]++;
}
$accountant = intval(json_encode($branchCount[1] ?? 0));
$marketing = intval(json_encode($branchCount[2] ?? 0));
$management = intval(json_encode($branchCount[3] ?? 0));
$logistics = intval(json_encode($branchCount[4] ?? 0));
$dbt = intval(json_encode($branchCount[5] ?? 0));
$retail = intval(json_encode($branchCount[6] ?? 0));
$it = intval(json_encode($branchCount[7] ?? 0));
$foreignLanguage = intval(json_encode($branchCount[8] ?? 0));
$hotelManagement = intval(json_encode($branchCount[9] ?? 0));
$food = intval(json_encode($branchCount[10] ?? 0));
$homeEconomics = intval(json_encode($branchCount[11] ?? 0));
$fashion = intval(json_encode($branchCount[12] ?? 0));
$design = intval(json_encode($branchCount[13] ?? 0));
$dg = intval(json_encode($branchCount[14] ?? 0));

$branchSent = [];

$sqlLastWeek = "SELECT MAX(week + 0) AS latestWeek FROM record";
$resultLastWeek = mysqli_query($conn, $sqlLastWeek);
$rowLastWeek = mysqli_fetch_assoc($resultLastWeek);
$latestWeek = $rowLastWeek['latestWeek'];
$week = $_GET['week'] ?? $latestWeek;

$sqlSent = "SELECT DISTINCT user_id AS userID , branch.id AS branchID
    FROM record 
    JOIN user ON record.user_id = user.id
    JOIN branch ON user.branch_id = branch.id
    WHERE user.status != 'internship' AND record.week = '$week'
";

$querySent = mysqli_query($conn, $sqlSent);
while ($row = mysqli_fetch_array($querySent)) {
    $branch = $row["branchID"];
    if (!isset($branchSent[$branch])) {
        $branchSent[$branch] = 0;
    }
    $branchSent[$branch]++;
}
$sentAccountant = intval(json_encode($branchSent[1] ?? 0));
$sentMarketing = intval(json_encode($branchSent[2] ?? 0));
$sentManagement = intval(json_encode($branchSent[3] ?? 0));
$sentLogistics = intval(json_encode($branchSent[4] ?? 0));
$sentDbt = intval(json_encode($branchSent[5] ?? 0));
$sentRetail = intval(json_encode($branchSent[6] ?? 0));
$sentIt = intval(json_encode($branchSent[7] ?? 0));
$sentForeignLanguage = intval(json_encode($branchSent[8] ?? 0));
$sentHotelManagement = intval(json_encode($branchSent[9] ?? 0));
$sentFood = intval(json_encode($branchSent[10] ?? 0));
$sentHomeEconomics = intval(json_encode($branchSent[11] ?? 0));
$sentFashion = intval(json_encode($branchSent[12] ?? 0));
$sentDesign = intval(json_encode($branchSent[13] ?? 0));
$sentDg = intval(json_encode($branchSent[14] ?? 0));
//ร้อยละ
$persentageAccountant = round(($sentAccountant / $accountant) * 100) ?? 0;
$persentageMarketing = round(($sentMarketing / $marketing) * 100) ?? 0;
$persentageManagement = round(($sentManagement / $management) * 100) ?? 0;
$persentageLogistics = round(($sentLogistics / $logistics) * 100) ?? 0;
$persentageDbt = round(($sentDbt / $dbt) * 100) ?? 0;
$persentageRetail = round(($sentRetail / $retail) * 100) ?? 0;
$persentageIt = round(($sentIt / $it) * 100) ?? 0;
$persentageForeingLanguage = round(($sentForeignLanguage / $foreignLanguage) * 100) ?? 0;
$persentageHotelManagement = round(($sentHotelManagement / $hotelManagement) * 100) ?? 0;
$persentageFood = round(($sentFood / $food) * 100) ?? 0;
$persentageHomeEconomics = round(($sentHomeEconomics / $homeEconomics) * 100) ?? 0;
$persentageFashion = round(($sentFashion / $fashion) * 100) ?? 0;
$persentageDesign = round(($sentDesign / $design) * 100) ?? 0;
$persentageDg = round(($sentDg / $dg) * 100) ?? 0;
// FOR SEARCH (BRANCH)
$sqlSearch = "SELECT * FROM branch";
$querySearch = mysqli_query($conn, $sqlSearch);
// ---------------------------------------------------------------------------------------------------
$termSelect = date('n');
if ($termSelect >= 5 && $termSelect <= 9) {
    $termSelect = 1;
} elseif ($termSelect >= 10) {
    $termSelect = 2;
}

// Today
if (isset($_GET['user'])) {
    $userID = $_GET['user'];

    $sql = "SELECT r.date, r.miss,r.missStudentName,r.all_student,r.note , subject.id AS subjectID, subject.subID AS SubID, subject.name AS subjectName, user.username AS username, user.id AS uesrid, teacher.name AS teacherName, t2.name AS insteadTeacherName
    FROM record r
    JOIN subject ON r.subject_id = subject.id
    JOIN user ON r.user_id = user.id
    JOIN teacher ON subject.teacher_id = teacher.id
    LEFT JOIN teacher t2 ON r.insteadTeacher = t2.id
    WHERE r.user_id = $userID
    AND r.date = CURDATE()
    AND r.term = $termSelect
    ORDER BY r.id ASC";
    $query = mysqli_query($conn, $sql);

    $dataToday = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $dataToday[] = $row;
    }

    $usernameChart = $dataToday[0]['username'] ?? 'ห้องนี้ยังไม่ข้อมูลของวันปัจจุบัน';

    $dataLables = [];
    $miss = [];
    $all = [];
    foreach ($dataToday as $today) {
        $dataLables[] = $today['subjectName'];
        $miss[] = $today['miss'];
        $all[] = $today['all_student'];
    }
    $total = array_map(function ($miss, $all) {
        return $all - $miss;
    }, $miss, $all);

    $ShowLabels = json_encode($dataLables ?? []);
    $missStudent = json_encode($miss ?? []);
    $allStudent = json_encode($all ?? []);
    $totall = json_encode($total ?? []);
}
// Week
if (isset($_GET['weekChart'])) {
    list($week, $userID) = explode(' ', $_GET['weekChart']);

    $sqlWeek = "SELECT r.date,r.miss,r.missStudentName, r.all_student,r.week, u.username, teacher.name AS teacherName
    FROM record r
    JOIN subject on r.subject_id = subject.id
    JOIN teacher ON subject.teacher_id = teacher.id
    JOIN user u ON r.user_id = u.id
    JOIN (
        SELECT record.date, MAX(record.id) AS max_id
        FROM record
        WHERE record.user_id = '$userID' AND record.week = '$week' AND record.term = $termSelect
        GROUP BY record.date
    ) latest
    ON r.id = latest.max_id
    ";
    $queryWeek = mysqli_query($conn, $sqlWeek);

    $dataWeek = [];
    while ($rowWeek = mysqli_fetch_array($queryWeek)) {
        $dataWeek[] = $rowWeek;
    }

    $usernameChart = $dataWeek[0]['username'] ?? '';

    $dataLables = [];
    $miss = [];
    $all = [];
    foreach ($dataWeek as $week) {
        $dataLables[] = convertToThaiDate($week['date']);
        $miss[] = $week['miss'];
        $all[] = $week['all_student'];
        $total[] = $week['all_student'] - $week['miss'];
    }
    $total[] = $week['all_student'] - $week['miss'];
    $total = array_map(function ($miss, $all) {
        return $all - $miss;
    }, $miss, $all);

    $ShowLabels = json_encode($dataLables ?? []);
    $missStudent = json_encode($miss ?? []);
    $allStudent = json_encode($all ?? []);
    $totall = json_encode($total ?? []);
}
// Term
if (isset($_GET['termChart'])) {
    list($term, $userID) = explode(' ', $_GET['termChart']);

    $sqlTerm = "
        SELECT r.week,r.term,r.miss,r.missStudentName,r.all_student, u.username
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE r.id IN (
            SELECT MAX(id)
            FROM record
            WHERE user_id = '$userID'
            AND term = $term
            AND (week IS NOT NULL)
            AND (date IN (
                SELECT MAX(date)
                FROM record
                WHERE user_id = '$userID'
                    AND term = $term
                GROUP BY week
            ))
            GROUP BY week
        )
        ORDER BY r.week ASC
        ";
    $queryTerm = mysqli_query($conn, $sqlTerm);


    $dataTerm = [];
    while ($rowTerm = mysqli_fetch_array($queryTerm)) {
        $dataTerm[] = $rowTerm;
    }

    $usernameChart = $dataTerm[0]['username'] ?? '';

    $dataLables = [];
    $miss = [];
    $all = [];
    foreach ($dataTerm as $Term) {
        $dataLables[] = "สัปดาห์ที่" . ' ' . $Term['week'];
        $miss[] = $Term['miss'];
        $all[] = $Term['all_student'];
        $total[] = $Term['all_student'] - $Term['miss'];
    }
    $total = array_map(function ($miss, $all) {
        return $all - $miss;
    }, $miss, $all);

    $ShowLabels = json_encode($dataLables ?? []);
    $missStudent = json_encode($miss ?? []);
    $allStudent = json_encode($all ?? []);
    $totall = json_encode($total ?? []);
}
// Data branch
if (isset($_GET['id']) || isset($_GET['weekBranch'])) {
    $dataBranch = true;

    // รับค่า branchID จาก URL หรือ session
    $branchID = isset($_GET['id']) ? intval($_GET['id']) : ($_SESSION['branch_id'] ?? null);
    $_SESSION['branch_id'] = $branchID;

    // ถ้ายังไม่มี weekBranch ให้ดึงสัปดาห์ล่าสุด
    if (!isset($_GET['weekBranch']) || empty($_GET['weekBranch'])) {
        $sqlLatestWeek = "SELECT MAX(week) as latestWeek FROM record";
        $resultLatest = mysqli_query($conn, $sqlLatestWeek);
        $weekBranch = 0;
        if ($row = mysqli_fetch_assoc($resultLatest)) {
            $weekBranch = intval($row['latestWeek']);
        }

        // redirect ไป URL ที่มี weekBranch
        header("Location: index.php?id=$branchID&weekBranch=$weekBranch");
        exit;
    }

    // ถ้าเลือก weekBranch มาแล้ว
    $weekBranch = intval($_GET['weekBranch']);

    // ชื่อสาขา
    $sqlBranch = "SELECT name FROM branch WHERE id = $branchID";
    $resultBranch = mysqli_query($conn, $sqlBranch);
    $branchName = '';
    if ($rowBranch = mysqli_fetch_assoc($resultBranch)) {
        $branchName = $rowBranch['name'];
    }

    // จำนวนผู้ใช้ทั้งหมดในสาขานั้น (ไม่รวม internship)
    $sqlTotal = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = $branchID AND status != 'internship'
    ";
    $resultTotal = mysqli_query($conn, $sqlTotal);
    $total = 0;
    if ($row = mysqli_fetch_assoc($resultTotal)) {
        $total = intval($row['total']);
    }

    // จำนวนที่ส่งแล้วในสัปดาห์นั้น
    $sqlSent = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = $branchID 
        AND u.status != 'internship'
        AND r.week = $weekBranch
    ";
    $resultSent = mysqli_query($conn, $sqlSent);

    $sent = 0;
    if ($row = mysqli_fetch_assoc($resultSent)) {
        $sent = intval($row['sent']);
    }

    $notSent = $total - $sent;
    $branchLabels = json_encode(['ส่งแล้ว', 'ยังไม่ส่ง']);
    $branchData = json_encode([$sent, $notSent]);

    // รายชื่อผู้ใช้และสถานะ (ส่งแล้ว/ยังไม่ส่ง)
    $sqlUsers = "
        SELECT u.id, u.username,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM record r 
                    WHERE r.user_id = u.id 
                    AND r.week = $weekBranch
                ) THEN 'ส่งแล้ว'
                ELSE 'ยังไม่ส่ง'
            END as status
        FROM user u
        WHERE u.branch_id = $branchID 
        AND u.status != 'internship'
        ORDER BY u.username ASC
    ";
    $resultUsers = mysqli_query($conn, $sqlUsers);

    $userStatusList = [];
    while ($row = mysqli_fetch_assoc($resultUsers)) {
        $userStatusList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการเรียน / สอน - หน้าหลัก</title>
    <link rel="shortcut icon" href="image/logo_nvc.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

</head>
<style>
    html,
    body {
        overflow-x: hidden;
    }

    @media only screen and (max-width: 500px) {
        .container {
            padding: 2rem;
            width: 100%;
            height: auto;
        }
    }
</style>

<body>
    <?php include("component/navbar.php") ?>
    <div class="d-flex">
        <?php if ($usertype == 'staff') {
            include './component/sidebar.php';
        } else {
            include './component/sidebar_user.php';
        }
        ?>
        <!-- STAFF PAGE -->
        <?php if ($usertype == 'staff') { ?>
            <div class="container-fluid m-2 p-4 border rounded-3" style="height: auto; width: 100%">
                <div class="d-flex">
                    <h5>หน้าหลัก</h5>
                    <script>
                        const first = 1;
                    </script>
                    <!-- ปริ้นแผนภูมิ (ภาพรวมทั้งหมดทุกสาขา) -->
                    <?php $week = isset($_GET['week']) ? $_GET['week'] : ''; ?>
                    <form id="pdfForm" method="POST" action="./docs/googleDoc_chart1.php" target="_blank" class="ms-auto me-2">
                        <input type="hidden" name="chartImageFile" id="chartImageFile">
                        <input type="hidden" name="week" value="<?php echo $week; ?>">
                        <button type="button" onclick="saveAndSubmit(first)" class="btn btn-sm btn-warning">ปริ้นแผนภูมิ</button>
                    </form>
                    <!-- ปริ้นเอกสารบันทึกความ (ภาพรวมทั้งหมดทุกสาขา) -->
                    <?php $week = isset($_GET['week']) ? $_GET['week'] : ''; ?>
                    <!-- ปุ่มที่ผู้ใช้สามารถกดเพื่อสร้างเอกสาร -->
                    <form action="./docs/googleDocs1.php" target="_blank" method="post">
                        <input type="hidden" name="week" value="<?php echo $week; ?>">
                        <button type="submit" class="btn btn-sm btn-primary">รายงานสรุปผล</button>
                    </form>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p>กราฟแสดงการส่งบันทึกการสอน <br>
                        ภาคเรียนที่ <?php echo $termSelect ?> ปีการศึกษา <?php echo Years($year); ?>
                    </p>
                    <!-- ดูการส่งบันทึกรูปแบบกราฟ เป็นรายสัปดาห์ของทุกสาขา -->
                    <div class="d-flex align-items-start">
                        <p class="pe-3">สัปดาห์ที่</p>
                        <form action="" method="get">
                            <select name="week" id="week" class="form-select form-sm w-auto" onchange="this.form.submit()">
                                <option value="" selected disable   d>เลือก</option>
                                <?php
                                $sqlWeek = "SELECT DISTINCT week FROM record ORDER BY record.week + 0 DESC";
                                $queryWeek = mysqli_query($conn, $sqlWeek);
                                while ($rowWeek = mysqli_fetch_array($queryWeek)) {
                                ?>
                                    <option value="<?php echo $rowWeek['week'] ?>" <?php echo ($week == $rowWeek['week'] ? 'selected' : '') ?>><?php echo $rowWeek['week'] ?></option>
                                <?php } ?>
                            </select>
                        </form>
                    </div>
                </div>

                <p class="text-center">แผนภูมิแสดงการส่งบันทึกการเรียน / การสอน</p>
                <canvas id="myChart" width="600" height="200"></canvas>
                <script>
                    const labels = <?php echo $branchs; ?>;
                    const data = {
                        labels: labels,
                        datasets: [{
                                label: 'จำนวนเต็ม',
                                data: [<?php echo $accountant ?>, <?php echo $marketing ?>, <?php echo $management ?>, <?php echo $logistics ?>, <?php echo $dbt ?>, <?php echo $retail ?>, <?php echo $it ?>, <?php echo $foreignLanguage ?>, <?php echo $hotelManagement ?>, <?php echo $food ?>, <?php echo $homeEconomics ?>, <?php echo $fashion ?>, <?php echo $design ?>, <?php echo $dg ?>],
                                borderColor: 'rgb(245, 75, 75)',
                                backgroundColor: 'rgb(241, 154, 154)',
                            },
                            {
                                label: 'จำนวนห้องที่ส่ง',
                                data: [<?php echo $sentAccountant ?>, <?php echo $sentMarketing ?>, <?php echo $sentManagement ?>, <?php echo $sentLogistics ?>, <?php echo $sentDbt ?>, <?php echo $sentRetail ?>, <?php echo $sentIt ?>, <?php echo $sentForeignLanguage ?>, <?php echo $sentHotelManagement ?>, <?php echo $sentFood ?>, <?php $sentHomeEconomics ?>, <?php echo $sentFashion ?>, <?php echo $sentDesign ?>, <?php echo $sentDg ?>],
                                borderColor: 'rgb(245, 190, 72)',
                                backgroundColor: 'rgb(232, 206, 151)',
                            },
                            {
                                label: 'ร้อยละ',
                                data: [<?php echo $persentageAccountant ?>, <?php echo $persentageMarketing ?>, <?php echo $persentageManagement ?>, <?php echo $persentageLogistics ?>, <?php echo $persentageDbt ?>, <?php echo $persentageRetail ?>, <?php echo $persentageIt ?>, <?php echo $persentageForeingLanguage ?>, <?php echo $persentageHotelManagement ?>, <?php echo $persentageFood ?>, <?php echo $persentageHomeEconomics ?>, <?php echo $persentageFashion ?>, <?php echo $persentageDesign ?>, <?php echo $persentageDg ?>],
                                borderColor: 'rgb(129, 225, 129)',
                                backgroundColor: 'rgb(175, 236, 175)',
                            }
                        ]
                    };


                    const config = {
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    min: 0,
                                    max: 120,
                                    ticks: {
                                        stepSize: 20
                                    }
                                }
                            }
                        }
                    };

                    new Chart(document.getElementById('myChart'), config);
                </script>
                <hr>
                <script>
                    function saveAndSubmit(first) {
                        document.getElementById('chartImageFile').value = first;
                        const canvas = document.getElementById('myChart');
                        const image = canvas.toDataURL('image/png');
                        const formData = new FormData();
                        formData.append('imageData', image);
                        formData.append('paramiter', first);

                        fetch('saveChartImage.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.text())
                            .then(filename => {
                                if (filename.startsWith("chart_")) {
                                    // กำหนดชื่อไฟล์ลงใน hidden input แล้ว submit form
                                    document.getElementById('chartImageFile').value = filename;
                                    document.getElementById('pdfForm').submit();
                                } else {
                                    alert("เกิดข้อผิดพลาด: " + filename);
                                }
                            })
                            .catch(err => {
                                console.error('Upload failed:', err);
                                alert("ไม่สามารถบันทึกภาพได้");
                            });
                    }
                </script>

                <div class="d-flex mb-2">
                    <h5>สถิติแต่ละห้อง / สาขา</h5>
                    <?php if (isset($_GET['id'])) { ?>
                        <!-- ปริ้นแผนภูมิ (ข้อมูลแต่ละสาขา) -->
                        <script>
                            const second = 2;
                        </script>
                        <?php $week = isset($_GET['weekBranch']) ? $_GET['weekBranch'] : ''; ?>
                        <form id="pdfFormSec" method="POST" action="./docs/googleDoc_chart2.php" target="_blank" class="ms-auto me-2">
                            <input type="hidden" name="chartImageFile2" id="chartImageFile2">
                            <input type="hidden" name="branchID" value="<?php echo $branchID; ?>">
                            <input type="hidden" name="week" value="<?php echo $week; ?>">
                            <button type="button" onclick="saveAndSubmit(second)" class="btn btn-sm btn-warning">ปริ้นแผนภูมิ</button>
                        </form>
                        <!-- ปริ้นเอกสารบันทึกความ (ข้อมูลแต่ละสาขา) -->
                        <?php $week = isset($_GET['weekBranch']) ? $_GET['weekBranch'] : ''; ?>
                        <?php $branchID = isset($_GET['id']) ? $_GET['id'] : ''; ?>
                        <form action="./docs/googleDocs2.php" target="_blank" method="post">
                            <input type="hidden" name="week" value="<?php echo $week; ?>">
                            <input type="hidden" name="branchID" value="<?php echo $branchID; ?>">
                            <button type="submit" class="btn btn-sm btn-primary">รายงานสรุปผล</button>
                        </form>
                    <?php } ?>
                </div>
                <div class="d-flex">
                    <div class="d-flex ms-auto">
                        <div class="d-block">
                            <form method="get" action="./backend/dataBranch.php" class="ps-2">
                                <select name="branch" id="branch" class="form-select">
                                    <option value="" selected disabled>-- เลือกสาขา --</option>
                                    <?php
                                    $sql = "SELECT * FROM branch WHERE name != 'สามัญสัมพันธ์'";
                                    $query = mysqli_query($conn, $sql);
                                    while ($row = mysqli_fetch_array($query)) {
                                    ?>
                                        <option value="<?php echo $row['id'] ?>" <?php if (isset($_GET['branch']) && $_GET['branch'] == $row['id']) echo 'selected'; ?>><?php echo $row['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <button class="btn btn-primary w-100 btn-sm mt-2" type="submit" name="searchDataBranch">ค้นหาเฉพาะสาขา</button>
                            </form>
                        </div>
                        <div>
                            <form action="" method="get" class="ms-2">
                                <select name="user" id="user" class="form-select">
                                    <option value="" selected disabled>-- กรุณาเลือกสาขาก่อน --</option>
                                </select>
                                <button class="btn btn-primary w-100 btn-sm mt-2" type="submit" name="searchDataBranch">ค้นหาผู้ใช้งาน</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- ข้อมูลแต่ละสาขา เช็คว่าส่งหรือไม่ส่งเท่าไหร่ -->
                <br>
                <?php if (isset($_GET['id'])) { ?>
                    <h6 class="text-center"><?php if (empty($branchName)) {
                        echo 'กรุณาเลือกสาขาก่อน';
                    } else {
                        echo "สาขา" . " " . "<strong>" . $branchName . "</strong>";
                    } ?>
                    </h6>
                    <div class="d-flex justify-content-center">
                        <div class="mx-3">
                            เลือกดูรายสัปดาห์
                            <?php $branch = $_GET['branch'] ?? ''; ?>
                            <form action="" method="get" style="width: 100%;">
                                <input type="hidden" name="id" value="<?php echo $branchID; ?>">
                                <select name="weekBranch" id="weekBranch" class="form-select" onchange="this.form.submit()">
                                    <option value="" disabled <?php echo empty($weekBranch) ? 'selected' : ''; ?>>-- เลือก --</option>
                                    <?php
                                    $sqlWeeks = "SELECT DISTINCT week FROM record ORDER BY week DESC";
                                    $resultWeeks = mysqli_query($conn, $sqlWeeks);
                                    while ($rowWeek = mysqli_fetch_assoc($resultWeeks)) {
                                        $week = $rowWeek['week'];
                                        $selected = ($weekBranch == $week) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $week; ?>" <?php echo $selected; ?>><?php echo $week; ?></option>
                                    <?php } ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <canvas id="dataBranch" width="1000" height="300"></canvas>
                    <script>
                        const labelsDataBranch = <?php echo $branchLabels ?? '[]'; ?>;
                        const dataDataBranch = {
                            labels: labelsDataBranch,
                            datasets: [{
                                label: 'จำนวนผู้ใช้',
                                data: <?php echo $branchData ?? '[]'; ?>,
                                backgroundColor: [
                                    'rgb(129, 225, 129)', // ส่งแล้ว
                                    'rgb(245, 190, 72)' // ยังไม่ส่ง
                                ],
                                borderColor: [
                                    'rgb(75, 150, 75)',
                                    'rgb(200, 150, 50)'
                                ],
                            }]
                        };

                        const configBranch = {
                            type: 'bar',
                            data: dataDataBranch,
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'สรุปการส่งบันทึกในสาขา'
                                    },
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        max: 15,
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 5
                                        }
                                    }
                                }
                            }
                        };
                        new Chart(document.getElementById('dataBranch'), configBranch);
                    </script>
                    <script>
                        function saveAndSubmit(second) {
                            document.getElementById('chartImageFile2').value = second;
                            const canvas = document.getElementById('dataBranch');
                            const image = canvas.toDataURL('image/png');
                            const formData = new FormData();
                            formData.append('imageDataSec', image);
                            formData.append('paramiterSec', second);

                            fetch('saveChartImage.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(resp => resp.text())
                                .then(filenameSec => {
                                    if (filenameSec.startsWith("chart_")) {
                                        // กำหนดชื่อไฟล์ลงใน hidden input แล้ว submit form
                                        document.getElementById('chartImageFile2').value = filenameSec;
                                        document.getElementById('pdfFormSec').submit();
                                    } else {
                                        alert("เกิดข้อผิดพลาด: " + filenameSec);
                                    }
                                })
                                .catch(err => {
                                    console.error('Upload failed:', err);
                                    alert("ไม่สามารถบันทึกภาพได้");
                                });
                        }
                    </script>

                    <!-- ข้อมูลของผู้ใช้งาน วันนี้ / สัปดาห์นี้ / เทอมนี้-->
                <?php } elseif (isset($_GET['user']) || isset($_GET['weekChart']) || isset($_GET['termChart'])) { ?>
                    <div class="text-center">
                        <h6>สถิติของห้อง: <strong><?php echo $usernameChart ?? null;
                                                    ?></strong></h6>
                        <!-- เลือกดูรายสัปดาห์ -->
                        <div class="d-flex justify-content-center my-1">
                            <div class="mx-3">
                                เลือกดูรายสัปดาห์
                                <form action="" method="get" class="mt-1">
                                    <select name="weekChart" id="weekChart" class="form-select w-100" onchange="this.form.submit()">
                                        <option value="" selected disabled>เลือกดูรายสัปดาห์</option>
                                        <?php
                                        $sql = "SELECT DISTINCT week FROM record WHERE user_id = $userID ORDER by record.week DESC";
                                        $query = mysqli_query($conn, $sql);
                                        while ($row = mysqli_fetch_array($query)) {
                                        ?>
                                            <option value="<?php echo $row['week'] . ' ' . $userID ?>"><?php echo $row['week'] ?></option>
                                        <?php } ?>
                                    </select>
                                </form>
                            </div>
                            <div>
                                <!-- เลือกดูรายเทอม -->
                                เลือกดูรายเทอม
                                <form action="" method="get" class="mt-1">
                                    <select name="termChart" id="termChart" class="form-select w-100" onchange="this.form.submit()">
                                        <option value="" selected disabled>เลือกดูรายเทอม</option>
                                        <?php
                                        $sql = "SELECT DISTINCT term FROM record WHERE user_id = $userID ORDER by record.term ASC";
                                        $query = mysqli_query($conn, $sql);
                                        while ($row = mysqli_fetch_array($query)) {
                                        ?>
                                            <option value="2 <?php echo $row['term'] . ' ' . $userID ?>"><?php echo $row['term'] ?></option>
                                        <?php } ?>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <canvas id="data" width="1000" height="300"></canvas>
                    <script>
                        const labelsToday = <?php echo $ShowLabels ?? '[]' ?>;
                        const dataMiss = <?php echo $missStudent ?? '[]' ?>;
                        const dataAll = <?php echo $allStudent ?? '[]' ?>;
                        const dataCome = <?php echo $totall ?? '[]' ?>;
                        const dataDataToday = {
                            labels: labelsToday,
                            datasets: [{
                                    label: 'ขาดเรียน',
                                    data: dataMiss,
                                    backgroundColor: 'rgb(245, 190, 72)',
                                    borderColor: 'rgb(200, 150, 50)',
                                },
                                {
                                    label: 'มาเรียน',
                                    data: dataCome,
                                    backgroundColor: 'rgb(129, 225, 129)',
                                    borderColor: 'rgb(75, 150, 75)',
                                },
                                {
                                    label: 'ทั้งหมด',
                                    data: dataAll,
                                    borderColor: 'rgb(76, 92, 243)',
                                    backgroundColor: 'rgba(76, 92, 243, 0.5)',
                                }
                            ]
                        };

                        const configToday = {
                            type: 'bar',
                            data: dataDataToday,
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        display: true
                                    },
                                },
                                scales: {
                                    y: {
                                        max: 40,
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 10
                                        }
                                    }
                                }
                            }
                        };
                        new Chart(document.getElementById('data'), configToday);
                    </script>
                <?php } ?>
                <hr>
                <br>
                <!-- รายละเอียดจากกราฟ -------------------------------------------------------------------------- -->
                <!-- ข้อมูลตามสาขา -->
                <?php if (isset($dataBranch)) { ?>
                    <h5>รายละเอียดจากกราฟ</h5>
                    <table class="table table-responsive">
                        <tr class="table table-info">
                            <th>ผู้ใช้งาน</th>
                            <th>สถานะ</th>
                        </tr>
                        <?php foreach ($userStatusList as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td class="<?= $user['status'] === 'ส่งแล้ว' ? 'text-success' : 'text-danger' ?>">
                                    <?= $user['status'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <!-- ข้อมูลวันนี้ -->
                <?php } elseif (isset($dataToday)) { ?>
                    <h5 class="mb-3">รายละเอียดจากกราฟ</h5>
                    <div class="table table-responsive">
                        <table class="table table-bordered table-sm">
                            <tr class="table table-info ">
                                <th>วันที่</th>
                                <th>รหัสวิชา</th>
                                <th>ชื่อวิชา</th>
                                <th>ครูผู้สอน</th>
                                <th class="text-danger">ขาดเรียน</th>
                                <th class="text-danger">รายชื่อนักเรียนขาดเรียน</th>
                                <th class="text-success">มาเรียน</th>
                                <th>ทั้งหมด</th>
                                <th class="text-danger">หมายเหตุ</th>

                            </tr>
                            <?php
                            foreach ($dataToday as $rowDetail):
                                $comeStudent = $rowDetail['all_student'] - $rowDetail['miss']; ?>
                                <tr>
                                    <td><?php echo convertToThaiDate($rowDetail['date']); ?></td>
                                    <td><?php echo $rowDetail['SubID'] ?></td>
                                    <td><?php echo $rowDetail['subjectName'] ?></td>
                                    <td><?php echo $rowDetail['teacherName'] ?></td>
                                    <td><?php echo $rowDetail['miss'] ?></td>
                                    <td><?php echo $rowDetail['missStudentName'] ?></td>
                                    <td><?php echo $comeStudent ?></td>
                                    <td><?php echo $rowDetail['all_student'] ?></td>
                                    <td><?php
                                        if ($rowDetail['note'] == 'เข้าสอนปกติ') {
                                            echo '<span class="text-success">เข้าสอนปกติ</span>';
                                        } elseif ($rowDetail['note'] == 'สอนแทน') {
                                            echo '<span class="text-warning">ครูสอนแทน</span>';
                                        } ?>
                                        <br>
                                        <?php
                                        if ($rowDetail['note'] === 'สอนแทน' && !empty($rowDetail['teacherName'])) {
                                            echo $rowDetail['insteadTeacherName'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <!-- ข้อมูลสัปดาห์นี้ -->
                <?php
                } elseif (isset($dataWeek)) {
                ?>
                    <h5 class="mb-3">รายละเอียดจากกราฟ สัปดาห์ที่
                        <?php
                        foreach ($dataWeek as $week) {
                            echo $week['week'];
                            break;
                        }
                        ?>
                    </h5>
                    <div class="table table-responsive">
                        <table class="table table-bordered table-sm">
                            <tr class="table table-info ">
                                <th>วันที่</th>
                                <th class="text-danger">ขาดเรียน</th>
                                <th class="text-danger">รายชื่อนักเรียนขาดเรียน</th>
                                <th class="text-success">มาเรียน</th>
                                <th>ทั้งหมด</th>
                            </tr>
                            <?php
                            foreach ($dataWeek as $row):
                                $come = $row['all_student'] - $row['miss'];
                            ?>
                                <tr>
                                    <td><?php echo convertToThaiDate($row['date']); ?></td>
                                    <td><?php echo $row['miss'] ?></td>
                                    <td><?php echo $row['missStudentName'] ?></td>
                                    <td><?php echo $come ?></td>
                                    <td><?php echo $row['all_student'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <!-- show data term -->
                <?php } elseif (isset($dataTerm)) {
                ?>
                    <h5 class="mb-3">รายละเอียดจากกราฟ ภาคเรียนที่
                        <?php
                        foreach ($dataTerm as $term) {
                            echo $term['term'];
                            break;
                        }
                        ?>
                    </h5>
                    <div class="table table-responsive">
                        <table class="table table-bordered table-sm">
                            <tr class="table table-info ">
                                <th>สัปดาห์ที่</th>
                                <th class="text-danger">ขาดเรียน</th>
                                <th class="text-danger">รายชื่อนักเรียนขาดเรียน</th>
                                <th class="text-success">มาเรียน</th>
                                <th>ทั้งหมด</th>
                            </tr>
                            <?php
                            foreach ($dataTerm as $row):
                                $come = $row['all_student'] - $row['miss'];
                            ?>
                                <tr>
                                    <td><?php echo $row['week']; ?></td>
                                    <td><?php echo $row['miss'] ?></td>
                                    <td><?php echo $row['missStudentName'] ?></td>
                                    <td><?php echo $come ?></td>
                                    <td><?php echo $row['all_student'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php } ?>
            </div>
    </div>
<?php } elseif ($usertype == 'user') { ?>
    <!-- USER PAGE -->
    <div class="container m-2 p-4 border rounded-3" style="height: auto; width: 100%">
        <h5>หน้าหลัก</h5>
        <hr>
        <div class="d-flex justify-content-between">
            <p>สถิติแต่ละวันและวิชาในแต่ละวัน <br>
            ภาคเรียนที่ <?php echo $termSelect ?> ปีการศึกษา <?php echo Years($year); ?>
            </p>
        </div>
        <br>
        <h5 class="text-center">สถิติ</h5>
        <p class="text-center">วันที่ <?php echo convertToThaiDate(date('Y-m-d')); ?></p>
        <div class="d-flex justify-content-center">
            <canvas id="userChart" width="900" height="300"></canvas>
        </div>
        <script>
            const labels = <?php echo $subjectName; ?>;
            const data = {
                labels: labels,
                datasets: [{
                        label: 'ขาดเรียน',
                        data: <?php echo $miss ?>,
                        borderColor: 'rgb(245, 75, 75)',
                        backgroundColor: 'rgb(241, 154, 154)',
                    },
                    {
                        label: 'มาเรียน',
                        data: <?php echo $totallStudent ?>,
                        borderColor: 'rgb(129, 225, 129)',
                        backgroundColor: 'rgb(175, 236, 175)',
                    },
                    {
                        label: 'นักเรียนทั้งหมด',
                        data: <?php echo $all ?>,
                        borderColor: 'rgb(75, 75, 245)',
                        backgroundColor: 'rgb(137, 137, 250)',
                    }
                ]
            };


            const UserCheckConfig = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 40,
                            ticks: {
                                stepSize: 10
                            }
                        }
                    }
                }
            };
            const chart = new Chart(document.getElementById('userChart'), UserCheckConfig);
        </script>
    <?php } ?>
</body>

</html>
<!-- JS HERE -->
<script src="assets/js/wanting.js"></script>
<script>
    // run user form select branch
    $(document).on("change", '#branch', function() {
        let branchID = $(this).val();
        let formData = new FormData();
        let $users = $('#user');
        // console.log(branchID);

        formData.append("branchID", branchID);
        $.ajax({
            url: "./backend/selectUserFromIndex.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                // console.log(data);

                $users.empty().append('<option value="" selected disabled>-- เลือกผู้ใช้ --</option>');
                $.each(data, function(index, user) {
                    $users.append(
                        '<option value="' + user.id + '">' + user.username + '</option>'
                    );
                })
            }
        })
    })
</script>