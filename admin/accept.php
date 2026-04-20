<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$bookid = $conn->real_escape_string($_GET['id1']);
$rollno = $conn->real_escape_string($_GET['id2']);


$sql_issue = "UPDATE LMS.record 
              SET Date_of_Issue = CURDATE(), 
                  Due_Date = DATE_ADD(CURDATE(), INTERVAL 60 DAY), 
                  Renewals_left = 1 
              WHERE BookId = '$bookid' AND RollNo = '$rollno' AND Date_of_Issue IS NULL";

if ($conn->query($sql_issue) === TRUE) {
    
    $sql_book_update = "UPDATE LMS.book SET Availability = Availability - 1 WHERE BookId = '$bookid'";
    $conn->query($sql_book_update);

    echo "<script type='text/javascript'>
            alert('Book Issued Successfully! ✅');
            window.location.href = 'issue_requests.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Error: Could not issue book. ⚠️');
            window.location.href = 'issue_requests.php';
          </script>";
}
?>