<?php
ob_start();
require('../includes/dbconn.php');


if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];
$sql_check = "SELECT * FROM user WHERE RollNo='$rollno' AND Type='Admin'";
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
    <title>Catalog New Book - Librarian Portal</title>
    
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
        
        /* ===== Sidebar (Unified) ===== */
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

        /* ===== Content Area ===== */
        #content { width: 100%; padding: 20px 40px; }
        .top-navbar {
            background: var(--card-bg); padding: 15px 25px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
        }
        #sidebarCollapse { background: var(--primary); color: white; border: none; border-radius: 8px; padding: 8px 15px; }

        /* ===== Add Book Form Card ===== */
        .add-book-card {
            background: white; border-radius: 24px; padding: 45px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05); border: none;
            max-width: 850px; margin: 0 auto; position: relative; overflow: hidden;
        }
        .add-book-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
        }

        .form-label { font-weight: 600; color: #4b5563; font-size: 0.9rem; margin-bottom: 8px; }
        .input-group-text { background: #f8f9fc; border: 2px solid #f3f4f6; color: var(--primary); border-radius: 12px 0 0 12px; }
        .form-control, .form-select {
            border-radius: 0 12px 12px 0; padding: 12px 15px; border: 2px solid #f3f4f6;
            transition: 0.3s; background-color: #f8f9fc;
        }
        .no-group { border-radius: 12px !important; }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary); background-color: white; box-shadow: 0 0 0 4px rgba(93, 99, 212, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; border-radius: 14px; padding: 16px; font-weight: 700;
            border: none; transition: 0.3s; width: 100%; font-size: 1.1rem;
            box-shadow: 0 10px 20px rgba(93, 99, 212, 0.2);
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(93, 99, 212, 0.3); color: white; }

        .header-icon {
            width: 60px; height: 60px; background: rgba(93, 99, 212, 0.1);
            color: var(--primary); border-radius: 16px; display: flex;
            align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 20px;
        }

        @media (max-width: 768px) { #sidebar { margin-left: -260px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } .add-book-card { padding: 25px; } }
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
            <li class="active"><a href="addbook.php"><i class="bi bi-plus-circle"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper"></i> Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check"></i> Issued Materials</a></li>
            <li class="mt-5 pt-3 border-top"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3 shadow-sm">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Book Management</h5>
            </div>
            <div class="d-flex align-items-center bg-white rounded-pill p-1 pe-3 shadow-sm border">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="35" class="me-2 rounded-circle">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="add-book-card">
            <div class="header-icon">
                <i class="bi bi-journal-plus"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1">Catalog New Book</h2>
            <p class="text-muted mb-5">Fill in the details below to add a new physical or digital book to the collection.</p>

            <form action="addbook.php" method="post" enctype="multipart/form-data">
                
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label">Full Book Title</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-type"></i></span>
                            <input type="text" name="title" class="form-control" placeholder="E.g. The Great Gatsby" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Primary Author</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="author" class="form-control" placeholder="E.g. F. Scott Fitzgerald" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Book Type</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-laptop"></i></span>
                            <select name="is_online" id="is_online" class="form-select" required>
                                <option value="0">Physical Book</option>
                                <option value="1">Digital Book (Online/PDF)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4 digital-fields" style="display:none;">
                        <label class="form-label">Upload PDF</label>
                        <input type="file" name="book_file" id="book_file" class="form-control no-group" accept="application/pdf">
                    </div>

                    <div class="col-md-6 mb-4 digital-fields" style="display:none;">
                        <label class="form-label">Price ($) - 0 for Free</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="0.00">
                        </div>
                    </div>

                    <div class="col-md-6 mb-4" id="availability_div">
                        <label class="form-label">Available Copies</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-stack"></i></span>
                            <input type="number" name="availability" id="availability" class="form-control" placeholder="Number of copies" min="1" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-5" id="status_div">
                        <label class="form-label">Physical Condition / Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                            <select name="status" id="status" class="form-select" required>
                                <option value="" disabled selected>Select book status...</option>
                                <option value="New">Brand New Condition</option>
                                <option value="Old">Used / Fair Condition</option>
                                <option value="Lost">Reported Missing</option>
                                <option value="Damaged">Damaged / Repairs Needed</option>
                                <option value="Replacement">Replacement Copy</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6 mb-3">
                        <a href="book.php" class="btn btn-light border w-100 py-3 fw-bold rounded-4" style="border-radius: 14px;">
                            <i class="bi bi-arrow-left me-2"></i> Back to Library
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button type="submit" name="submit" class="btn btn-submit">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Register Book
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

        const isOnlineSelect = document.getElementById('is_online');
        const digitalFields = document.querySelectorAll('.digital-fields');
        const bookFileInput = document.getElementById('book_file');
        
        const availabilityDiv = document.getElementById('availability_div');
        const availabilityInput = document.getElementById('availability');
        
        const statusDiv = document.getElementById('status_div');
        const statusInput = document.getElementById('status');

        isOnlineSelect.addEventListener('change', function() {
            if (this.value === '1') { 
              
                digitalFields.forEach(el => el.style.display = 'block');
                bookFileInput.setAttribute('required', 'required');
                
              
                availabilityDiv.style.display = 'none';
                statusDiv.style.display = 'none';
                
           
                availabilityInput.removeAttribute('required');
                statusInput.removeAttribute('required');
                
            } else { 
              
                digitalFields.forEach(el => el.style.display = 'none');
                bookFileInput.removeAttribute('required');
                
    
                availabilityDiv.style.display = 'block';
                statusDiv.style.display = 'block';
                
                
                availabilityInput.setAttribute('required', 'required');
                statusInput.setAttribute('required', 'required');
                
            
                availabilityInput.value = '';
                statusInput.value = '';
            }
        });
    });
</script>

<?php

if(isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    
    $is_online = isset($_POST['is_online']) ? (int)$_POST['is_online'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.00;
    

    if ($is_online == 1) {
        $availability = 1;
        $status = 'Digital';
    } else {
        $availability = $conn->real_escape_string($_POST['availability']);
        $status = $conn->real_escape_string($_POST['status']);
    }

    $file_path = NULL;

    if($is_online == 1 && isset($_FILES['book_file']) && $_FILES['book_file']['error'] == 0) {
        $target_dir = "../uploads/books/"; 
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $clean_file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES["book_file"]["name"]));
        $file_name = time() . '_' . $clean_file_name;
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if($file_type == "pdf") {
            if(move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
                $file_path = "uploads/books/" . $file_name; 
            } else {
                echo "<script>alert('Failed to upload the file. Please check folder permissions.');</script>";
            }
        } else {
            echo "<script>alert('Only PDF files are allowed!');</script>";
            $is_online = 0;
            $availability = 0; 
        }
    }

    $file_path_sql = $file_path ? "'$file_path'" : "NULL";

    $sql_insert = "INSERT INTO book (Title, Author, Availability, status, is_online, file_path, price) 
                   VALUES ('$title', '$author', '$availability', '$status', $is_online, $file_path_sql, $price)";
    
    if($conn->query($sql_insert)) {
        echo "<script>
                alert('New Book has been added successfully! ✅');
                window.location.href = 'book.php';
              </script>";
    } else {
        echo "<script>
                alert('Oops! Book could not be added. Error: " . $conn->error . " ⚠️');
              </script>"; 
    }
}
?>
</body>
</html>