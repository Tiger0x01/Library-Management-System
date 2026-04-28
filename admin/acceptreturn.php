<?php
require('../includes/dbconn.php');


if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}


$bookid = $conn->real_escape_string($_GET['id1']);
$rollno = $conn->real_escape_string($_GET['id2']);

$sql1 = "UPDATE LMS.record SET Date_of_Return = CURDATE() 
         WHERE BookId = '$bookid' AND RollNo = '$rollno' AND Date_of_Return IS NULL";

if ($conn->query($sql1) === TRUE) {
    
    $sql2 = "UPDATE LMS.book SET Availability = Availability + 1 WHERE BookId = '$bookid'";
    $conn->query($sql2);

    $sql3 = "DELETE FROM LMS.return WHERE BookId = '$bookid' AND RollNo = '$rollno'";
    $conn->query($sql3);

    $sql4 = "DELETE FROM LMS.renew WHERE BookId = '$bookid' AND RollNo = '$rollno'";
    $conn->query($sql4);

    echo "<script type='text/javascript'>
            alert('Book Return Processed Successfully! ✅');
            window.location.href = 'return_requests.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Error: Could not process return. ⚠️');
            window.location.href = 'return_requests.php';
          </script>";
}
?>