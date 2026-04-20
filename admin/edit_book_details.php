<?php
ob_start();
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];
$sql_check = "SELECT * FROM user WHERE RollNo='$rollno' AND Type='Admin'"; // إزالة LMS. لتوحيد السياق
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    header("Location: ../student/index.php");
    exit();
}

$admin_row = $result_check->fetch_assoc();
$admin_name = $admin_row['Name'] ?? 'Librarian';

if(isset($_GET['id'])) {
    $bookid = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM book WHERE BookId='$bookid'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['Title'];
        $author = $row['Author'] ?? '';
        $avail = $row['Availability'];
        $status = $row['status'];
        
        $is_online = isset($row['is_online']) ? (int)$row['is_online'] : 0;
        $price = isset($row['price']) ? (float)$row['price'] : 0.00;
        $current_file = $row['file_path'] ?? '';
    } else {
        header("Location: book.php");
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
    <title>Edit Book - Librarian Portal</title>
    
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

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: #2b3452; overflow-x: hidden;}
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* ===== Sidebar ===== */
        #sidebar { min-width: 260px; max-width: 260px; background: var(--card-bg); transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -260px; }
        .sidebar-header { padding: 25px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 4px solid var(--primary); }

        /* ===== Content ===== */
        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: var(--card-bg); padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }

        /* ===== Form Card ===== */
        .form-card {
            background: white; border-radius: 24px; padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.04); border: none;
            max-width: 900px; margin: 0 auto; position: relative; overflow: hidden;
        }
        .form-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(90deg, var(--primary), var(--primary-light)); }

        .form-label { font-weight: 600; color: #4b5563; font-size: 0.9rem; margin-bottom: 8px; }
        .input-group-text { background: #f8f9fc; border: 2px solid #f3f4f6; color: var(--primary); border-radius: 12px 0 0 12px; }
        .form-control, .form-select { border-radius: 0 12px 12px 0; padding: 12px 15px; border: 2px solid #f3f4f6; transition: 0.3s; background-color: #f8f9fc; }
        .no-group { border-radius: 12px !important; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); background-color: white; box-shadow: 0 0 0 4px rgba(93, 99, 212, 0.1); }

        .btn-update {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; border-radius: 12px; padding: 14px 30px; font-weight: 700; border: none; transition: 0.3s;
        }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(93, 99, 212, 0.2); color: white;}

        .book-icon-header {
            width: 60px; height: 60px; background: rgba(93, 99, 212, 0.1); color: var(--primary);
            border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 20px;
        }

        @media (max-width: 768px) { #sidebar { margin-left: -260px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } .form-card { padding: 25px; } }
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
            <li class="active"><a href="book.php"><i class="bi bi-journal-album"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper"></i> Pending Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li><a href="sales.php"><i class="bi bi-cash-coin me-2"></i> Sales & Revenue</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3 shadow-sm" style="background: var(--primary); color: white;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Update Catalog Item</h5>
            </div>
            <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 shadow-sm rounded-circle">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="form-card">
            <div class="book-icon-header">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Edit Book Details</h3>
            <p class="text-muted mb-4">Update information for Book ID: <span class="badge bg-light text-primary border fs-6">#<?php echo htmlspecialchars($bookid); ?></span></p>

            <form action="edit_book_details.php?id=<?php echo $bookid ?>" method="post" enctype="multipart/form-data">
                
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="Title">Book Title</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-type"></i></span>
                            <input type="text" id="Title" name="Title" value="<?php echo htmlspecialchars($title) ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="Author">Primary Author</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" id="Author" name="Author" value="<?php echo htmlspecialchars($author) ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Book Type</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-laptop"></i></span>
                            <select name="is_online" id="is_online" class="form-select" required>
                                <option value="0" <?php if($is_online == 0) echo 'selected'; ?>>Physical Book</option>
                                <option value="1" <?php if($is_online == 1) echo 'selected'; ?>>Digital Book (Online/PDF)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4 digital-fields" style="display:none;">
                        <label class="form-label">Update PDF File <small class="text-muted fw-normal">(Leave empty to keep current file)</small></label>
                        <input type="file" name="book_file" class="form-control no-group" accept="application/pdf">
                        <?php if($is_online == 1 && !empty($current_file)): ?>
                            <small class="text-success mt-1 d-block"><i class="bi bi-check-circle me-1"></i> Current file uploaded.</small>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-4 digital-fields" style="display:none;">
                        <label class="form-label">Price ($) - 0 for Free</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?php echo $price; ?>">
                        </div>
                    </div>

                    <div class="col-md-6 mb-4" id="availability_div">
                        <label class="form-label" for="Availability">Available Copies</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-stack"></i></span>
                            <input type="number" id="Availability" name="Availability" value="<?php echo $avail ?>" class="form-control" min="0">
                        </div>
                    </div>

                    <div class="col-md-6 mb-4" id="status_div">
                        <label class="form-label" for="status">Physical Condition / Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                            <select name="status" id="status" class="form-select">
                                <option value="New" <?php if($status == 'New') echo 'selected'; ?>>New Condition</option>
                                <option value="Old" <?php if($status == 'Old') echo 'selected'; ?>>Old / Used</option>
                                <option value="Lost" <?php if($status == 'Lost') echo 'selected'; ?>>Reported Lost</option>
                                <option value="Damaged" <?php if($status == 'Damaged') echo 'selected'; ?>>Damaged Item</option>
                                <option value="Replacement" <?php if($status == 'Replacement') echo 'selected'; ?>>Replacement Copy</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-3">
                    <a href="book.php" class="btn btn-light border py-3 fw-bold rounded-4 flex-grow-1" style="border-radius: 12px;">Cancel</a>
                    <button type="submit" name="submit" class="btn btn-update flex-grow-1 m-0"><i class="bi bi-save me-2"></i> Save Changes</button>
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
        sidebarCollapseBtn.addEventListener('click', function () { sidebar.classList.toggle('active'); });

   
        const isOnlineSelect = document.getElementById('is_online');
        const digitalFields = document.querySelectorAll('.digital-fields');
        const availabilityDiv = document.getElementById('availability_div');
        const statusDiv = document.getElementById('status_div');

        function toggleFields() {
            if (isOnlineSelect.value === '1') {
                digitalFields.forEach(el => el.style.display = 'block');
                availabilityDiv.style.display = 'none';
                statusDiv.style.display = 'none';
            } else {
                digitalFields.forEach(el => el.style.display = 'none');
                availabilityDiv.style.display = 'block';
                statusDiv.style.display = 'block';
            }
        }

  
        toggleFields();
        isOnlineSelect.addEventListener('change', toggleFields);
    });
