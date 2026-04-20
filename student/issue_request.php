<?php
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='../index.php';</script>";
    exit();
}

if(isset($_GET['id'])) {
    
    $roll = $_SESSION['RollNo'];
    
    $id = $conn->real_escape_string($_GET['id']);
    
    $sql = "INSERT INTO LMS.record (RollNo, BookId, Time) VALUES ('$roll', '$id', curtime())";

    try {
        if($conn->query($sql) === TRUE) {
            echo "<script type='text/javascript'>
                    alert('Request Sent to Admin Successfully. 👍');
                    window.location.href = 'book.php';
                  </script>";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "<script type='text/javascript'>
                    alert('You have already requested or borrowed this book! ⚠️');
                    window.location.href = 'book.php';
                  </script>";
        } else {
            echo "<script type='text/javascript'>
                    alert('An error occurred: " . addslashes($e->getMessage()) . "');
                    window.location.href = 'book.php';
                  </script>";
        }
    }

} else {
    header("Location: book.php");
    exit();
}
?>