<?php
if (isset($_POST['imageData']) && isset($_POST['paramiter']) && $_POST['paramiter'] == 1) {
    $data = $_POST['imageData'];
    // แยก header ออกจาก base64
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    // ตั้งชื่อไฟล์
    $filename = 'chart_1.png';
    $folder = 'image/chart/';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filePath = $folder . $filename;

    if (file_put_contents($filePath, $data)) {
        echo $filename; // ส่งชื่อไฟล์กลับไป
    } else {
        echo "ไม่สามารถบันทึกไฟล์ได้";
    }
}
elseif (isset($_POST['imageDataSec']) && isset($_POST['paramiterSec']) && $_POST['paramiterSec'] == 2) {
    $data = $_POST['imageDataSec'];
    // แยก header ออกจาก base64
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    // ตั้งชื่อไฟล์
    $filenameSec = 'chart_2.png';
    $folder = 'image/chart/';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filePath = $folder . $filenameSec;

    if (file_put_contents($filePath, $data)) {
        echo $filenameSec; // ส่งชื่อไฟล์กลับไป
    } else {
        echo "ไม่สามารถบันทึกไฟล์ได้";
    }
}
elseif (isset($_POST['imageDataThird']) && isset($_POST['paramiterThird']) && $_POST['paramiterThird'] == 3) {
    $data = $_POST['imageDataThird'];
    // แยก header ออกจาก base64
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    // ตั้งชื่อไฟล์
    $filenameThird = 'chart_3.png';
    $folder = 'image/chart/';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filePath = $folder . $filenameThird;

    if (file_put_contents($filePath, $data)) {
        echo $filenameThird; // ส่งชื่อไฟล์กลับไป
    } else {
        echo "ไม่สามารถบันทึกไฟล์ได้";
    }
} else {
    echo "กรุณาเลือกสัปดาห์ที่จะดูอีกครั้ง";
}
?>
