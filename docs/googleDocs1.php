<?php
include '../backend/db.php';  // ตรวจสอบให้แน่ใจว่าการเชื่อมต่อกับฐานข้อมูลทำงานได้ถูกต้อง
include 'google_docs_client.php';  // เชื่อมต่อกับฟังก์ชัน Google Docs API

// ตรวจสอบค่าที่รับมาจากฟอร์ม
if (isset($_POST['week'])) {
    $week = $_POST['week'];  // รับค่าจากฟอร์ม
} else {
    die("Error: Missing week.");
}

$sqlBranches = "
    SELECT 
        branch.id, 
        branch.name AS branchName
    FROM branch
";
$queryBranches = mysqli_query($conn, $sqlBranches);

$allBranchData = [];

$accountant = null;

while ($rowBranch = mysqli_fetch_assoc($queryBranches)) {
    $branchID = $rowBranch['id'];
    $branchName = $rowBranch['branchName'];
    // แยกชื่อตามสาขา
    if ($branchID == 1) {
        $accountant = $branchName;
    }elseif($branchID == 2){
        $marketing = $branchName;
    }
    elseif($branchID == 3){
        $management = $branchName;
    }
    elseif($branchID == 4){
        $logistics = $branchName;
    }
    elseif($branchID == 5){
        $dbt = $branchName;
    }
    elseif($branchID == 6){
        $retail = $branchName;
    }
    elseif($branchID == 7){
        $it = $branchName;
    }
    elseif($branchID == 8){
        $foreignLanguage = $branchName;
    }
    elseif($branchID == 9){
        $hotelManagement = $branchName;
    }
    elseif($branchID == 10){
        $food = $branchName;
    }
    elseif($branchID == 11){
        $homeEconomics = $branchName;
    }
    elseif($branchID == 12){
        $fashion = $branchName;
    }
    elseif($branchID == 13){
        $design = $branchName;
    }
    elseif($branchID == 14){
        $dg = $branchName;
    }
    // ทั้งวิทลัย
    $sqlAll = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE status != 'internship'
    ";
    $queryAll = mysqli_query($conn, $sqlAll);
    $totalAll = 0;
    if ($resAll = mysqli_fetch_assoc($queryAll)) {
        $totalAll = intval($resAll['total']);
    }
    // บัญชี
    $sqlAccount = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 1 AND status != 'internship'
    ";
    $queryAccount = mysqli_query($conn, $sqlAccount);
    $totalAccount = 0;
    if ($resAccount = mysqli_fetch_assoc($queryAccount)) {
        $totalAccount = intval($resAccount['total']);
    }
    // การตลาด
    $sqlMarketing = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 2 AND status != 'internship'
    ";
    $queryMarketing = mysqli_query($conn, $sqlMarketing);
    $totalMarketing = 0;
    if ($resMerketing = mysqli_fetch_assoc($queryMarketing)) {
        $totalMarketing = intval($resMerketing['total']);
    }
    // การจัดการสำนักงาน
    $sqlManagement = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 3 AND status != 'internship'
    ";
    $queryManagement = mysqli_query($conn, $sqlManagement);
    $totalManagement = 0;
    if ($resManagement = mysqli_fetch_assoc($queryManagement)) {
        $totalManagement = intval($resManagement['total']);
    }
    // โลจิสติกส์
    $sqlLogistics = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 4 AND status != 'internship'
    ";
    $queryLogistics = mysqli_query($conn, $sqlLogistics);
    $totalLogistics = 0;
    if ($resLogistics = mysqli_fetch_assoc($queryLogistics)) {
        $totalLogistics = intval($resLogistics['total']);
    }
    // เทคโนโลยีธุรกิจดิจิทัล
    $sqlDbt = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 5 AND status != 'internship'
    ";
    $queryDbt = mysqli_query($conn, $sqlDbt);
    $totalDbt = 0;
    if ($resDbt = mysqli_fetch_assoc($queryDbt)) {
        $totalDbt = intval($resDbt['total']);
    }
    // ธุรกิจค้าปลีก
    $sqlRetail = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 6 AND status != 'internship'
    ";
    $queryRetail = mysqli_query($conn, $sqlRetail);
    $totalRetail = 0;
    if ($resRetail = mysqli_fetch_assoc($queryRetail)) {
        $totalRetail = intval($resRetail['total']);
    }
    // เทคโนโลยีสารสนเทศ
    $sqlIt = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 7 AND status != 'internship'
    ";
    $queryIt = mysqli_query($conn, $sqlIt);
    $totalIt = 0;
    if ($resIt = mysqli_fetch_assoc($queryIt)) {
        $totalIt = intval($resIt['total']);
    }
    // ภาษาต่างประเทศ
    $sqlForeignLanguage = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 8 AND status != 'internship'
    ";
    $queryForeignLanguage = mysqli_query($conn, $sqlForeignLanguage);
    $totalForeignLanguage = 0;
    if ($resForeignLanguage = mysqli_fetch_assoc($queryForeignLanguage)) {
        $totalForeignLanguage = intval($resForeignLanguage['total']);
    }
    // การโรงแรม
    $sqlHotelManagement = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 9 AND status != 'internship'
    ";
    $queryHotelManagement = mysqli_query($conn, $sqlHotelManagement);
    $totalHotelManagement = 0;
    if ($resHotelManagement = mysqli_fetch_assoc($queryHotelManagement)) {
        $totalHotelManagement = intval($resHotelManagement['total']);
    }
    // อาหารและโภชนาการ
    $sqlFood = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 10 AND status != 'internship'
    ";
    $queryFood = mysqli_query($conn, $sqlFood);
    $totalFood = 0;
    if ($resFood = mysqli_fetch_assoc($queryFood)) {
        $totalFood = intval($resFood['total']);
    }
    // คหกรรม
    $sqlHomeEconomics = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 11 AND status != 'internship'
    ";
    $queryHomeEconomics = mysqli_query($conn, $sqlHomeEconomics);
    $totalHomeEconomics = 0;
    if ($resHomeEconomics = mysqli_fetch_assoc($queryHomeEconomics)) {
        $totalHomeEconomics = intval($resHomeEconomics['total']);
    }
    // แฟชั่นและสิ่งทอ
    $sqlFashion = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 12 AND status != 'internship'
    ";
    $queryFashion = mysqli_query($conn, $sqlFashion);
    $totalFashion = 0;
    if ($resFashion = mysqli_fetch_assoc($queryFashion)) {
        $totalFashion = intval($resFashion['total']);
    }
    // การออกแบบ
    $sqlDesign = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 13 AND status != 'internship'
    ";
    $queryDesign = mysqli_query($conn, $sqlDesign);
    $totalDesign = 0;
    if ($resDesign = mysqli_fetch_assoc($queryDesign)) {
        $totalDesign = intval($resDesign['total']);
    }
    // ดิจิทัลกราฟิก
    $sqlDg = "
        SELECT COUNT(*) as total 
        FROM user 
        WHERE branch_id = 14 AND status != 'internship'
    ";
    $queryDg = mysqli_query($conn, $sqlDg);
    $totalDg = 0;
    if ($resDg = mysqli_fetch_assoc($queryDg)) {
        $totalDg = intval($resDg['total']);
    }

    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น ทั้งวิทลัย
    $sqlSentAll = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentAll = mysqli_query($conn, $sqlSentAll);
    $sentAll = 0;
    if ($resAll = mysqli_fetch_assoc($resultSentAll)) {
        $sentAll = intval($resAll['sent']);
    }
    $notSentAll = $totalAll - $sentAll;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาบัญชี
    $sqlSentAccount = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 1 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentAccount = mysqli_query($conn, $sqlSentAccount);
    $sentAccount = 0;
    if ($resAccount = mysqli_fetch_assoc($resultSentAccount)) {
        $sentAccount = intval($resAccount['sent']);
    }
    $notSentAccount = $totalAccount - $sentAccount;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาการตลาด
    $sqlSentMarketing = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 2 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentMarketing = mysqli_query($conn, $sqlSentMarketing);
    $sentMarketing = 0;
    if ($resMarketing = mysqli_fetch_assoc($resultSentMarketing)) {
        $sentMarketing = intval($resMarketing['sent']);
    }
    $notSentMarketing = $totalMarketing - $sentMarketing;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาการจัดการสำนักงาน
    $sqlSentManagement = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 3 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentManagement = mysqli_query($conn, $sqlSentManagement);
    $sentManagement = 0;
    if ($resManagement = mysqli_fetch_assoc($resultSentManagement)) {
        $sentManagement = intval($resManagement['sent']);
    }
    $notSentManagement = $totalManagement - $sentManagement;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาโลจิสติกส์
    $sqlSentLogistics = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 4 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentLogistics = mysqli_query($conn, $sqlSentLogistics);
    $sentLogistics = 0;
    if ($resLogistics = mysqli_fetch_assoc($resultSentLogistics)) {
        $sentLogistics = intval($resLogistics['sent']);
    }
    $notSentLogistics = $totalLogistics - $sentLogistics;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาเทคโนโลยีธุรกิจดิจิทัล
    $sqlSentDbt = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 5 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentDbt = mysqli_query($conn, $sqlSentDbt);
    $sentDbt = 0;
    if ($resDbt = mysqli_fetch_assoc($resultSentDbt)) {
        $sentDbt = intval($resDbt['sent']);
    }
    $notSentDbt = $totalDbt - $sentDbt;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาธุรกิจค้าปลีก
    $sqlSentRetail = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 6 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentRetail = mysqli_query($conn, $sqlSentRetail);
    $sentRetail = 0;
    if ($resRetail = mysqli_fetch_assoc($resultSentRetail)) {
        $sentRetail = intval($resRetail['sent']);
    }
    $notSentRetail = $totalRetail - $sentRetail;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาเทคโนโลยีสารสนเทศ
    $sqlSentIt = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 7 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentIt = mysqli_query($conn, $sqlSentIt);
    $sentIt = 0;
    if ($resIt = mysqli_fetch_assoc($resultSentIt)) {
        $sentIt = intval($resIt['sent']);
    }
    $notSentIt = $totalIt - $sentIt;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาภาษาต่างประเทศ
    $sqlSentForeignLanguage = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 8 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentForeignLanguage = mysqli_query($conn, $sqlSentForeignLanguage);
    $sentForeignLanguage = 0;
    if ($resForeignLanguage = mysqli_fetch_assoc($resultSentForeignLanguage)) {
        $sentForeignLanguage = intval($resForeignLanguage['sent']);
    }
    $notSentForeignLanguage = $totalForeignLanguage - $sentForeignLanguage;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาการโรงแรม
    $sqlSentHotelManagement = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 9 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentHotelManagement = mysqli_query($conn, $sqlSentHotelManagement);
    $sentHotelManagement = 0;
    if ($resHotelManagement = mysqli_fetch_assoc($resultSentHotelManagement)) {
        $sentHotelManagement = intval($resHotelManagement['sent']);
    }
    $notSentHotelManagement = $totalHotelManagement - $sentHotelManagement;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาอาหารและโภชนาการ
    $sqlSentFood = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 10 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentFood = mysqli_query($conn, $sqlSentFood);
    $sentFood = 0;
    if ($resFood = mysqli_fetch_assoc($resultSentFood)) {
        $sentFood = intval($resFood['sent']);
    }
    $notSentFood = $totalFood - $sentFood;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาคหกรรม
    $sqlSentHomeEconomics = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 11 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentHomeEconomics = mysqli_query($conn, $sqlSentHomeEconomics);
    $sentHomeEconomics = 0;
    if ($resHomeEconomics = mysqli_fetch_assoc($resultSentHomeEconomics)) {
        $sentHomeEconomics = intval($resHomeEconomics['sent']);
    }
    $notSentHomeEconomics = $totalHomeEconomics - $sentHomeEconomics;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาแฟชั่นและสิ่งทอ
    $sqlSentFashion = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 12 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentFashion = mysqli_query($conn, $sqlSentFashion);
    $sentFashion = 0;
    if ($resFashion = mysqli_fetch_assoc($resultSentFashion)) {
        $sentFashion = intval($resFashion['sent']);
    }
    $notSentFashion = $totalFashion - $sentFashion;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาการออกแบบ
    $sqlSentDesign = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 13 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentDesign = mysqli_query($conn, $sqlSentDesign);
    $sentDesign = 0;
    if ($resDesign = mysqli_fetch_assoc($resultSentDesign)) {
        $sentDesign = intval($resDesign['sent']);
    }
    $notSentDesign = $totalDesign - $sentDesign;
    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น สาขาดิจิทัลกราฟิก
    $sqlSentDg = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.branch_id = 14 
        AND u.status != 'internship'
        AND r.week = $week
    ";
    $resultSentDg = mysqli_query($conn, $sqlSentDg);
    $sentDg = 0;
    if ($resDg = mysqli_fetch_assoc($resultSentDg)) {
        $sentDg = intval($resDg['sent']);
    }
    $notSentDg = $totalDg - $sentDg;
}


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

