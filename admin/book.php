<?php
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];
$sql_check = "SELECT * FROM book.user WHERE RollNo='$rollno' AND Type='Admin'"; // تم تعديل اسم قاعدة البيانات لتجنب الأخطاء، تأكد أنها تتوافق مع dbconn.php
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
    <title>Library Catalog - Librarian Portal</title>
    
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
            overflow-x: hidden;
        }

        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* ===== Sidebar ===== */
        #sidebar {
            min-width: 280px; max-width: 280px; background: var(--card-bg);
            transition: all 0.3s; box-shadow: 4px 0 20px rgba(0,0,0,0.03);
            min-height: 100vh; z-index: 999;
        }
        #sidebar.active { margin-left: -280px; }
        .sidebar-header {
            padding: 30px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white; text-align: center;
        }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 15px 30px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; transition: 0.3s;
        }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; }
        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 5px solid var(--primary);
        }

        /* ===== Content ===== */
        #content { width: 100%; padding: 20px 40px; }
        .top-navbar {
            background: var(--card-bg); padding: 15px 25px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;
        }

        /* ===== Search & Header ===== */
        .search-card {
            background: linear-gradient(to right, #ffffff, #f8f9fc);
            border-radius: 20px; padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 30px;
            border: 1px solid rgba(0,0,0,0.02);
        }
        .search-input {
            border-radius: 12px; padding: 12px 20px; border: 2px solid #eee; transition: 0.3s; background: white;
        }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(93,99,212,0.1); }
        
        .btn-search { background: var(--primary); color: white; border-radius: 12px; font-weight: 600; padding: 10px 25px; transition: 0.3s; }
        .btn-search:hover { background: #4a50b5; color: white; transform: translateY(-2px); }

        .btn-print {
            background: #05CD99; color: white; border-radius: 12px;
            padding: 12px 25px; font-weight: 600; border: none; transition: 0.3s;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-print:hover { background: #04b083; color: white; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(5, 205, 153, 0.2); }

        /* ===== Table Styling ===== */
        .custom-table-card {
            background: white; border-radius: 24px; padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.04); border: none;
        }
        .table thead th {
            background: transparent; color: #888; font-weight: 600;
            text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; 
            border-bottom: 2px solid #f4f7fa; padding-bottom: 15px;
        }
        .table tbody tr { transition: all 0.3s; border-radius: 12px; }
        .table tbody tr:hover { background-color: #f8f9fc; transform: scale(1.005); box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .table td { vertical-align: middle; padding: 20px 10px; border-bottom: 1px solid #f4f7fa; }

        .book-id-badge { background: #f0f2ff; color: var(--primary); font-weight: 700; padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; letter-spacing: 0.5px;}
        
        /* Book Icons */
        .icon-box-physical { width: 45px; height: 45px; background: rgba(93, 99, 212, 0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .icon-box-digital { width: 45px; height: 45px; background: rgba(255, 171, 0, 0.1); color: #FFAB00; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }

        /* Status Badges */
        .badge-digital { background: rgba(255, 171, 0, 0.1); color: #FFAB00; border: 1px solid rgba(255, 171, 0, 0.2); }
        .badge-physical { background: rgba(93, 99, 212, 0.1); color: var(--primary); border: 1px solid rgba(93, 99, 212, 0.2); }
        
        .avail-badge { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center;}
        .avail-yes { background: rgba(5, 205, 153, 0.1); color: #05CD99; border: 1px solid rgba(5, 205, 153, 0.2); }
        .avail-no { background: rgba(238, 93, 80, 0.1); color: #EE5D50; border: 1px solid rgba(238, 93, 80, 0.2); }
        .avail-unlimited { background: rgba(0, 176, 255, 0.1); color: #00b0ff; border: 1px solid rgba(0, 176, 255, 0.2); }

        .btn-action { border-radius: 10px; font-weight: 600; padding: 8px 16px; font-size: 0.85rem; transition: 0.3s; }
        .btn-action:hover { transform: translateY(-2px); }

        @media (max-width: 768px) { #sidebar { margin-left: -280px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } }
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
            <li><a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people me-2"></i> Manage Students</a></li>
            <li class="active"><a href="book.php"><i class="bi bi-journal-album me-2"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle me-2"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper me-2"></i>Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check me-2"></i> Issued Materials</a></li>
            
            <li class="mt-3"><small class="text-muted px-4 fw-bold text-uppercase" style="font-size: 0.7rem;">Analytics</small></li>
            <li><a href="sales.php"><i class="bi bi-cash-coin me-2"></i> Sales & Revenue</a></li>
            
            <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle shadow-sm me-3">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark">Master Catalog</h5>
            </div>
            <div class="d-flex align-items-center bg-white rounded-pill p-1 pe-3 border shadow-sm">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 shadow-sm rounded-circle">
                <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo $admin_name; ?></span>
            </div>
        </nav>

        <div class="search-card">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div class="flex-grow-1">
                    <form action="book.php" method="post" class="mb-0">
                        <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <span class="input-group-text bg-white border-0 text-primary ps-4"><i class="bi bi-search fs-5"></i></span>
                            <input type="text" name="title" class="form-control search-input border-0 bg-white" placeholder="Search by Book Title, Author or ID..." value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                            <button type="submit" name="submit" class="btn btn-search px-4">Search Catalog</button>
                        </div>
                    </form>
                </div>
                <div>
                    <a href="printbook.php" target="_blank" class="btn btn-print w-100">
                        <i class="bi bi-printer me-2 fs-5"></i> Print Catalog Report
                    </a>
                </div>
            </div>
        </div>

        <div class="custom-table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-collection text-primary me-2"></i> Inventory List</h5>
            </div>

            <?php
            if(isset($_POST['submit'])) {
                $s = $conn->real_escape_string($_POST['title']);
                $sql = "SELECT * FROM book WHERE BookId='$s' OR Title LIKE '%$s%' OR Author LIKE '%$s%' ORDER BY BookId DESC";
                echo "<div class='alert alert-light border shadow-sm mb-4'><i class='bi bi-funnel text-primary me-2'></i> Showing results for: <strong class='text-primary'>".htmlspecialchars($s)."</strong> <a href='book.php' class='text-decoration-none ms-3 badge bg-secondary'>Clear Filter</a></div>";
            } else {
                $sql = "SELECT * FROM book ORDER BY BookId DESC";
            }

            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Ref ID</th>
                                <th>Book Information</th>
                                <th>Format & Pricing</th>
                                <th>Availability Status</th>
                                <th class="text-end">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { 
                                $bid = $row['BookId'];
                                $title = $row['Title'] ?? 'Unknown';
                                $author = $row['Author'] ?? 'Unknown Author';
                                $avail = $row['Availability'] ?? 0;
                                
                        
                                $is_online = isset($row['is_online']) ? (int)$row['is_online'] : 0;
                                $price = isset($row['price']) ? (float)$row['price'] : 0.00;
                            ?>
                                <tr>
                                    <td><span class="book-id-badge">#<?php echo htmlspecialchars($bid); ?></span></td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="<?php echo ($is_online == 1) ? 'icon-box-digital' : 'icon-box-physical'; ?> me-3 shadow-sm">
                                                <i class="bi <?php echo ($is_online == 1) ? 'bi-laptop' : 'bi-book-half'; ?>"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($title); ?></h6>
                                                <small class="text-muted"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($author); ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if($is_online == 1): ?>
                                            <span class="badge badge-digital rounded-pill px-3 py-2 mb-1 d-inline-block">Digital PDF</span>
                                            <div class="fw-bold mt-1 <?php echo ($price > 0) ? 'text-dark' : 'text-success'; ?>">
                                                <?php echo ($price > 0) ? "$" . number_format($price, 2) : "<i class='bi bi-gift me-1'></i>Free"; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-physical rounded-pill px-3 py-2 mb-1 d-inline-block">Physical Book</span>
                                            <div class="fw-bold mt-1 text-muted">Library Issue</div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($is_online == 1): ?>
                                            <span class="avail-badge avail-unlimited"><i class="bi bi-infinity me-1"></i> Unlimited</span>
                                        <?php else: ?>
                                            <?php if($avail > 0): ?>
                                                <span class="avail-badge avail-yes"><i class="bi bi-check-circle-fill me-1"></i> <?php echo $avail; ?> in Stock</span>
                                            <?php else: ?>
                                                <span class="avail-badge avail-no"><i class="bi bi-x-circle-fill me-1"></i> Out of Stock</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="bookdetails.php?id=<?php echo $bid; ?>" class="btn btn-light border btn-action text-secondary shadow-sm" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_book_details.php?id=<?php echo $bid; ?>" class="btn btn-outline-primary btn-action shadow-sm" title="Edit Book">
                                                <i class="bi bi-pencil-square"></i> Edit
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
                echo '<div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                            <i class="bi bi-journal-x text-muted opacity-50" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="fw-bold mt-3">No Books Found</h5>
                        <p class="text-muted">Try using different keywords or check the Book ID.</p>
                        <a href="book.php" class="btn btn-primary rounded-pill px-4 mt-2 shadow-sm">Reset View</a>
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