</script>

<?php

if(isset($_POST['submit'])) {
    $bookid = $conn->real_escape_string($_GET['id']);
    $name = $conn->real_escape_string($_POST['Title']);  
    $author = $conn->real_escape_string($_POST['Author']);  
    
    $is_online = isset($_POST['is_online']) ? (int)$_POST['is_online'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.00;
    
    if ($is_online == 1) {
        $avail = 1;
        $status = 'Digital';
    } else {
        $avail = $conn->real_escape_string($_POST['Availability']);
        $status = $conn->real_escape_string($_POST['status']);
        $price = 0.00;
    }

    $file_update_query = "";

    if($is_online == 1 && isset($_FILES['book_file']) && $_FILES['book_file']['error'] == 0) {
        $target_dir = "../uploads/books/"; 
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        $clean_file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES["book_file"]["name"]));
        $file_name = time() . '_' . $clean_file_name;
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if($file_type == "pdf") {
            if(move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
                $file_path = "uploads/books/" . $file_name;
                $file_update_query = ", file_path='$file_path'"; // إضافة مسار الملف لجملة الـ SQL
            } else {
                echo "<script>alert('Failed to upload the new PDF file.');</script>";
            }
        } else {
            echo "<script>alert('Only PDF files are allowed! The file was not updated.');</script>";
        }
    } 
    else if ($is_online == 0) {
        $file_update_query = ", file_path=NULL";
    }

    $sql1 = "UPDATE book SET Title='$name', Author='$author', Availability='$avail', status='$status', is_online='$is_online', price='$price' $file_update_query WHERE BookId='$bookid'";

    if($conn->query($sql1) === TRUE) {
        echo "<script type='text/javascript'>alert('Book Updated Successfully! ✅'); window.location.href='book.php';</script>";
    } else { 
        echo "<script type='text/javascript'>alert('Error updating book: " . $conn->error . " ⚠️');</script>";
    }
}
?>
</body>
</html>