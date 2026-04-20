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
    <title>Manage Students - Librarian Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* ===== Sidebar ===== */
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar {
            min-width: 260px; max-width: 260px; background: var(--card-bg);
            transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03);
            min-height: 100vh; z-index: 999;
        }
        #sidebar.active { margin-left: -260px; }
        .sidebar-header {
            padding: 25px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; text-align: center;
        }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500;
        }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 4px solid var(--primary);
        }

        /* ===== Content ===== */
        #content { width: 100%; padding: 20px 40px; }
        
        .top-navbar {
            background: var(--card-bg); padding: 15px 25px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
        }

        /* ===== Search Card ===== */
        .search-card {
            background: white; border-radius: 20px; padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 30px;
            border: none;
        }
        .search-input {
            border-radius: 12px; padding: 12px 20px; border: 2px solid #eee;
            transition: 0.3s;
        }
        .search-input:focus { border-color: var(--primary); box-shadow: none; }
        .btn-search {
            background: var(--primary); color: white; border-radius: 12px;
            padding: 12px 25px; font-weight: 600; transition: 0.3s;
        }
        .btn-search:hover { background: var(--primary-light); color: white; transform: translateY(-2px); }

        /* ===== Table Styling ===== */
        .custom-table-card {
            background: white; border-radius: 20px; padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: none;
        }
        .table thead th {
            background: transparent; color: #adb5bd; font-weight: 600;
            text-transform: uppercase; font-size: 0.8rem; border-bottom: 2px solid #f8f9fa;
            padding-bottom: 15px;
        }
        .table tbody tr { transition: 0.2s; cursor: default; }
        .table tbody tr:hover { background-color: #f8f9fc; }
        .table td { vertical-align: middle; padding: 15px 10px; border-bottom: 1px solid #f8f9fa; }

        .student-avatar {
            width: 45px; height: 45px; border-radius: 12px;
            margin-right: 15px; object-fit: cover;
        }
        
        .btn-details {
            border-radius: 10px; font-weight: 600; padding: 6px 15px;
            font-size: 0.85rem; transition: 0.3s;
        }

        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state i { font-size: 4rem; color: #e9ecef; }

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
            <li class="active"><a href="student.php"><i class="bi bi-people"></i> Manage Students</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper"></i> Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3" style="background: var(--primary); color: white;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Student Management</h5>
            </div>
            <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 shadow-sm">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="search-card">
            <form action="student.php" method="post">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                            <input type="text" name="title" class="form-control search-input border-start-0" placeholder="Search by Student Name or University ID..." value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="submit" class="btn btn-search w-100 mt-2 mt-md-0 shadow-sm">Search Records</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="custom-table-card">
            <?php
            if(isset($_POST['submit'])) {
                $s = $conn->real_escape_string($_POST['title']);
                $sql = "SELECT * FROM LMS.user WHERE (RollNo='$s' OR Name LIKE '%$s%') AND Type='Student'";
                echo "<p class='text-muted mb-4 ms-2'>Showing results for: <strong>".htmlspecialchars($s)."</strong> <a href='student.php' class='text-decoration-none ms-2'>(Clear)</a></p>";
            } else {
                $sql = "SELECT * FROM LMS.user WHERE Type='Student' ORDER BY Name ASC";
            }

            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Student Info</th>
                                <th>University ID</th>
                                <th>Email Address</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { 
                                $s_name = $row['Name'];
                                $s_roll = $row['RollNo'];
                                $s_email = $row['EmailId'];
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s_name); ?>&background=random&color=fff&rounded=true&bold=true" class="student-avatar shadow-sm">
                                            <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($s_name); ?></h6>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border fw-semibold">#<?php echo htmlspecialchars($s_roll); ?></span></td>
                                    <td><span class="text-muted"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($s_email); ?></span></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="studentdetails.php?id=<?php echo $s_roll; ?>" class="btn btn-outline-primary btn-details">
                                                <i class="bi bi-eye-fill me-1"></i> View
                                            </a>
                                            <a href="remove_student.php?id=<?php echo $s_roll; ?>" 
                                               class="btn btn-outline-danger btn-details" 
                                               onclick="return confirm('Are you sure you want to PERMANENTLY remove this student and all their history?');">
                                                <i class="bi bi-trash3-fill me-1"></i> Remove
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
                echo '<div class="empty-state">
                        <i class="bi bi-person-x opacity-25"></i>
                        <h5 class="fw-bold mt-3">No Students Found</h5>
                        <p class="text-muted">We couldn\'t find any records matching your search criteria.</p>
                        <a href="student.php" class="btn btn-primary rounded-pill px-4 mt-2">Show All Students</a>
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