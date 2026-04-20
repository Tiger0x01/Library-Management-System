<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];
$sql_check = "SELECT * FROM LMS.user WHERE RollNo='$rollno' AND Type='Admin'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    header("Location: ../student/index.php");
    exit();
}

$admin_row = $result_check->fetch_assoc();
$admin_name = $admin_row['Name'] ?? 'Librarian';

if(isset($_GET['id'])) {
    $book_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM LMS.book WHERE BookId='$book_id'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['Title'];
        $author = $row['Author'] ?? 'Unknown Author';
        $avail = $row['Availability'];
        $status = $row['status'];
    } else {
        echo "<script>alert('Book not found!'); window.location='book.php';</script>";
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
    <title>Book Info - <?php echo htmlspecialchars($title); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --primary-light: #7b81ea;
            --bg-body: #f4f7fa;
            --badge-blue: #eef0f8;
            --badge-green: #e1f7ec;
            --text-green: #28c76f;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-body);
            color: #2b3452;
        }

        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* ===== Sidebar ===== */
        #sidebar {
            min-width: 260px; max-width: 260px; background: #fff;
            transition: all 0.3s; box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            min-height: 100vh; z-index: 999;
        }
        #sidebar.active { margin-left: -260px; }
        .sidebar-header {
            padding: 25px; background: var(--primary);
            color: white; text-align: center;
        }
        #sidebar ul.components { padding: 15px 0; }
        #sidebar ul li a {
            padding: 15px 25px; display: block;
            color: #6c757d; text-decoration: none; font-weight: 500;
        }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.1em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 4px solid var(--primary);
        }

        /* ===== Content Area ===== */
        #content { width: 100%; padding: 25px 40px; }
        .top-navbar {
            background: #fff; padding: 15px 25px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
        }

        /* ===== Detail Card Styling ===== */
        .card-container {
            background: white; border-radius: 24px; padding: 45px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.03); border: none;
            position: relative; overflow: hidden;
        }

        .id-badge {
            background-color: var(--badge-blue); color: var(--primary);
            padding: 8px 18px; border-radius: 10px; font-weight: 800; font-size: 14px;
            display: inline-block; margin-bottom: 20px;
        }

        .info-label { color: #adb5bd; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .info-value { font-size: 24px; font-weight: 700; color: #1a202c; margin-bottom: 30px; line-height: 1.2; }

        .avail-badge {
            background-color: var(--badge-green); color: var(--text-green);
            padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 13px;
            display: inline-flex; align-items: center; border: 1px solid rgba(40, 199, 111, 0.1);
        }

        /* Outline Buttons */
        .btn-outline-custom {
            border: 2px solid var(--primary); color: var(--primary);
            border-radius: 12px; font-weight: 700; padding: 12px 25px;
            transition: 0.3s; background: transparent;
        }
        .btn-outline-custom:hover { background: var(--primary); color: white; transform: translateY(-2px); }

        .btn-outline-edit {
            border: 2px solid var(--text-green); color: var(--text-green);
            border-radius: 12px; font-weight: 700; padding: 12px 25px;
            transition: 0.3s; background: transparent; text-decoration: none; display: inline-block;
        }
        .btn-outline-edit:hover { background: var(--text-green); color: white; transform: translateY(-2px); }

        .watermark-icon {
            position: absolute; right: -20px; top: 50%; transform: translateY(-50%);
            font-size: 220px; color: var(--primary); opacity: 0.03; pointer-events: none;
        }

        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: absolute; }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3 class="mb-0 fw-bold"><i class="bi bi-book-half me-2"></i> LMS</h3>
            <small class="text-white-50">Librarian Portal</small>
        </div>
        <ul class="list-unstyled components">
            <li><a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people"></i> Manage Students</a></li>
            <li class="active"><a href="book.php"><i class="bi bi-journal-text"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper"></i> Pending Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Book Information Preview</h5>
            </div>
            <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="32" class="me-2 shadow-sm rounded-circle">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo strtolower($admin_name); ?></span>
            </div>
        </nav>

        <div class="card-container">
            <i class="bi bi-journal-bookmark-fill watermark-icon"></i>

            <div class="row g-0 position-relative">
                <div class="col-lg-8">
                    <span class="id-badge shadow-sm"><i class="bi bi-hash me-1"></i> Catalog ID: <?php echo $book_id; ?></span>

                    <div class="info-label">Book Title / Nomenclature</div>
                    <div class="info-value" style="font-size: 32px; letter-spacing: -0.5px;">
                        <?php echo htmlspecialchars($title); ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Primary Author</div>
                            <div class="info-value" style="font-size: 18px; color: #4a5568;">
                                <i class="bi bi-person-check text-primary me-2"></i><?php echo htmlspecialchars($author); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">System Status</div>
                            <div class="info-value">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">
                                    <i class="bi bi-info-circle me-1"></i> <?php echo strtoupper($status); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-label">Availability in Archives</div>
                    <div class="mt-2">
                        <span class="avail-badge">
                            <i class="bi bi-check2-all me-2 fs-5"></i> Currently <strong><?php echo $avail; ?> copies</strong> available for loan
                        </span>
                    </div>
                </div>

                <div class="col-lg-4 text-end d-flex flex-column justify-content-end mt-4 mt-lg-0">
                    <div class="d-flex flex-column gap-3 align-items-lg-end">
                        <a href="book.php" class="btn btn-outline-custom w-100" style="max-width: 200px;">
                            <i class="bi bi-arrow-left me-2"></i> Go Back
                        </a>
                        <a href="edit_book_details.php?id=<?php echo $book_id; ?>" class="btn btn-outline-edit w-100" style="max-width: 200px;">
                            <i class="bi bi-pencil-square me-2"></i> Edit Record
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted" style="font-size: 13px; opacity: 0.7;">
    &copy; <?php echo date('Y'); ?> <strong>LMS Pro</strong> - Global Archive Management System.
</footer>

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