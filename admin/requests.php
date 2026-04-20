<?php
require('../includes/dbconn.php');
if (!isset($_SESSION['RollNo'])) {
    header("Location: index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];
$sql_check = "SELECT * FROM LMS.user WHERE RollNo='$rollno' AND Type='Admin'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    echo "<script type='text/javascript'>alert('Access Denied: Admins Only!'); window.location.href='index.php';</script>";
    exit();
}

$admin_row = $result_check->fetch_assoc();
$admin_name = $admin_row['Name'] ?? 'Admin';


$res_req = $conn->query("SELECT COUNT(*) as total FROM LMS.record WHERE Date_of_Issue IS NULL");
$count_requests = $res_req->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests Dashboard - LMS Pro</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --primary-light: #7b81ea;
            --bg-color: #f4f7fa;
            --card-bg: #ffffff;
            --issue-color: #5D63D4;
            --renew-color: #FFB800;
            --return-color: #28c76f;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: #2b3452;
            overflow-x: hidden;
        }

        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* ===== Sidebar ===== */
        #sidebar {
            min-width: 280px; max-width: 280px; background: var(--card-bg);
            transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03);
            min-height: 100vh; z-index: 999;
        }
        #sidebar.active { margin-left: -280px; }
        .sidebar-header {
            padding: 25px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; text-align: center;
        }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500;
            transition: 0.3s; border-left: 4px solid transparent;
        }
        #sidebar ul li a i { margin-right: 15px; font-size: 1.1em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--primary); background: rgba(93, 99, 212, 0.08); border-left: 4px solid var(--primary);
        }

        /* ===== Main Content ===== */
        #content { width: 100%; padding: 30px; }
        
        .top-navbar {
            background: var(--card-bg); padding: 15px 25px; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 40px;
        }

        /* ===== Dashboard Cards ===== */
        .welcome-section { margin-bottom: 40px; }
        .welcome-section h2 { font-weight: 700; color: #1a1a1a; }

        .request-card {
            background: white;
            border-radius: 30px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(0,0,0,0.02);
            height: 100%;
            position: relative;
            overflow: hidden;
            text-decoration: none !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }

        .request-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .icon-circle {
            width: 90px;
            height: 90px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            font-size: 2.5rem;
            transition: 0.3s;
        }

      
        .card-issue .icon-circle { background: rgba(93, 99, 212, 0.1); color: var(--issue-color); }
        .card-issue:hover .icon-circle { background: var(--issue-color); color: white; }

        .card-renew .icon-circle { background: rgba(255, 184, 0, 0.1); color: var(--renew-color); }
        .card-renew:hover .icon-circle { background: var(--renew-color); color: white; }

        .card-return .icon-circle { background: rgba(40, 199, 111, 0.1); color: var(--return-color); }
        .card-return:hover .icon-circle { background: var(--return-color); color: white; }

        .request-card h4 { font-weight: 700; color: #2b3452; margin-bottom: 10px; }
        .request-card p { color: #8e94a9; font-size: 0.9rem; margin-bottom: 0; }

        .btn-go {
            margin-top: 20px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f8f9fa;
            color: #2b3452;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        .request-card:hover .btn-go { background: var(--primary); color: white; }

        @media (max-width: 768px) {
            #sidebar { margin-left: -280px; position: absolute; }
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
            <li><a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people me-2"></i> Manage Students</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album me-2"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle me-2"></i> Add New Book</a></li>
            <li class="active"><a href="requests.php"><i class="bi bi-envelope-paper me-2"></i> Requests 
                <?php if($count_requests > 0): ?><span class="badge bg-danger rounded-pill ms-1"><?php echo $count_requests; ?></span><?php endif; ?>
            </a></li>
            <li><a href="current.php"><i class="bi bi-journal-check me-2"></i> Issued Materials</a></li>
            <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="fw-bold mb-0">System Requests</h5>
            </div>
            
            <div class="d-flex align-items-center bg-white border rounded-pill p-1 pe-3 shadow-sm">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 rounded-circle">
                <span class="fw-bold small text-dark"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="welcome-section">
            <h2>What would you like to manage?</h2>
            <p class="text-muted">Select a category to view and process pending student requests.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="issue_requests.php" class="request-card card-issue">
                    <div class="icon-circle"><i class="bi bi-journal-plus"></i></div>
                    <h4>Issue Requests</h4>
                    <p>Approve or reject new book borrowing requests from students.</p>
                    <div class="btn-go shadow-sm"><i class="bi bi-arrow-right"></i></div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="renew_requests.php" class="request-card card-renew">
                    <div class="icon-circle"><i class="bi bi-arrow-repeat"></i></div>
                    <h4>Renew Requests</h4>
                    <p>Extend the due date for books already issued to students.</p>
                    <div class="btn-go shadow-sm"><i class="bi bi-arrow-right"></i></div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="return_requests.php" class="request-card card-return">
                    <div class="icon-circle"><i class="bi bi-journal-check"></i></div>
                    <h4>Return Requests</h4>
                    <p>Process returned books and clear student records/dues.</p>
                    <div class="btn-go shadow-sm"><i class="bi bi-arrow-right"></i></div>
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted">
    <small>&copy; <?php echo date('Y'); ?> <b>IIE Library Management System</b>. All rights reserved.</small>
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