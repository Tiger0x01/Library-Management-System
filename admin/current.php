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
$admin_name = $admin_row['Name'] ?? 'Librarian';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issued Books - Librarian Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --primary-light: #7b81ea;
            --bg-color: #f4f7fa;
            --card-bg: #ffffff;
            --danger: #ea5455;
            --success: #28c76f;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: #2b3452;
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
        }
        #sidebar ul li a i { margin-right: 15px; font-size: 1.1em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--primary); background: rgba(93, 99, 212, 0.08); border-left: 5px solid var(--primary);
        }

        /* ===== Content Area ===== */
        #content { width: 100%; padding: 30px; }
        .top-navbar {
            background: var(--card-bg); padding: 15px 25px; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 30px;
        }

        /* ===== Search Card ===== */
        .search-card {
            background: white; border-radius: 20px; padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 30px;
        }
        .search-input {
            border-radius: 12px; padding: 12px 20px; border: 2px solid #f1f1f1;
            transition: 0.3s;
        }
        .search-input:focus { border-color: var(--primary); box-shadow: none; }
        .btn-search {
            background: var(--primary); color: white; border-radius: 12px;
            padding: 12px 25px; font-weight: 600; transition: 0.3s; border: none;
        }
        .btn-search:hover { background: var(--primary-light); transform: translateY(-2px); }

        /* ===== Table Card ===== */
        .custom-table-card {
            background: white; border-radius: 25px; padding: 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.04); border: none;
        }
        .table thead th {
            background: #f8f9fa; color: #adb5bd; font-weight: 600;
            text-transform: uppercase; font-size: 0.75rem; border: none;
            padding: 15px; border-radius: 10px;
        }
        .table tbody tr { border-bottom: 1px solid #f8f9fa; transition: 0.3s; }
        .table tbody tr:hover { background-color: #fcfcff; }
        .table td { vertical-align: middle; padding: 20px 15px; }

        /* Status Badges */
        .status-badge {
            padding: 6px 14px; border-radius: 10px; font-weight: 700; font-size: 0.8rem;
        }
        .overdue { background: rgba(234, 84, 85, 0.1); color: var(--danger); }
        .on-time { background: rgba(40, 199, 111, 0.1); color: var(--success); }

        .book-info h6 { font-weight: 700; color: #2b3452; margin-bottom: 2px; }
        .student-roll { background: #eef0f8; color: var(--primary); font-weight: 700; padding: 5px 10px; border-radius: 8px; font-size: 0.8rem; }

        .empty-state { text-align: center; padding: 80px 0; }
        .empty-state i { font-size: 4rem; color: #e9ecef; }

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
            <li><a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people"></i> Manage Students</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album"></i> Library Books</a></li>
            <li ><a href="addbook.php"><i class="bi bi-plus-circle"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper"></i> Requests</a></li>
            <li class="active"><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-list fs-4 text-primary"></i>
                </button>
                <h4 class="fw-bold mb-0">Currently Issued Materials</h4>
            </div>
            <div class="d-flex align-items-center bg-white border rounded-pill p-1 pe-3">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 rounded-circle">
                <span class="fw-bold small text-dark"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="search-card">
            <form action="current.php" method="post">
                <div class="row g-3 align-items-center">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                            <input type="text" name="title" class="form-control search-input border-start-0" placeholder="Search by Roll No, Book ID, or Title..." required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="submit" class="btn btn-search w-100 shadow-sm">Filter Records</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="custom-table-card">
            <?php
            if(isset($_POST['submit'])) {
                $s = $conn->real_escape_string($_POST['title']);
                $sql = "SELECT record.BookId, RollNo, Title, Due_Date, Date_of_Issue, DATEDIFF(CURDATE(), Due_Date) as overdue_days 
                        FROM LMS.record, LMS.book 
                        WHERE (Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL AND book.Bookid = record.BookId) 
                        AND (RollNo='$s' OR record.BookId='$s' OR Title LIKE '%$s%')";
                echo "<p class='text-muted mb-4 ms-1'>Results for: <b>".htmlspecialchars($s)."</b> <a href='current.php' class='text-decoration-none ms-2'>(Clear)</a></p>";
            } else {
                $sql = "SELECT record.BookId, RollNo, Title, Due_Date, Date_of_Issue, DATEDIFF(CURDATE(), Due_Date) as overdue_days 
                        FROM LMS.record, LMS.book 
                        WHERE Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL AND book.Bookid = record.BookId";
            }

            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book ID</th>
                                <th>Book Details</th>
                                <th>Issue / Due Dates</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { 
                                $rno = $row['RollNo'];
                                $bid = $row['BookId'];
                                $title = $row['Title'];
                                $i_date = $row['Date_of_Issue'];
                                $d_date = $row['Due_Date'];
                                $overdue = $row['overdue_days'];
                            ?>
                                <tr>
                                    <td><span class="student-roll"><?php echo strtoupper($rno); ?></span></td>
                                    <td><code class="fw-bold text-muted">#<?php echo $bid; ?></code></td>
                                    <td class="book-info">
                                        <h6><?php echo htmlspecialchars($title); ?></h6>
                                        <small class="text-muted">Library Collection</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark"><i class="bi bi-calendar-check me-2 text-success"></i><?php echo $i_date; ?></div>
                                        <div class="small fw-bold text-muted"><i class="bi bi-calendar-x me-2 text-danger"></i><?php echo $d_date; ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if($overdue > 0): ?>
                                            <span class="status-badge overdue">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Overdue: <?php echo $overdue; ?> Days
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge on-time">
                                                <i class="bi bi-clock-history me-1"></i> On Time
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php 
            } else {
                echo '<div class="empty-state">
                        <i class="bi bi-journal-x opacity-25"></i>
                        <h5 class="fw-bold mt-4 text-dark">No Issued Books Found</h5>
                        <p class="text-muted">There are no records matching your current criteria.</p>
                        <a href="current.php" class="btn btn-outline-primary rounded-pill px-4 mt-2">View All Records</a>
                      </div>';
            }
            ?>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted">
    <small>&copy; <?php echo date('Y'); ?> <b>IIE Library Management</b>. All rights reserved.</small>
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