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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Requests - Librarian Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --primary-light: #7b81ea;
            --bg-color: #f4f7fa;
            --card-bg: #ffffff;
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
            padding: 30px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; text-align: center;
        }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500;
        }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.1em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 5px solid var(--primary);
        }

        /* ===== Content Area ===== */
        #content { width: 100%; padding: 30px; }
        .top-navbar {
            background: var(--card-bg); padding: 15px 25px; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 30px;
        }

        /* ===== Request Navigation Tabs ===== */
        .request-nav {
            background: white; border-radius: 15px; padding: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
            display: inline-flex; gap: 8px;
        }
        .nav-btn {
            border-radius: 10px; padding: 10px 22px; font-weight: 600;
            text-decoration: none; color: #6c757d; transition: 0.3s;
        }
        .nav-btn.active { background: var(--primary); color: white !important; box-shadow: 0 4px 10px rgba(93, 99, 212, 0.2); }
        .nav-btn:hover:not(.active) { background: #f0f2ff; color: var(--primary); }

        /* ===== Table Styling ===== */
        .custom-table-card {
            background: white; border-radius: 24px; padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.04); border: none;
        }
        .table thead th {
            background: #f8f9fa; color: #adb5bd; font-weight: 600;
            text-transform: uppercase; font-size: 0.75rem; border: none;
            padding: 15px; border-radius: 10px;
        }
        .table tbody tr { border-bottom: 1px solid #f1f1f1; transition: 0.3s; }
        .table tbody tr:hover { background-color: #fcfcff; }
        .table td { vertical-align: middle; padding: 20px 15px; }

        /* ===== Components ===== */
        .roll-badge { background: #eef0f8; color: var(--primary); font-weight: 700; padding: 8px 15px; border-radius: 10px; font-size: 0.85rem; }
        .avail-badge { padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; }
        .avail-yes { background: rgba(40, 199, 111, 0.1); color: #28c76f; }
        .avail-no { background: rgba(234, 84, 85, 0.1); color: #ea5455; }

        .btn-action { border-radius: 12px; font-weight: 600; padding: 8px 18px; font-size: 0.85rem; border: none; transition: 0.3s; }
        .btn-accept { background: #2ecc71; color: white; margin-right: 5px; }
        .btn-accept:hover { background: #27ae60; box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3); }
        .btn-reject { background: #e74c3c; color: white; }
        .btn-reject:hover { background: #c0392b; box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3); }

        .empty-state { text-align: center; padding: 80px 0; }
        .empty-state i { font-size: 5rem; color: #dfe4ea; }

        @media (max-width: 768px) {
            #sidebar { margin-left: -280px; position: absolute; }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
            .request-nav { width: 100%; display: flex; flex-direction: column; }
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
            <li><a href="addbook.php"><i class="bi bi-plus-circle"></i> Add New Book</a></li>
            <li class="active"><a href="requests.php"><i class="bi bi-envelope-paper"></i> Pending Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Issue Approvals</h5>
            </div>
            <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 shadow-sm">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="text-center">
            <div class="request-nav">
                <a href="issue_requests.php" class="nav-btn active">Issue Requests</a>
                <a href="renew_requests.php" class="nav-btn">Renew Requests</a>
                <a href="return_requests.php" class="nav-btn">Return Requests</a>
            </div>
        </div>

        <div class="custom-table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-journal-plus text-primary me-2"></i> Borrowing Requests</h5>
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill" style="background: rgba(93,99,212,0.1)">Processing Queue</span>
            </div>

            <?php
            $sql = "SELECT * FROM LMS.record, LMS.book WHERE Date_of_Issue is NULL AND record.BookId=book.BookId ORDER BY Time";
            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Book ID</th>
                                <th>Book Name</th>
                                <th class="text-center">Availability</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { 
                                $bid = $row['BookId'];
                                $rno = $row['RollNo'];
                                $title = $row['Title'];
                                $avail = $row['Availability'];
                            ?>
                                <tr>
                                    <td><span class="roll-badge"><?php echo strtoupper($rno); ?></span></td>
                                    <td><code class="fw-bold text-muted">#<?php echo $bid; ?></code></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($title); ?></div>
                                        <small class="text-muted">Requested for loan</small>
                                    </td>
                                    <td class="text-center">
                                        <?php if($avail > 0): ?>
                                            <span class="avail-badge avail-yes"><i class="bi bi-check-circle me-1"></i> <?php echo $avail; ?> Available</span>
                                        <?php else: ?>
                                            <span class="avail-badge avail-no"><i class="bi bi-x-circle me-1"></i> Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($avail > 0): ?>
                                            <a href="accept.php?id1=<?php echo $bid; ?>&id2=<?php echo $rno; ?>" class="btn btn-action btn-accept shadow-sm">Accept</a>
                                        <?php endif; ?>
                                        <a href="reject.php?id1=<?php echo $bid; ?>&id2=<?php echo $rno; ?>" class="btn btn-action btn-reject shadow-sm">Reject</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php 
            } else {
                echo '<div class="empty-state">
                        <i class="bi bi-journal-check opacity-25"></i>
                        <h5 class="fw-bold mt-3 text-muted">No Pending Requests</h5>
                        <p class="text-muted">All students have their books. No new issue requests found.</p>
                      </div>';
            }
            ?>
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