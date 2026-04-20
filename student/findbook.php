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
    <title>Find a Book - Modern LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            overflow-x: hidden;
        }

        /* ===== Sidebar Styling ===== */
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

        /* ===== Hero Search Section ===== */
        .hero-search-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            text-align: center;
            margin-bottom: 30px;
            border: 1px solid rgba(0,0,0,0.02);
        }
        
        .search-wrapper {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input-lg {
            border-radius: 50px;
            padding: 18px 30px 18px 55px;
            font-size: 1.1rem;
            border: 2px solid #e9ecef;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
            transition: all 0.3s;
        }

        .search-input-lg:focus {
            border-color: #4e54c8;
            box-shadow: 0 8px 25px rgba(78, 84, 200, 0.15);
            outline: none;
        }

        .search-icon-inside {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.4rem;
            color: #adb5bd;
            z-index: 5;
        }

        .search-btn-lg {
            position: absolute;
            right: 8px;
            top: 8px;
            bottom: 8px;
            border-radius: 40px;
            padding: 0 30px;
            background: #4e54c8;
            color: white;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .search-btn-lg:hover {
            background: #3b3f98;
        }

        /* ===== Results Table Styling ===== */
        .results-container {
            background: white; border-radius: 18px; padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        }
        
        .table thead th {
            background-color: transparent; color: #adb5bd; font-weight: 600;
            text-transform: uppercase; font-size: 0.85rem; border-bottom: 2px solid #f4f7fa; padding-bottom: 15px;
        }
        .table tbody tr { transition: background-color 0.2s; }
        .table tbody tr:hover { background-color: #f8f9fa; }
        .table tbody td {
            vertical-align: middle; border-bottom: 1px solid #f4f7fa;
            padding: 15px 10px; color: #495057; font-weight: 500;
        }

        /* Empty / Initial State */
        .illustration-state {
            text-align: center;
            padding: 40px 20px;
        }
        .illustration-state i {
            font-size: 5rem;
            color: #e9ecef;
            margin-bottom: 15px;
            display: block;
        }

        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: absolute; }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
            .search-btn-lg { position: relative; right: auto; top: auto; bottom: auto; width: 100%; margin-top: 15px; padding: 15px; }
            .search-input-lg { padding-right: 30px; }
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
            <li class="active">
                <a href="findbook.php"><i class="bi bi-search"></i> Find Book</a>
            </li>
            <li>
                <a href="history.php"><i class="bi bi-clock-history"></i> Borrow History</a>
            </li>
            <li>
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
                <h5 class="fw-bold mb-0 text-dark">Library Search Engine</h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <h6 class="mb-0 fw-bold text-dark"><?php echo explode(' ', trim($user_name))[0]; ?></h6>
                    <small class="text-muted">ID: <?php echo $rollno; ?></small>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=4e54c8&color=fff&rounded=true&bold=true" alt="Profile" width="45">
            </div>
        </nav>

        <div class="hero-search-card">
            <h2 class="fw-bold text-dark mb-3">What are you looking for?</h2>
            <p class="text-muted mb-4">Discover millions of resources, books, and study materials.</p>
            
            <form action="findbook.php" method="post" class="mb-0">
                <div class="search-wrapper">
                    <i class="bi bi-search search-icon-inside"></i>
                    <input type="text" name="search_query" class="form-control search-input-lg w-100" placeholder="Enter book title or ID..." value="<?php echo isset($_POST['search_query']) ? htmlspecialchars($_POST['search_query']) : ''; ?>" required>
                    <button type="submit" name="submit" class="search-btn-lg">Search</button>
                </div>
            </form>
        </div>

        <?php
        if(isset($_POST['submit'])) {
            $search = $conn->real_escape_string($_POST['search_query']);
            $sql = "SELECT * FROM LMS.book WHERE Title LIKE '%$search%' OR BookId = '$search'";
            $result = $conn->query($sql);

            echo '<div class="results-container">';
            echo "<h5 class='fw-bold mb-4'>Search Results for: <span class='text-primary'>".htmlspecialchars($search)."</span></h5>";

            if($result && $result->num_rows > 0) {
        ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th>Publisher</th>
                                <th>Availability</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { 
                                $bookid = $row['BookId'];
                                $title = $row['Title'] ?? 'Unknown';
                                $publisher = $row['Publisher'] ?? 'N/A';
                                $avail = $row['Availability'] ?? 0;
                            ?>
                                <tr>
                                    <td><span class="text-muted fw-semibold">#<?php echo $bookid; ?></span></td>
                                    <td><strong class="text-dark"><?php echo $title; ?></strong></td>
                                    <td><?php echo $publisher; ?></td>
                                    <td>
                                        <?php if($avail > 0): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Available (<?php echo $avail; ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="bookdetails.php?id=<?php echo $bookid; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                            View Details <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
        <?php 
            } else {
                echo '<div class="illustration-state">';
                echo '<i class="bi bi-search text-muted" style="opacity: 0.3;"></i>';
                echo '<h5 class="text-dark fw-bold">No books found</h5>';
                echo '<p class="text-muted">We couldn\'t find any matches for your search. Try different keywords.</p>';
                echo '</div>';
            }
            echo '</div>';
        } else {
        
        ?>
            <div class="results-container illustration-state pb-5">
                <i class="bi bi-journal-text text-primary" style="opacity: 0.2;"></i>
                <h5 class="text-dark fw-bold">Ready to learn?</h5>
                <p class="text-muted">Type a book name or ID in the search bar above to get started.</p>
            </div>
        <?php } ?>

    </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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