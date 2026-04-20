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
    $rno = $conn->real_escape_string($_GET['id']);
    $sql_student = "SELECT * FROM LMS.user WHERE RollNo='$rno'";
    $res_student = $conn->query($sql_student);
    
    if($res_student && $res_student->num_rows > 0) {
        $student = $res_student->fetch_assoc();
        $s_name = $student['Name'];
        $s_email = $student['EmailId'];
        $s_mob = $student['MobNo'];
    } else {
        echo "<script>alert('Student not found!'); window.location='student.php';</script>";
        exit();
    }
} else {
    header("Location: student.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - <?php echo $s_name; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --bg-color: #f4f7fa;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: #2b3452;
        }

        /* Sidebar Same Style */
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: 260px; max-width: 260px; background: var(--card-bg); transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -260px; }
        .sidebar-header { padding: 25px 20px; background: linear-gradient(135deg, var(--primary) 0%, #7b81ea 100%); color: white; text-align: center; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 4px solid var(--primary); }

        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: var(--card-bg); padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }

        /* Profile Details Card */
        .profile-card {
            background: white; border-radius: 25px; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none;
            max-width: 700px; margin: 0 auto;
        }
        .profile-header-bg {
            height: 140px; background: linear-gradient(135deg, var(--primary) 0%, #7b81ea 100%);
        }
        .profile-body { padding: 40px; text-align: center; margin-top: -80px; }
        .profile-img-lg {
            width: 150px; height: 150px; border-radius: 50%; border: 8px solid #fff;
            background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 20px;
        }

        .info-list { text-align: left; margin-top: 30px; }
        .info-item {
            display: flex; align-items: center; padding: 15px; border-radius: 15px;
            background: #f8f9fc; margin-bottom: 12px; transition: 0.3s;
        }
        .info-item:hover { background: #f0f2ff; transform: translateX(5px); }
        .info-icon {
            width: 45px; height: 45px; border-radius: 12px; background: rgba(93, 99, 212, 0.1);
            color: var(--primary); display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-right: 20px;
        }
        .info-label { font-size: 0.8rem; color: #8c98a4; text-transform: uppercase; font-weight: 600; margin-bottom: 2px; }
        .info-value { font-size: 1rem; color: #2b3452; font-weight: 700; }

        .btn-back {
            background: var(--primary); color: white; border-radius: 12px;
            padding: 12px 30px; font-weight: 600; border: none; transition: 0.3s;
        }
        .btn-back:hover { background: #4a50b5; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(93, 99, 212, 0.3); }

        @media (max-width: 768px) { #sidebar { margin-left: -260px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } }
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
            <li><a href="requests.php"><i class="bi bi-envelope-paper"></i> Pending Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3" style="background: var(--primary); color: white;"><i class="bi bi-list fs-5"></i></button>
                <h5 class="fw-bold mb-0 text-dark">Student Profile Details</h5>
            </div>
        </nav>

        <div class="profile-card">
            <div class="profile-header-bg"></div>
            <div class="profile-body">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s_name); ?>&size=200&background=random&color=fff&bold=true" class="profile-img-lg shadow-sm">
                
                <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($s_name); ?></h3>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">Active Student</span>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-hash"></i></div>
                        <div>
                            <div class="info-label">University ID (Roll No)</div>
                            <div class="info-value"><?php echo htmlspecialchars($rno); ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($s_email); ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="bi bi-telephone"></i></div>
                        <div>
                            <div class="info-label">Mobile Number</div>
                            <div class="info-value"><?php echo htmlspecialchars($s_mob); ?></div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="student.php" class="btn btn-back shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i> Back to Student List
                    </a>
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