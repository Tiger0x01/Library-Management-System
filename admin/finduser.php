<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];
$sql_check = "SELECT * FROM LMS.user WHERE RollNo='$rollno' AND Type='Admin'";
$result_check = $conn->query($sql_check);
$admin_row = $result_check->fetch_assoc();
$admin_name = $admin_row['Name'] ?? 'Librarian';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find User - Librarian Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root { --primary: #5D63D4; --primary-light: #7b81ea; --bg: #f4f7fa; --card: #ffffff; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg); color: #2b3452; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* Sidebar (Unified) */
        #sidebar { min-width: 280px; max-width: 280px; background: var(--card); transition: 0.3s; box-shadow: 4px 0 20px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -280px; }
        .sidebar-header { padding: 30px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; }
        #sidebar ul li a { padding: 15px 30px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; }
        #sidebar ul li a:hover { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 5px solid var(--primary); }

        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: #fff; padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }

        /* Search Section */
        .search-container { background: white; border-radius: 24px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .search-box-input { background: #f8f9fc; border-radius: 16px; padding: 12px 20px; border: 2px solid #f1f3f9; transition: 0.3s; }
        .search-box-input:focus-within { border-color: var(--primary); background: white; box-shadow: 0 0 0 5px rgba(93,99,212,0.1); }
        .search-box-input input { border: none; background: transparent; outline: none; width: 100%; padding: 10px; font-weight: 500; }

        .btn-search-user { background: var(--primary); color: white; border-radius: 12px; padding: 12px 30px; font-weight: 600; border: none; transition: 0.3s; }
        .btn-search-user:hover { background: var(--primary-light); transform: translateY(-2px); }

        /* Results Card */
        .results-card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.04); border: none; }
        .table thead th { background: transparent; color: #adb5bd; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border-bottom: 2px solid #f8f9fa; padding-bottom: 15px; }
        .table td { vertical-align: middle; padding: 18px 10px; border-bottom: 1px solid #f8f9fa; }
        
        .user-avatar { width: 45px; height: 45px; border-radius: 12px; margin-right: 15px; object-fit: cover; }
        .roll-badge { background: #f0f2ff; color: var(--primary); font-weight: 700; padding: 6px 12px; border-radius: 10px; font-size: 0.85rem; }

        @media (max-width: 768px) { #sidebar { margin-left: -280px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3 class="mb-0 fw-bold">LMS Pro</h3>
            <small class="text-white-50">Librarian Panel</small>
        </div>
        <ul class="list-unstyled components">
            <li><a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people me-2"></i> Manage Students</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album me-2"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle me-2"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper me-2"></i> Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check me-2"></i> Issued Materials</a></li>
            <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle shadow-sm me-3"><i class="bi bi-list fs-5"></i></button>
                <h5 class="fw-bold mb-0 text-dark">User Directory Search</h5>
            </div>
        </nav>

        <div class="search-container text-center">
            <h3 class="fw-bold mb-2">Locate Student Profile 👤</h3>
            <p class="text-muted mb-4">Search by <b>Name</b> or <b>Roll Number</b> to access account details.</p>
            <form action="finduser.php" method="post" class="row g-3 justify-content-center">
                <div class="col-md-7">
                    <div class="search-box-input d-flex align-items-center">
                        <i class="bi bi-person-search text-muted me-2"></i>
                        <input type="text" name="search_user" placeholder="Type Student Name or ID..." required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="submit" class="btn btn-search-user w-100 h-100 shadow-sm">Search User</button>
                </div>
            </form>
        </div>

        <div class="results-card">
            <?php
            if(isset($_POST['submit'])) {
                $s = $conn->real_escape_string($_POST['search_user']);
                $sql = "SELECT * FROM LMS.user WHERE (RollNo='$s' OR Name LIKE '%$s%') AND Type='Student' ORDER BY Name ASC";
                $result = $conn->query($sql);
                
                if($result && $result->num_rows > 0) {
                    echo '<div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>University ID</th>
                                        <th>Contact Email</th>
                                        <th>Phone Number</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    while($row = $result->fetch_assoc()) {
                        $s_name = $row['Name'];
                        echo '<tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name='.urlencode($s_name).'&background=random&color=fff&rounded=true&bold=true" class="user-avatar shadow-sm">
                                        <div class="fw-bold">'.htmlspecialchars($s_name).'</div>
                                    </div>
                                </td>
                                <td><span class="roll-badge">#'.$row['RollNo'].'</span></td>
                                <td><i class="bi bi-envelope-at me-2 text-muted"></i>'.$row['EmailId'].'</td>
                                <td><i class="bi bi-phone me-2 text-muted"></i>'.$row['MobNo'].'</td>
                                <td class="text-end">
                                    <a href="studentdetails.php?id='.$row['RollNo'].'" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Profile</a>
                                </td>
                              </tr>';
                    }
                    echo '</tbody></table></div>';
                } else {
                    echo '<div class="text-center py-5"><i class="bi bi-person-x text-muted display-4"></i><h5 class="mt-3">No student found with this name or ID.</h5></div>';
                }
            } else {
                echo '<div class="text-center py-5 opacity-25"><i class="bi bi-people" style="font-size: 5rem;"></i><h5 class="mt-3">Enter criteria to start searching...</h5></div>';
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarCollapse').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>