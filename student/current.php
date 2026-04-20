<?php
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='../index.php';</script>";
    exit();
}

$rollno = $_SESSION['RollNo'];

$sql_user = "SELECT Name FROM LMS.user WHERE RollNo='$rollno'";
$result_user = $conn->query($sql_user);
$user_name = ($result_user && $result_user->num_rows > 0) ? $result_user->fetch_assoc()['Name'] : "Student";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currently Issued Books - Modern LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            overflow-x: hidden;
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

        /* ===== Main Content ===== */
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

        /* ===== Search Bar ===== */
        .search-card {
            background: white; border-radius: 15px; padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 25px;
        }
        .search-input {
            border-radius: 50px 0 0 50px; padding: 12px 25px;
            border: 1px solid #e9ecef; border-right: none; box-shadow: none !important;
        }
        .search-input:focus { border-color: #4e54c8; }
        .search-btn {
            border-radius: 0 50px 50px 0; padding: 12px 30px;
            background-color: #4e54c8; color: white; border: 1px solid #4e54c8;
            transition: all 0.3s;
        }
        .search-btn:hover { background-color: #3b3f98; color: white; }

        /* ===== Table Styling ===== */
        .custom-table-container {
            background: white; border-radius: 18px; padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        }
        .table thead th {
            background-color: transparent; color: #adb5bd; font-weight: 600;
            text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;
            border-bottom: 2px solid #f4f7fa; padding-bottom: 15px;
        }
        .table tbody tr { transition: background-color 0.2s; }
        .table tbody tr:hover { background-color: #f8f9fa; }
        .table tbody td {
            vertical-align: middle; border-bottom: 1px solid #f4f7fa;
            padding: 15px 10px; color: #495057; font-weight: 500;
        }
        .book-icon {
            width: 45px; height: 45px; background: rgba(78, 84, 200, 0.08);
            border-radius: 12px; display: flex; align-items: center;
            justify-content: center; color: #4e54c8; font-size: 1.2rem;
        }


        .btn-action {
            border-radius: 8px; padding: 6px 15px; font-size: 0.85rem; font-weight: 600;
            transition: all 0.2s;
        }
        
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 4rem; color: #dee2e6; margin-bottom: 15px; display: block; }
        .empty-state h5 { font-weight: 600; color: #6c757d; }

        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: absolute; }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
            .btn-action { width: 100%; margin-bottom: 5px; }
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
            <li>
                <a href="index.php"><i class="bi bi-grid-1x2-fill"></i> Home</a>
            </li>
            <li>
                <a href="book.php"><i class="bi bi-journal-album"></i> All Books</a>
            </li>
            <li>
                <a href="history.php"><i class="bi bi-clock-history"></i> Borrow History</a>
            </li>
            <li class="active">
                <a href="current.php"><i class="bi bi-list-check"></i> Currently Issued</a>
            </li>
            <li class="mt-5 border-top pt-3">
                <a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left text-danger"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Currently Issued Books</h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <h6 class="mb-0 fw-bold text-dark"><?php echo explode(' ', trim($user_name))[0]; ?></h6>
                    <small class="text-muted">ID: <?php echo $rollno; ?></small>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=4e54c8&color=fff&rounded=true&bold=true" alt="Profile" width="45">
            </div>
        </nav>

        <div class="search-card">
            <form action="current.php" method="post" class="mb-0">
                <div class="row align-items-center">
                    <div class="col-md-8 offset-md-2">
                        <label class="form-label fw-semibold text-muted ms-2 mb-2">Find a currently borrowed book:</label>
                        <div class="input-group">
                            <input type="text" id="title" name="title" class="form-control search-input" placeholder="Enter Book Name or Book ID..." value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                            <button type="submit" name="submit" class="btn search-btn">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="custom-table-container">
            <?php
            if(isset($_POST['submit'])) {
                $s = $conn->real_escape_string($_POST['title']);
                $sql = "SELECT * FROM LMS.record, LMS.book WHERE RollNo = '$rollno' AND Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL AND book.Bookid = record.BookId AND (record.BookId='$s' OR Title LIKE '%$s%')";
                echo "<p class='text-muted mb-4'>Showing results for: <strong>".htmlspecialchars($s)."</strong> <a href='current.php' class='text-decoration-none ms-2'>(Clear Search)</a></p>";
            } else {
                $sql = "SELECT * FROM LMS.record, LMS.book WHERE RollNo = '$rollno' AND Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL AND book.Bookid = record.BookId";
            }

            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Book Details</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th class="text-center">Renewals Left</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $today = date('Y-m-d');

                            while($row = $result->fetch_assoc()) { 
                                $bookid = $row['BookId'];
                                $name = $row['Title'];
                                $issuedate = $row['Date_of_Issue'];
                                $duedate = $row['Due_Date'];
                                $renewals = $row['Renewals_left'];
                                
                                $is_overdue = ($duedate < $today);
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="book-icon me-3"><i class="bi bi-book"></i></div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo $name; ?></h6>
                                                <small class="text-muted">ID: #<?php echo $bookid; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-event text-muted me-2"></i><?php echo date('d M Y', strtotime($issuedate)); ?>
                                    </td>
                                    <td>
                                        <?php if($is_overdue): ?>
                                            <span class="text-danger fw-bold">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo date('d M Y', strtotime($duedate)); ?>
                                            </span>
                                            <div style="font-size: 0.75rem;" class="text-danger mt-1">Overdue!</div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="bi bi-calendar-check me-2"></i><?php echo date('d M Y', strtotime($duedate)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($renewals > 0): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill border border-info border-opacity-25">
                                                <?php echo $renewals; ?> Left
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill border border-secondary border-opacity-25">
                                                0 Left
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <?php if($renewals > 0): ?>
                                                <a href="renew_request.php?id=<?php echo $bookid; ?>" class="btn btn-outline-success btn-action">
                                                    <i class="bi bi-arrow-clockwise me-1"></i> Renew
                                                </a>
                                            <?php endif; ?>
                                            <a href="return_request.php?id=<?php echo $bookid; ?>" class="btn btn-outline-primary btn-action">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Return
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php 
            } else { 
            ?>
                <div class="empty-state">
                    <i class="bi bi-bookmark-dash"></i>
                    <h5>No Books Currently Issued</h5>
                    <p class="text-muted">You don't have any books borrowed right now.</p>
                    <a href="book.php" class="btn btn-primary rounded-pill px-4 mt-2">Browse & Borrow Books</a>
                </div>
            <?php } ?>
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