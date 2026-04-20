<?php
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    echo "<script type='text/javascript'>alert('Access Denied!!!'); window.location='../index.php';</script>";
    exit();
}

$rollno = $_SESSION['RollNo'];
$update_msg = "";

if(isset($_POST['submit'])) {
    $name = $conn->real_escape_string($_POST['Name']);
    $email = $conn->real_escape_string($_POST['EmailId']);
    $mobno = $conn->real_escape_string($_POST['MobNo']);
    $pswd = $conn->real_escape_string($_POST['Password']);

    $sql_update = "UPDATE LMS.user SET Name='$name', EmailId='$email', MobNo='$mobno', Password='$pswd' WHERE RollNo='$rollno'";

    if($conn->query($sql_update) === TRUE) {
        $update_msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <strong>Awesome!</strong> Your profile has been updated successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>';
    } else {
        $update_msg = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Oops!</strong> Something went wrong while updating.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>';
    }
}

$sql = "SELECT * FROM LMS.user WHERE RollNo='$rollno'";
$result = $conn->query($sql);
if($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['Name'];
    $email = $row['EmailId'];
    $mobno = $row['MobNo'];
    $pswd = $row['Password'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Modern LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7fa; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: 260px; max-width: 260px; background: #ffffff; transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        .sidebar-header { padding: 25px 20px; background: linear-gradient(135deg, #5D63D4 0%, #7b81ea 100%); color: white; text-align: center; }
        #sidebar ul li a { padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: #5D63D4; background: rgba(93, 99, 212, 0.05); border-left: 4px solid #5D63D4; }
        #content { width: 100%; padding: 20px 40px; }
        .card-custom { background: white; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 25px; }
        .profile-cover { background: linear-gradient(135deg, #5D63D4 0%, #7b81ea 100%); height: 120px; position: relative; }
        .profile-avatar-container { text-align: center; margin-top: -60px; padding-bottom: 20px; position: relative; z-index: 2; }
        .avatar-lg { width: 120px; height: 120px; border-radius: 50%; border: 5px solid #ffffff; background: white; }
        .form-section-title { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 20px; border-bottom: 2px solid #f4f7fa; padding-bottom: 10px; }
        .btn-update { background: linear-gradient(135deg, #5D63D4 0%, #7b81ea 100%); color: white; padding: 12px 35px; border-radius: 50px; border: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header"><h3 class="mb-0 fw-bold">LMS</h3><small>Student Portal</small></div>
        <ul class="list-unstyled components">
            <li><a href="index.php"><i class="bi bi-grid-1x2-fill me-2"></i> Home</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album me-2"></i> All Books</a></li>
            <li><a href="findbook.php"><i class="bi bi-search me-2"></i> Find Book</a></li>
            <li><a href="history.php"><i class="bi bi-clock-history me-2"></i> Borrow History</a></li>
            <li><a href="current.php"><i class="bi bi-list-check me-2"></i> Currently Issued</a></li>
            <li class="active"><a href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <h5 class="fw-bold mb-4 text-dark">Profile Settings</h5>
        <?php echo $update_msg; ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card-custom">
                    <div class="profile-cover"></div>
                    <div class="profile-avatar-container">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($name); ?>&background=f4f7fa&color=5D63D4&bold=true" class="avatar-lg">
                        <h4 class="fw-bold text-dark mt-3 mb-0"><?php echo htmlspecialchars($name); ?></h4>
                        <p class="text-muted">@<?php echo htmlspecialchars($rollno); ?></p>
                    </div>
                    <div class="p-4 pt-0">
                        <ul class="list-unstyled text-muted">
                            <li class="mb-3"><i class="bi bi-envelope text-primary me-3"></i> <?php echo htmlspecialchars($email); ?></li>
                            <li><i class="bi bi-telephone text-primary me-3"></i> <?php echo htmlspecialchars($mobno); ?></li>
                            </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card-custom p-4 p-md-5">
                    <form action="" method="post">
                        <h5 class="form-section-title"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Personal Information</h5>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="Name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="EmailId" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="tel" name="MobNo" class="form-control" value="<?php echo htmlspecialchars($mobno); ?>" required>
                                </div>
                            </div>
                        </div>

                        <h5 class="form-section-title"><i class="bi bi-shield-lock-fill me-2 text-primary"></i> Security Settings</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" name="Password" class="form-control" value="<?php echo htmlspecialchars($pswd); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end mt-3 mt-md-0">
                                <button type="submit" name="submit" class="btn btn-update w-100">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>