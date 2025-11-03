<?php
include 'db.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $status = "success";

        $userID = $_POST['userID'];
        $subjectID = $_POST['subjectID'];
        $subjectName = $_POST['subjectName'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $week = $_POST['week'];
        $passwordVerifyTeacher = $_POST['passwordVerifyTeacher'];
        $note = $_POST['note'];
        $miss = $_POST['miss'];
        $all = $_POST['all'];
        $term = $_POST['term'];
        $insteadTeacher = $_POST['insteadTeacher'] ?? null;
        $missStudent = $_POST['missStudent'] ?? null;

        $sqlTeacher = "SELECT id FROM teacher WHERE password_match = '$passwordVerifyTeacher'";
        $queryTeacher = mysqli_query($conn, $sqlTeacher);

        if (mysqli_num_rows($queryTeacher) > 0) {
            $row = mysqli_fetch_assoc($queryTeacher);
            $teacherID = $row['id'];

            if ($note === 'เข้าสอนปกติ') {
                $sqlSubject = "SELECT teacher_id FROM subject WHERE id = '$subjectID'";
                $querySubject = mysqli_query($conn, $sqlSubject);
                if ($querySubject && mysqli_num_rows($querySubject) > 0) {
                    $subjectRow = mysqli_fetch_assoc($querySubject);
                    $teacherID = $subjectRow['teacher_id'];
                }
            } else if ($note === 'สอนแทน' && !empty($insteadTeacher)) {
                $teacherID = $insteadTeacher;
            }

            $arr = array_map('trim', explode(',', $missStudent));
            $arr = array_filter($arr);
            $missStudent = implode(",\n ", $arr);

            $sql = "INSERT INTO record (
                        user_id, subject_id, subject_name, begin_period, end_period, date, 
                        week, note, miss, all_student, term, insteadTeacher,missStudentName
                    ) VALUES (
                        '$userID','$subjectID','$subjectName','$start','$end',NOW(),
                        '$week','$note','$miss','$all','$term','$teacherID','$missStudent'
                    )";
            $query = mysqli_query($conn, $sql);

            if (!$query) {
                $status = "บันทึกข้อมูลไม่สำเร็จ";
            }
        } else {
            $status = "ไม่มีครูคนไหนใช้รหัสนี้";
        }

    echo json_encode([
        "status" => $status,
        "data" => [
            "userID" => $userID,
            "subjectID" => $subjectID,
            "subjectName" => $subjectName,
            "start" => $start,
            "end" => $end,
            "week" => $week,
            "passwordVerifyTeacher" => $passwordVerifyTeacher,
            "note" => $note,
            "miss" => $miss,
            "all" => $all,
            "term" => $term,
            "insteadTeacher" => $teacherID ?? null,
            "missStudent" => $missStudent ?? null
        ]
    ]);
}
?>