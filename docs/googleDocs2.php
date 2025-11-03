<?php
include '../backend/db.php';  // ตรวจสอบให้แน่ใจว่าการเชื่อมต่อกับฐานข้อมูลทำงานได้ถูกต้อง
include 'google_docs_client2.php';  // เชื่อมต่อกับฟังก์ชัน Google Docs API

// ตรวจสอบค่าที่รับมาจากฟอร์ม
if (isset($_POST['week']) && isset($_POST['branchID'])) {
    $week = $_POST['week'];  // รับค่าจากฟอร์ม
    $branchID = $_POST['branchID'];  // รับค่าจากฟอร์ม
} else {
    die("Error: Missing week.");
}

$sqlBranch = "SELECT name FROM branch WHERE id = $branchID";
$queryBranch = mysqli_query($conn , $sqlBranch);
$rowBranch = mysqli_fetch_array($queryBranch);
// FOR DATE
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
$date = [
    "day" => date("d"),
    "month" => $thai_months[date("m")],
    "year" => date("Y") + 543
];
// TERM
$termSelect = date('n');
if ($termSelect >= 5 && $termSelect <= 9) {
    $termSelect = 1;
} elseif ($termSelect >= 10) {
    $termSelect = 2;
}
// ดึงข้อมูลสาขาและสัปดาห์
if (!empty($branchID) && !empty($week)) {
    $sqlBranch = "SELECT branch.name,CONCAT(teacher.name, ' ', teacher.lastname) AS teacherName FROM branch JOIN teacher ON branch.teacher_id = teacher.id WHERE branch.id = $branchID";
    $queryBranch = mysqli_query($conn, $sqlBranch);

    if ($row = mysqli_fetch_array($queryBranch)) {
        $branchName = $row['name'];
        $teacherName = $row['teacherName'];
    }

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
        AND r.week = $week
    ";
    $resultSent = mysqli_query($conn, $sqlSent);

    $sent = 0;
    if ($row = mysqli_fetch_assoc($resultSent)) {
        $sent = intval($row['sent']);
    }

    $notSent = $total - $sent;
}

// สร้างข้อมูลที่จะส่งไปยัง Google Docs
$data = [
    'day' => $date['day'],
    'month' => $date['month'],
    'year' => $date['year'],
    'termSelect' => $termSelect,
    'week' => $week,
    'branchName' => $branchName,
    'sent' => $sent,
    'notSent' => $notSent,
    'teacherName' => $teacherName
];

try {
    // สร้างเอกสารใน Google Docs จาก Template
    $documentId = createGoogleDoc($data);

    if ($documentId) {
        echo "<script>window.location.href='https://docs.google.com/document/d/$documentId/edit'</script>";
    } else {
        echo "เกิดข้อผิดพลาดในการสร้างเอกสาร";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
