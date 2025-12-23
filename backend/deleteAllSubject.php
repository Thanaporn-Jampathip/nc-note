<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>กำลังประมวณผล . . .</title>
</head>

<body>

</body>

</html>
<?php
include "db.php";

if (isset($_POST['deleteAllSubject'])) {

    function dropFKIfExists($conn, $table, $constraintName, $dbName)
    {
        $check = mysqli_query($conn, "
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = '$dbName'
              AND TABLE_NAME = '$table'
              AND CONSTRAINT_NAME = '$constraintName'
        ");

        if ($check && mysqli_num_rows($check) > 0) {
            if (!mysqli_query($conn, "ALTER TABLE `$table` DROP FOREIGN KEY `$constraintName`")) {
                throw new Exception(mysqli_error($conn));
            }
        }
    }

    try {
        mysqli_begin_transaction($conn);

        $dbName = 'nc_note';

        /* =========================
           1. DROP FK (ถ้ามี)
           ========================= */
        dropFKIfExists($conn, 'subject', 'FK_teacherID', $dbName);
        dropFKIfExists($conn, 'subject', 'FK_userID', $dbName);
        dropFKIfExists($conn, 'record', 'subjectID', $dbName);
        dropFKIfExists($conn, 'record', 'userID', $dbName);

        /* =========================
           2. ทำให้ subject_id เป็น NULL ได้
           ========================= */
        if (
            !mysqli_query($conn, "
            ALTER TABLE record 
            MODIFY subject_id BIGINT(30) NULL
        ")
        ) {
            throw new Exception(mysqli_error($conn));
        }

        /* =========================
           3. เคลียร์ความสัมพันธ์ก่อนลบ
           ========================= */
        if (
            !mysqli_query($conn, "
            UPDATE record 
            SET subject_id = NULL,
            subject_name = NULL
        ")
        ) {
            throw new Exception(mysqli_error($conn));
        }

        /* =========================
           4. ลบ subject
           ========================= */
        if (!mysqli_query($conn, "DELETE FROM subject")) {
            throw new Exception(mysqli_error($conn));
        }

        /* =========================
           5. เพิ่ม FK กลับ
           ========================= */

        // record → subject (SET NULL)
        mysqli_query($conn, "
            ALTER TABLE record 
            ADD CONSTRAINT subjectID 
            FOREIGN KEY (subject_id) 
            REFERENCES subject(id)
            ON DELETE SET NULL
        ");

        // subject → teacher
        mysqli_query($conn, "
            ALTER TABLE subject 
            ADD CONSTRAINT FK_teacherID 
            FOREIGN KEY (teacher_id) 
            REFERENCES teacher(id)
            ON DELETE RESTRICT
        ");

        // subject → user
        mysqli_query($conn, "
            ALTER TABLE subject 
            ADD CONSTRAINT FK_userID 
            FOREIGN KEY (userID) 
            REFERENCES user(id)
            ON DELETE RESTRICT
        ");

        mysqli_commit($conn);

        echo '<script>
            Swal.fire({
                title: "ลบรายวิชาสำเร็จ",
                icon: "success",
                timer: 1500,
                didOpen: () => Swal.showLoading()
            }).then(() =>{
                window.location.href="../subject.php";
            })
        </script>';

    } catch (Exception $e) {

        mysqli_rollback($conn);

        echo '<script>
            Swal.fire({
                title: "เกิดข้อผิดพลาด",
                text: "' . $e->getMessage() . '",
                icon: "error"
            }).then(() =>{
                window.location.href="../subject.php";
            })
        </script>';
    }

}
?>