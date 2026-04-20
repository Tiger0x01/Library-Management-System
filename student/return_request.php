<?php
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='../index.php';</script>";
    exit();
}

if(isset($_GET['id'])) {
    $roll = $_SESSION['RollNo'];
    
    $id = $conn->real_escape_string($_GET['id']);

    $sql = "INSERT INTO LMS.return (RollNo, BookId) VALUES ('$roll', '$id')";

    if($conn->query($sql) === TRUE) {
        echo "<script type='text/javascript'>
                alert('Return Request Sent to Admin Successfully. ✅');
                window.location.href = 'current.php';
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Request Already Sent or an error occurred! ⚠️');
                window.location.href = 'current.php';
              </script>";
    }
} else {
    header("Location: current.php");
    exit();
}
?>