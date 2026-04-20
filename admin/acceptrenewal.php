<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$bookid = $conn->real_escape_string($_GET['id1']);
$rollno = $conn->real_escape_string($_GET['id2']);


$sql_update = "UPDATE LMS.record 
               SET Due_Date = DATE_ADD(Due_Date, INTERVAL 60 DAY), 
                   Renewals_left = 0 
               WHERE BookId = '$bookid' AND RollNo = '$rollno'";

if ($conn->query($sql_update) === TRUE) {
    
    $sql_delete = "DELETE FROM LMS.renew WHERE BookId = '$bookid' AND RollNo = '$rollno'";
    $conn->query($sql_delete);

    echo "<script type='text/javascript'>
            alert('Renewal Approved Successfully! ✅');
            window.location.href = 'renew_requests.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Error: Could not process renewal. ⚠️');
            window.location.href = 'renew_requests.php';
          </script>";
}
?>