<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $rollno = $conn->real_escape_string($_GET['id']);

    if ($rollno == 'ADMIN' || $rollno == $_SESSION['RollNo']) {
        echo "<script>alert('Error: You cannot remove an administrator account!'); window.location='student.php';</script>";
        exit();
    }


    $conn->query("DELETE FROM LMS.renew WHERE RollNo='$rollno'");

    $conn->query("DELETE FROM LMS.return WHERE RollNo='$rollno'");

    $conn->query("DELETE FROM LMS.record WHERE RollNo='$rollno'");

    $sql_delete_user = "DELETE FROM LMS.user WHERE RollNo='$rollno' AND Type='Student'";

    if ($conn->query($sql_delete_user) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo "<script type='text/javascript'>
                    alert('Student Account #$rollno has been removed successfully. ✅');
                    window.location.href = 'student.php';
                  </script>";
        } else {
            echo "<script>alert('Student not found or already removed.'); window.location='student.php';</script>";
        }
    } else {
        echo "<script type='text/javascript'>
                alert('Error: Could not complete removal. ⚠️');
                window.location.href = 'student.php';
              </script>";
    }

} else {

    header("Location: student.php");
    exit();
}
?>