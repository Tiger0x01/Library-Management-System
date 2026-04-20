<?php
ob_start();
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='../index.php';</script>";
    exit();
}

$rollno = $_SESSION['RollNo'];

$sql_user = "SELECT Name FROM LMS.user WHERE RollNo='$rollno'";
$result_user = $conn->query($sql_user);
$user_name = ($result_user && $result_user->num_rows > 0) ? $result_user->fetch_assoc()['Name'] : "Student";
if(isset($_GET['id'])) {
    $bookid = $conn->real_escape_string($_GET['id']);
    $sql_book = "SELECT * FROM LMS.book WHERE BookId='$bookid'";

    try {
        $result_book = $conn->query($sql_book);
        if($result_book && $result_book->num_rows > 0) {
            $row = $result_book->fetch_assoc();

            $title = $row['Title'] ?? $row['title'] ?? 'Unknown Title';
            $author = $row['Author'] ?? 'Unknown Author';
            $year = $row['Year'] ?? $row['year'] ?? 'N/A';
            $avail = $row['Availability'] ?? $row['availability'] ?? 0;
            $description = $row['Description'] ?? 'No description available at the moment. This book is a great addition to your reading list.';
            
            $is_online = isset($row['is_online']) ? (int)$row['is_online'] : 0;
            $price = isset($row['price']) ? (float)$row['price'] : 0.00;
            
            $is_purchased = false;
            if ($is_online == 1) {
                $check_purchased = "SELECT * FROM LMS.purchased_books WHERE RollNo='$rollno' AND BookId='$bookid'";
                $res_purchased = $conn->query($check_purchased);
                if ($res_purchased && $res_purchased->num_rows > 0) {
                    $is_purchased = true;
                }
            }

        } else {
            echo "<script>alert('Book Not Found!'); window.location='book.php';</script>";
            exit();
        }
    } catch (Exception $e) {
        echo "<script>alert('Database Error!'); window.location='book.php';</script>";
        exit();
    }
} else {
    header("Location: book.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Book Details - Modern LMS</title>

        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            overflow-x: hidden;
            color: #2b3452;
        }

        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar {
            min-width: 260px; max-width: 260px; background: #ffffff;
            transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03);
            min-height: 100vh; z-index: 999;
        }
        #sidebar.active { margin-left: -260px; }
        .sidebar-header {
            padding: 25px 20px; background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            color: white; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 15px 25px; font-size: 1.05em; display: block;
            color: #6c757d; text-decoration: none; transition: 0.3s ease; font-weight: 500;
            border-left: 4px solid transparent;
        }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; transition: 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: #4e54c8; background: rgba(78, 84, 200, 0.05); border-left: 4px solid #4e54c8;
        }

        #content { width: 100%; padding: 20px 40px; min-height: 100vh; transition: all 0.3s; }
        
        .top-navbar {
            background: #ffffff; padding: 15px 25px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
        }
        #sidebarCollapse {
            background: #4e54c8; color: white; border: none;
            border-radius: 8px; padding: 8px 15px; transition: 0.3s;
        }
        #sidebarCollapse:hover { background: #3b3f98; }

        .book-details-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            border: none;
            overflow: hidden;
        }

        .book-cover-placeholder {
            background: <?php echo ($is_online == 1) ? 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)' : 'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)'; ?>;
            height: 100%;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
        }

        .book-cover-placeholder .main-icon {
            font-size: 8rem;
            opacity: 0.9;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.15));
        }
        
        .badge-type {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.95);
            color: #333;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            z-index: 10;
        }

        .badge-type i {
            font-size: 1.1rem !important; 
            margin-right: 5px;
        }

        .book-info-section { padding: 40px; }
        .info-row {
            display: flex; align-items: center; margin-bottom: 15px;
            padding-bottom: 15px; border-bottom: 1px dashed #eee;
        }
        .info-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .info-icon {
            width: 45px; height: 45px; background: rgba(78, 84, 200, 0.08);
            color: #4e54c8; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; font-size: 1.3rem; margin-right: 15px;
            flex-shrink: 0;
        }
        .info-label {
            font-size: 0.85rem; color: #888; margin-bottom: 2px;
            text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;
        }
        .info-value { font-size: 1.05rem; color: #333; font-weight: 600; margin-bottom: 0; }

        .action-area {
            background: #f8f9fc;
            padding: 25px 40px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-action {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: absolute; }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
            .book-cover-placeholder { min-height: 250px; }
            .book-info-section { padding: 25px; }
            .action-area { flex-direction: column; text-align: center; gap: 15px; padding: 20px; }
            .btn-action { width: 100%; justify-content: center; }
        }
        </style>
    </head>
    <body>

        <div class="wrapper">
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3 class="mb-0 fw-bold"><i class="bi bi-book-half me-2"></i> LMS</h3>
                    <small class="text-white-50">Student Portal</small>
                </div>
                <ul class="list-unstyled components">
                    <li><a href="index.php"><i class="bi bi-grid-1x2-fill"></i> Home</a></li>
                    <li class="active"><a href="book.php"><i class="bi bi-journal-album"></i> All Books</a></li>
                    <li><a href="history.php"><i class="bi bi-clock-history"></i> Borrow History</a></li>
                    <li><a href="current.php"><i class="bi bi-list-check"></i> Currently Issued</a></li>
                    <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left text-danger"></i> Logout</a></li>
                </ul>
            </nav>

            <div id="content">
                <nav class="top-navbar d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn me-3">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <h5 class="fw-bold mb-0 text-dark">Book Details</h5>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-end d-none d-md-block">
                            <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($user_name); ?></h6>
                            <small class="text-muted">ID: <?php echo htmlspecialchars($rollno); ?></small>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=4e54c8&color=fff&rounded=true&bold=true" alt="Profile" width="45">
                    </div>
                </nav>

                <div class="container-fluid px-0 mb-5">
                    <div class="row justify-content-center">
                        <div class="col-xl-9 col-lg-10">

                            <div class="mb-4">
                                <a href="book.php" class="btn btn-white bg-white border shadow-sm rounded-pill px-4 fw-medium text-dark text-decoration-none">
                                    <i class="bi bi-arrow-left me-2"></i> Back to Catalog
                                </a>
                            </div>

                            <div class="book-details-card">
                                <div class="row g-0">
                                    <div class="col-md-5 col-lg-4">
                                        <div class="book-cover-placeholder">
                                            <div class="badge-type">
                                                <?php if($is_online == 1): ?>
                                                    <i class="bi bi-laptop text-warning"></i> Digital Edition
                                                <?php else: ?>
                                                    <i class="bi bi-book text-primary"></i> Physical Copy
                                                <?php endif; ?>
                                            </div>
                                            
                                            <i class="main-icon bi <?php echo ($is_online == 1) ? 'bi-file-earmark-pdf-fill' : 'bi-journal-bookmark-fill'; ?>"></i>
                                        </div>
                                    </div>

                                    <div class="col-md-7 col-lg-8 d-flex flex-column">
                                        <div class="book-info-section flex-grow-1">
                                            <h2 class="fw-bold text-dark mb-4"><?php echo htmlspecialchars($title); ?></h2>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-row">
                                                        <div class="info-icon"><i class="bi bi-upc-scan"></i></div>
                                                        <div>
                                                            <p class="info-label">Book ID</p>
                                                            <p class="info-value">#<?php echo htmlspecialchars($bookid); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-row">
                                                        <div class="info-icon"><i class="bi bi-person-lines-fill"></i></div>
                                                        <div>
                                                            <p class="info-label">Author(s)</p>
                                                            <p class="info-value"><?php echo htmlspecialchars($author); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="info-row">
                                                        <div class="info-icon"><i class="bi bi-tag"></i></div>
                                                        <div>
                                                            <p class="info-label">Format & Pricing</p>
                                                            <p class="info-value">
                                                                <?php if($is_online == 1): ?>
                                                                    <?php echo ($price > 0) ? "<span class='text-warning fw-bold'>$".number_format($price, 2)."</span>" : "<span class='text-success fw-bold'>Free Online</span>"; ?>
                                                                <?php else: ?>
                                                                    Library Issue (Free)
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-row">
                                                        <div class="info-icon"><i class="bi bi-check2-circle"></i></div>
                                                        <div>
                                                            <p class="info-label">Availability</p>
                                                            <p class="info-value">
                                                                <?php if($is_online == 1): ?>
                                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Unlimited Access</span>
                                                                <?php elseif($avail > 0): ?>
                                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><?php echo $avail; ?> Copies</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Out of Stock</span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="info-row mt-3" style="border:none;">
                                                <div class="info-icon align-self-start mt-1"><i class="bi bi-card-text"></i></div>
                                                <div>
                                                    <p class="info-label">Description</p>
                                                    <p class="info-value text-muted fw-normal fs-6 lh-lg"><?php echo htmlspecialchars($description); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="action-area">
                                            <div class="text-muted small">
                                                <i class="bi bi-shield-check text-success me-1"></i> Secure Library System
                                            </div>
                                            
                                            <div class="d-flex gap-3">
                                                <?php if($is_online == 1): ?>
                                                    
                                                    <?php if($price == 0 || $is_purchased): ?>
                                                        <a href="viewbook.php?id=<?php echo $bookid; ?>" class="btn btn-success btn-action text-white">
                                                            <i class="bi bi-book-half me-2"></i> Start Reading
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="viewbook.php?id=<?php echo $bookid; ?>" class="btn btn-warning btn-action text-dark">
                                                            <i class="bi bi-cart3 me-2"></i> Buy for $<?php echo number_format($price, 2); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                <?php else: ?>
                                                
                                                    <?php if($avail > 0): ?>
                                                        <a href="issue_request.php?id=<?php echo $bookid; ?>" class="btn btn-primary btn-action text-white" style="background-color: #4e54c8; border-color: #4e54c8;">
                                                            <i class="bi bi-bookmark-plus me-2"></i> Request Issue
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary btn-action" disabled>
                                                            <i class="bi bi-x-circle me-2"></i> Currently Unavailable
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                const sidebarCollapseBtn = document.getElementById('sidebarCollapse');
                const sidebar = document.getElementById('sidebar');

                sidebarCollapseBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                });
            });
        </script>
    </body>
</html>