// สร้างข้อมูลที่จะส่งไปยัง Google Docs
$data = [

    'termSelect' => $termSelect,
    'day' => $date['day'],
    'month' => $date['month'],
    'year' => $date['year'],
    'week' => $week,

    'sentAll' => $sentAll,
    'notSentAll' => $notSentAll,
    'totalAll' => $totalAll,
    'accountant' => $accountant,
    'sentAccount' => $sentAccount,
    'notSentAccount' => $notSentAccount,
    'totalAccount' => $totalAccount,
    'marketing' => $marketing,
    'sentMarketing' => $sentMarketing,
    'notSentMarketing' => $notSentMarketing,
    'totalMarketing' => $totalMarketing,
    'management' => $management,
    'sentManagement' => $sentManagement,
    'notSentManagement' => $notSentManagement,
    'totalManagement' => $totalManagement,
    'logistics' => $logistics,
    'sentLogistics' => $sentLogistics,
    'notSentLogistics' => $notSentLogistics,
    'totalLogistics' => $totalLogistics,
    'dbt' => $dbt,
    'sentDbt' => $sentDbt,
    'notSentDbt' => $notSentDbt,
    'totalDbt' => $totalDbt,
    'retail' => $retail,
    'sentRetail' => $sentRetail,
    'notSentRetail' => $notSentRetail,
    'totalRetail' => $totalRetail,
    'It' => $it,
    'sentIt' => $sentIt,
    'notSentIt' => $notSentIt,
    'totalIt' => $totalIt,
    'foreignLanguage' => $foreignLanguage,
    'sentForeignLanguage' => $sentForeignLanguage,
    'notSentForeignLanguage' => $notSentForeignLanguage,
    'totalForeignLanguage' => $totalForeignLanguage,
    'hotelManagement' => $hotelManagement,
    'sentHotelManagement' => $sentHotelManagement,
    'notSentHotelManagement' => $notSentHotelManagement,
    'totalHotelManagement' => $totalHotelManagement,
    'food' => $food,
    'sentFood' => $sentFood,
    'notSentFood' => $notSentFood,
    'totalFood' => $totalFood,
    'homeEconomics' => $homeEconomics,
    'sentHomeEconomics' => $sentHomeEconomics,
    'notSentHomeEconomics' => $notSentHomeEconomics,
    'totalHomeEconomics' => $totalHomeEconomics,
    'fashion' => $fashion,
    'sentFashion' => $sentFashion,
    'notSentFashion' => $notSentFashion,
    'totalFashion' => $totalFashion,
    'design' => $design,
    'sentDesign' => $sentDesign,
    'notSentDesign' => $notSentDesign,
    'totalDesign' => $totalDesign,
    'dg' => $dg,
    'sentDg' => $sentDg,
    'notSentDg' => $notSentDg,
    'totalDg' => $totalDg
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
