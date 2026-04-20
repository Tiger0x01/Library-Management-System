<?php
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];

$sql_user = "SELECT Name FROM user WHERE RollNo='$rollno'";
$result_user = $conn->query($sql_user);
$user_name = ($result_user && $result_user->num_rows > 0) ? $result_user->fetch_assoc()['Name'] : "Student";

$sql_my_books = "SELECT b.* FROM book b 
                 LEFT JOIN purchased_books p ON b.BookId = p.BookId AND p.RollNo = '$rollno'
                 WHERE b.is_online = 1 AND (b.price <= 0 OR p.BookId IS NOT NULL)
                 ORDER BY b.BookId DESC";

$result_books = $conn->query($sql_my_books);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Digital Library - LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root { --primary: #5D63D4; --primary-light: #7b81ea; --bg: #f4f7fa; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg); color: #2b3452; overflow-x: hidden;}
        .wrapper { display: flex; width: 100%; align-items: stretch;}
        
        /* ===== Sidebar ===== */
        #sidebar { min-width: 260px; max-width: 260px; background: #fff; min-height: 100vh; box-shadow: 4px 0 15px rgba(0,0,0,0.03); transition: 0.3s; z-index: 999;}
        #sidebar.active { margin-left: -260px; }
        .sidebar-header { padding: 25px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 15px 25px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; border-left: 4px solid transparent; transition: 0.3s;}
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 4px solid var(--primary); }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; }

        /* ===== Content ===== */
        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: #fff; padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }

        /* ===== Hero Section ===== */
        .library-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
            border-radius: 20px; padding: 30px 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;
        }
        .library-header-icon {
            width: 60px; height: 60px; background: rgba(93, 99, 212, 0.1); color: var(--primary);
            border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 20px;
        }

        /* ===== Book Card Styling ===== */
        .digital-book-card {
            background: white; border-radius: 20px; overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.04); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none; height: 100%; display: flex; flex-direction: column; position: relative;
        }
        .digital-book-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(93, 99, 212, 0.15); }
        
        .book-cover-wrapper {
            position: relative; height: 240px; overflow: hidden;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .book-cover-img {
            width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;
        }
        .digital-book-card:hover .book-cover-img { transform: scale(1.08); }
        
        .status-badge {
            position: absolute; top: 15px; right: 15px; padding: 6px 15px; border-radius: 50px;
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 2;
        }
        .badge-premium { background: rgba(255, 171, 0, 0.9); color: #fff; }
        .badge-free { background: rgba(5, 205, 153, 0.9); color: #fff; }

        .book-details { padding: 25px 20px; flex-grow: 1; display: flex; flex-direction: column; text-align: center;}
        .book-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; color: #2b3452; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { color: #888; font-size: 0.85rem; margin-bottom: 20px; font-weight: 500; }
        
        .btn-read-now {
            background: rgba(93, 99, 212, 0.1); color: var(--primary);
            border-radius: 12px; padding: 12px; font-weight: 700; width: 100%; border: none;
            transition: 0.3s; text-decoration: none; display: flex; justify-content: center; align-items: center;
            margin-top: auto;
        }
        .btn-read-now:hover { background: var(--primary); color: white; }

        /* ===== Empty State ===== */
        .empty-state { text-align: center; padding: 80px 20px; background: white; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .empty-icon { width: 120px; height: 120px; background: #f8f9fc; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 3.5rem; color: #cbd5e1; margin-bottom: 20px; }

        @media (max-width: 768px) { #sidebar { margin-left: -260px; position: absolute; } #sidebar.active { margin-left: 0; } #content { padding: 15px; } .library-header { flex-direction: column; text-align: center; gap: 15px; padding: 25px; } .library-header-icon { margin: 0 auto; } }
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
            <li><a href="index.php"><i class="bi bi-grid-1x2-fill"></i> Home</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album"></i> All Books</a></li>
            <li class="active"><a href="my_books.php"><i class="bi bi-bookmark-star-fill"></i> My Library</a></li>
            <li><a href="history.php"><i class="bi bi-clock-history"></i> Borrow History</a></li>
            <li><a href="current.php"><i class="bi bi-list-check"></i> Currently Issued</a></li>
            <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left text-danger"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3 text-white" style="background: var(--primary);">
                    <i class="bi bi-list fs-5"></i>
                </button>
            </div>
            <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border shadow-sm">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="38" class="me-2 rounded-circle">
                <div class="d-none d-md-block text-end">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;"><?php echo htmlspecialchars(explode(' ', trim($user_name))[0]); ?></h6>
                </div>
            </div>
        </nav>

        <div class="library-header">
            <div class="d-flex align-items-center flex-column flex-md-row">
                <div class="library-header-icon">
                    <i class="bi bi-collection-play-fill"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">My Digital Bookshelf</h3>
                    <p class="text-muted mb-0">Your personal collection of purchased and free digital reads.</p>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-4 py-2 rounded-pill fs-6">
                    <i class="bi bi-journal-bookmark-fill me-1"></i> <?php echo $result_books->num_rows; ?> Books Owned
                </span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <?php if($result_books && $result_books->num_rows > 0): ?>
                <?php while($row = $result_books->fetch_assoc()): 
                    $b_title = $row['Title'] ?? 'Unknown Book';
                    $b_author = $row['Author'] ?? 'Unknown Author';
                    $b_id = $row['BookId'];
                    $b_price = (float)$row['price'];
                    $cover_url = "https://ui-avatars.com/api/?name=".urlencode($b_title)."&background=random&color=fff&size=400&font-size=0.25&bold=true&length=3";
                ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="digital-book-card">
                            <div class="book-cover-wrapper">
                                <?php if($b_price > 0): ?>
                                    <div class="status-badge badge-premium"><i class="bi bi-star-fill me-1"></i> Premium</div>
                                <?php else: ?>
                                    <div class="status-badge badge-free"><i class="bi bi-gift-fill me-1"></i> Free</div>
                                <?php endif; ?>
                                
                                <img src="<?php echo $cover_url; ?>" alt="Book Cover" class="book-cover-img">
                            </div>
                            
                            <div class="book-details">
                                <h5 class="book-title" title="<?php echo htmlspecialchars($b_title); ?>"><?php echo htmlspecialchars($b_title); ?></h5>
                                <p class="book-author"><i class="bi bi-pen me-1"></i> <?php echo htmlspecialchars($b_author); ?></p>
                                
                                <a href="viewbook.php?id=<?php echo $b_id; ?>" class="btn-read-now mt-auto">
                                    <i class="bi bi-book-half me-2"></i> Read Book
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="bi bi-cloud-slash"></i></div>
                        <h3 class="fw-bold text-dark mb-2">Your Shelf is Empty</h3>
                        <p class="text-muted mb-4 fs-5">You haven't added any digital books to your collection yet.</p>
                        <a href="book.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm">
                            <i class="bi bi-search me-2"></i> Explore Library Catalog
                        </a>
                    </div>
                </div>
            <?php endif; ?>
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