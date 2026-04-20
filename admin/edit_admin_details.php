<?php
ob_start();
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

$row = $result_check->fetch_assoc();
$name = $row['Name'];
$email = $row['EmailId'];
$mobno = $row['MobNo'];
$pswd = $row['Password'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Settings - Modern LMS</title>
    
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

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: #2b3452; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* Sidebar */
        #sidebar { min-width: 280px; max-width: 280px; background: var(--card-bg); transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -280px; }
        .sidebar-header { padding: 30px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 15px 30px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; }
        #sidebar ul li a i { margin-right: 15px; font-size: 1.2em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 5px solid var(--primary); }

        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: #fff; padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }

        /* Form Card */
        .edit-card { background: white; border-radius: 28px; padding: 45px; box-shadow: 0 20px 50px rgba(0,0,0,0.04); max-width: 850px; margin: 0 auto; position: relative; }
        .edit-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px; background: linear-gradient(90deg, var(--primary), var(--primary-light)); border-radius: 28px 28px 0 0; }

        .avatar-preview { width: 120px; height: 120px; border-radius: 35px; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(93, 99, 212, 0.2); margin-bottom: 25px; transform: rotate(-5deg); }

        .form-label { font-weight: 700; color: #4b5563; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .input-group-text { background: #f8f9fc; border: 2px solid #f1f3f9; color: var(--primary); border-radius: 14px 0 0 14px; padding-left: 20px; padding-right: 20px; }
        .form-control { border-radius: 0 14px 14px 0; padding: 14px 18px; border: 2px solid #f1f3f9; background-color: #f8f9fc; font-weight: 500; transition: 0.3s; }
        .form-control:focus { border-color: var(--primary); background-color: white; box-shadow: 0 0 0 5px rgba(93, 99, 212, 0.08); }

        .btn-update-profile { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; border-radius: 16px; padding: 16px; font-weight: 700; border: none; transition: 0.4s; width: 100%; font-size: 1.05rem; box-shadow: 0 10px 20px rgba(93, 99, 212, 0.2); }
        .btn-update-profile:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(93, 99, 212, 0.3); color: white; }

        @media (max-width: 768px) { #sidebar { margin-left: -280px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } .edit-card { padding: 25px; } }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3 class="mb-0 fw-bold">LMS</h3>
            <small class="text-white-50">Librarian Portal</small>
        </div>
        <ul class="list-unstyled components">
            <li><a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people"></i> Manage Students</a></li>
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
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle shadow-sm me-3"><i class="bi bi-list fs-5"></i></button>
                <h5 class="fw-bold mb-0 text-dark">Account Settings</h5>
            </div>
            <div class="bg-light rounded-pill px-3 py-1 border d-flex align-items-center">
                 <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="28" class="me-2 rounded-circle">
                 <small class="fw-bold text-muted"><?php echo strtolower($name); ?></small>
            </div>
        </nav>

        <div class="edit-card text-center">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($name); ?>&size=250&background=5D63D4&color=fff&bold=true" class="avatar-preview shadow-lg" alt="Librarian">
            <h2 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($name); ?></h2>
            <p class="text-muted mb-5">System Administrator Profile • #<?php echo $rollno; ?></p>

            <form action="edit_admin_details.php?id=<?php echo $rollno ?>" method="post" class="text-start">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Full Administrator Name</label>
                        <div class="input-group shadow-sm border-0 rounded-4">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="Name" value="<?php echo htmlspecialchars($name) ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Official Email Address</label>
                        <div class="input-group shadow-sm border-0 rounded-4">
                            <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                            <input type="email" name="EmailId" value="<?php echo htmlspecialchars($email) ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Contact Mobile Number</label>
                        <div class="input-group shadow-sm border-0 rounded-4">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="MobNo" value="<?php echo htmlspecialchars($mobno) ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Secure Access Password</label>
                        <div class="input-group shadow-sm border-0 rounded-4">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" name="Password" value="<?php echo htmlspecialchars($pswd) ?>" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <a href="index.php" class="btn btn-outline-secondary w-100 py-3 fw-bold rounded-4 border-2">
                            <i class="bi bi-arrow-left me-2"></i> Cancel & Return
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="submit" class="btn btn-update-profile shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Save & Update Profile
                        </button>
                    </div>
                </div>
            </form>
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

<?php
if(isset($_POST['submit'])) {
    $rollno_id = $conn->real_escape_string($_GET['id']);
    $new_name = $conn->real_escape_string($_POST['Name']);
    $new_email = $conn->real_escape_string($_POST['EmailId']);
    $new_mobno = $conn->real_escape_string($_POST['MobNo']);
    $new_pswd = $conn->real_escape_string($_POST['Password']);

    $sql_update = "UPDATE LMS.user SET Name='$new_name', EmailId='$new_email', MobNo='$new_mobno', Password='$new_pswd' WHERE RollNo='$rollno_id'";

    if($conn->query($sql_update) === TRUE) {
        echo "<script type='text/javascript'>alert('Profile Updated Successfully! ✅'); window.location.href='index.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Oops! Error updating profile. ⚠️');</script>";
    }
}
?>
</body>
</html>