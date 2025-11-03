<?php
include '../backend/db.php';  // ตรวจสอบให้แน่ใจว่าการเชื่อมต่อกับฐานข้อมูลทำงานได้ถูกต้อง
include 'google_docs_client4.php';  // เชื่อมต่อกับฟังก์ชัน Google Docs API

// ตรวจสอบค่าที่รับมาจากฟอร์ม
if (isset($_POST['week']) && isset($_POST['branchID'])) {
    $week = $_POST['week'];
    $branchID = $_POST['branchID'];
} else {
    die("Error: Missing week.");
}

$imagePath = '../image/chart/chart_2.png';
if (file_exists($imagePath)) {
    $imageData = base64_encode(file_get_contents($imagePath));
    $imgSrc = 'data:image/png;base64,' . $imageData;
} else {
    $imgSrc = '';
}

$sqlBranch = "SELECT name FROM branch WHERE id = $branchID";
$queryBranch = mysqli_query($conn, $sqlBranch);
$row = mysqli_fetch_array($queryBranch);

// ดึงสถานะการส่งของ user
$sqlUsers = "
    SELECT u.id, u.username,
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM record r WHERE r.user_id = u.id AND r.week = '$week'
            )
            THEN 'ส่งแล้ว'
            ELSE 'ยังไม่ส่ง'
        END as status
    FROM user u
    WHERE u.branch_id = $branchID AND u.status != 'internship'
    ORDER BY u.username ASC
";
$resultUsers = mysqli_query($conn, $sqlUsers);
$userStatusList = [];

while($rowUser = mysqli_fetch_assoc($resultUsers)) {
    // เก็บเป็น array
    $userStatusList[] = [
        'username' => $rowUser['username'],
        'status' => $rowUser['status']
    ];
}

$statusUsersText = implode("\n", array_map(function($user) {
    return $user['username'] . " " . $user['status'];
}, $userStatusList));

function generateStatusTableText($userStatusList) {
    $header = "ชื่อ\t\t\t\t\t\tสถานะ\n" . str_repeat("-", 70) . "\n";
    $rows = "";

    foreach ($userStatusList as $user) {
        $rows .= $user['username'] . "\t\t\t\t\t\t" . $user['status'] . "\n";
    }

    return $header . $rows;
}

$statusUsersText = generateStatusTableText($userStatusList);

$data = [
    'week' => $week,
    'branchName' => $row['name'],
    'status_users' => $statusUsersText
];

$docIdFile = __DIR__ . "/รายงานสรุปภาพรวมบันทึกการเรียนการสอน.txt";

try {
    // สร้างเอกสารใน Google Docs จาก Template
    $documentId = createGoogleDoc($data);

    if ($documentId) {
        // แสดงลิงก์ไปยังเอกสาร Google Docs ที่สร้างขึ้น
        echo "<script>window.location.href = 'https://docs.google.com/document/d/$documentId/edit';</script>";
    } else {
        echo "เกิดข้อผิดพลาดในการสร้างเอกสาร";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
