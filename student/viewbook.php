<?php
ob_start();
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='../index.php';</script>";
    exit();
}

$rollno = $_SESSION['RollNo'];
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(isset($_POST['confirm_payment'])) {
    $trans_id = $conn->real_escape_string($_POST['transaction_id']);
    
    $insert_purchase = "INSERT INTO LMS.purchased_books (RollNo, BookId, transaction_id) VALUES ('$rollno', '$book_id', '$trans_id')";
    
    if($conn->query($insert_purchase)) {
        header("Location: viewbook.php?id=$book_id&paid=success");
        exit();
    }
}

$sql = "SELECT * FROM LMS.book WHERE BookId='$book_id' AND is_online=1";
$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    $book = $result->fetch_assoc();
    $title = $book['Title'];
    $author = $book['Author'];
    $price = $book['price'];
    $file_path = $book['file_path'];
    
    $can_read = false;
    $already_purchased = false;

    if($price <= 0) {
        $can_read = true;
    } else {
      
        $check_sql = "SELECT * FROM LMS.purchased_books WHERE RollNo='$rollno' AND BookId='$book_id'";
        if($conn->query($check_sql)->num_rows > 0) {
            $can_read = true;
            $already_purchased = true;
        }
    }
} else {
    
    echo "<script>alert('Book not found or not available for online reading.'); window.location='book.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read: <?php echo htmlspecialchars($title); ?> - LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #2b3452;
        }
        
        .navbar-custom {
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            padding: 15px 30px;
        }
        
        .pdf-container {
            height: 85vh;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: #333;
        }
        
        .payment-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }

        .vf-cash-banner {
            background: #e60000;
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            font-family: 'Cairo', sans-serif; 
        }

        .vf-logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .vf-logo img {
            width: 40px;
        }

        .price-tag {
            font-size: 2.5rem;
            font-weight: 800;
            color: #e60000;
            margin: 15px 0;
        }

        .form-control:focus {
            border-color: #e60000;
            box-shadow: 0 0 0 4px rgba(230, 0, 0, 0.1);
        }

        .btn-confirm {
            background: #e60000;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 10px;
            border: none;
            width: 100%;
            transition: 0.3s;
        }
        
        .btn-confirm:hover {
            background: #cc0000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<nav class="navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <a href="book.php" class="btn btn-light rounded-circle me-3 shadow-sm" title="Back to Library">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-bold text-truncate" style="max-width: 600px;">
            <i class="bi bi-book-half text-primary me-2"></i> <?php echo htmlspecialchars($title); ?>
            <small class="text-muted ms-2 fs-6 fw-normal">by <?php echo htmlspecialchars($author); ?></small>
        </h5>
    </div>
    
    <div>
        <?php if($can_read && $price > 0): ?>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                <i class="bi bi-check-circle-fill me-1"></i> Purchased
            </span>
        <?php elseif($can_read && $price <= 0): ?>
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">
                <i class="bi bi-stars me-1"></i> Free Book
            </span>
        <?php endif; ?>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    
    <?php if(isset($_GET['paid']) && $_GET['paid'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show text-center rounded-4 shadow-sm mx-auto" style="max-width: 800px;" role="alert">
            <strong><i class="bi bi-check2-circle fs-4 me-2 align-middle"></i> Payment Successful!</strong> You can now read the book.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if($can_read): ?>
        <div class="pdf-container">
            <iframe src="../<?php echo htmlspecialchars($file_path); ?>#toolbar=0" width="100%" height="100%" style="border:none;"></iframe>
        </div>
        
    <?php else: ?>
        <div class="payment-card">
            <h3 class="fw-bold mb-1">Premium Book Unlock</h3>
            <p class="text-muted mb-4">Complete your payment to access this digital book instantly.</p>
            
            <div class="vf-cash-banner">
                <div class="vf-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#e60000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                </div>
                <h4 class="fw-bold mb-2">Vodafone Cash</h4>
                <p class="mb-0 fs-6">لشراء هذا الكتاب، يرجى تحويل المبلغ المطلوب إلى رقم فودافون كاش التالي:</p>
                <h2 class="fw-bold mt-2 mb-0" style="letter-spacing: 2px; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">01024995208</h2>
            </div>
            
            <div class="mb-4">
                <span class="text-muted fw-semibold text-uppercase" style="letter-spacing: 1px;">Amount to Transfer</span>
                <div class="price-tag">$<?php echo number_format($price, 2); ?></div>
            </div>

            <hr class="mb-4" style="opacity: 0.1;">

            <form action="viewbook.php?id=<?php echo $book_id; ?>" method="POST">
                <div class="mb-4 text-start">
                    <label class="form-label fw-bold text-dark font-monospace" style="font-family: 'Cairo', sans-serif !important;">رقم العملية (Transaction ID) أو رقم المرسل:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-receipt"></i></span>
                        <input type="text" name="transaction_id" class="form-control py-2" placeholder="أدخل رقم التحويل هنا..." required>
                    </div>
                    <small class="text-muted mt-2 d-block text-start"><i class="bi bi-info-circle me-1"></i> For this demo, enter any 11-digit number or receipt ID to instantly unlock the book.</small>
                </div>
                
                <button type="submit" name="confirm_payment" class="btn-confirm">
                    <i class="bi bi-shield-lock me-2"></i> Confirm Payment & Read
                </button>
            </form>
            
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>