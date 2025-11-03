<?php
include './backend/db.php';
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

    // ดึงจำนวนที่ส่งแล้วในสัปดาห์นั้น ทั้งวิทลัย
    $sqlSentAll = "
        SELECT COUNT(DISTINCT r.user_id) as sent
        FROM record r
        JOIN user u ON r.user_id = u.id
        WHERE u.status != 'internship'
    ";
    $resultSentAll = mysqli_query($conn, $sqlSentAll);
    $sentAll = 0;
    if ($resAll = mysqli_fetch_assoc($resultSentAll)) {
        $sentAll = intval($resAll['sent']);
    }
  echo '<pre>';
  echo 'Total Users: ' . $totalAll . "\n";
  echo 'Sent Users: ' . $sentAll . "\n";
  echo '</pre>';
?>