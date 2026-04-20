<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}


if (isset($_GET['id1']) && isset($_GET['id2'])) {
    
    $bookid = $_GET['id1'];
    $rollno = $_GET['id2'];


    $stmt = $conn->prepare("DELETE FROM LMS.record WHERE RollNo = ? AND BookId = ?");
    $stmt->bind_param("si", $rollno, $bookid); // s = string (RollNo), i = integer (BookId)

    if ($stmt->execute()) {
       
        echo "<script type='text/javascript'>
                alert('Success: Request has been deleted successfully.');
                window.location.href='issue_requests.php';
              </script>";
    } else {
  
        echo "<script type='text/javascript'>
                alert('Error: Unable to delete record. Please try again.');
                window.location.href='issue_requests.php';
              </script>";
    }

    $stmt->close();

} else {
    header("Location: issue_requests.php");
    exit();
}

$conn->close();
?>