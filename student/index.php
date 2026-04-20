<?php
require('../includes/dbconn.php');

if(!isset($_SESSION['RollNo'])) {
    header("Location: ../index.php");
    exit();
}

$rollno = $_SESSION['RollNo'];

$sql = "SELECT * FROM user WHERE RollNo='$rollno'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['Name'] ?? 'Student';
    $category = $row['Category'] ?? 'N/A';
    $email = $row['EmailId'] ?? 'N/A';
    $mobno = $row['MobNo'] ?? 'N/A';
} else {
    $name = "Unknown"; $category = "N/A"; $email = "N/A"; $mobno = "N/A";
}

$purchased_books = [];
$sql_purchased = "SELECT BookId FROM purchased_books WHERE RollNo='$rollno'";
$res_purchased = $conn->query($sql_purchased);
if($res_purchased && $res_purchased->num_rows > 0) {
    while($p = $res_purchased->fetch_assoc()) {
        $purchased_books[] = $p['BookId'];
    }
}

$sql_total = "SELECT COUNT(*) as total FROM record WHERE RollNo='$rollno'";
$res_total = $conn->query($sql_total);
$total_borrowed = ($res_total) ? $res_total->fetch_assoc()['total'] : 0;

$sql_curr = "SELECT * FROM record JOIN book ON record.BookId = book.BookId WHERE RollNo='$rollno' AND Date_of_Return IS NULL";
$res_curr = $conn->query($sql_curr);
$currently_issued = ($res_curr) ? $res_curr->num_rows : 0;

$current_fines = "$0.00"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Modern LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            var(--primary): #5D63D4;
            --primary: #5D63D4;
            --primary-light: #7b81ea;
            --bg-color: #f4f7fa;
            --card-bg: #ffffff;
            --text-main: #333;
            --text-muted: #6c757d;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); overflow-x: hidden; color: var(--text-main); }

        /* ===== Sidebar ===== */
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: 260px; max-width: 260px; background: var(--card-bg); transition: all 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -260px; }
        .sidebar-header { padding: 25px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 15px 25px; font-size: 1.05em; display: block; color: var(--text-muted); text-decoration: none; transition: 0.3s ease; font-weight: 500; border-left: 4px solid transparent; }
        #sidebar ul li a i { margin-right: 12px; font-size: 1.2em; transition: 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 4px solid var(--primary); }

        /* ===== Main Content ===== */
        #content { width: 100%; padding: 20px 40px; min-height: 100vh; transition: all 0.3s; }
        
        .top-navbar { background: var(--card-bg); padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }
        #sidebarCollapse { background: var(--primary); color: white; border: none; border-radius: 8px; padding: 8px 15px; transition: 0.3s;}
        #sidebarCollapse:hover { background: var(--primary-light); color: white; }

        /* ===== Hero Banner ===== */
        .hero-banner { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); border-radius: 20px; padding: 40px 50px 70px 50px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(93, 99, 212, 0.2); margin-bottom: 0; }
        .hero-banner::after { content: '\F1BD'; font-family: 'bootstrap-icons'; position: absolute; right: -20px; top: -30px; font-size: 15rem; opacity: 0.1; transform: rotate(-15deg); }
        .hero-title { font-weight: 700; font-size: 2.2rem; margin-bottom: 10px; z-index: 2; position: relative;}
        .hero-subtitle { font-weight: 400; font-size: 1.1rem; opacity: 0.9; z-index: 2; position: relative; max-width: 600px;}

        /* ===== Stats Row ===== */
        .overlap-row { margin-top: -40px; position: relative; z-index: 10; }
        .stat-card-custom { background: var(--card-bg); border-radius: 20px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); display: flex; align-items: center; height: 100%; transition: transform 0.3s ease; border: none; }
        .stat-card-custom:hover { transform: translateY(-5px); }
        .stat-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 26px; margin-right: 20px; flex-shrink: 0;}
        .stat-info h6 { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 5px; text-transform: uppercase; }
        .stat-info h2 { font-size: 2rem; font-weight: 700; color: var(--text-main); margin: 0; line-height: 1; }

        /* ===== Glass Cards ===== */
        .glass-card { background: var(--card-bg); border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: none; padding: 25px; height: 100%; position: relative; }
        .shelf-item { display: flex; align-items: center; padding: 15px; border-radius: 12px; background: var(--bg-color); margin-bottom: 15px; transition: 0.2s; border: none; }
        .shelf-item:hover { background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .mini-book-cover { width: 45px; height: 65px; border-radius: 6px; background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px; box-shadow: 2px 4px 10px rgba(0,0,0,0.1); font-size: 1.2rem; flex-shrink: 0; }
        
        /* ===== Recommended Books ===== */
        .section-title { font-weight: 700; font-size: 1.4rem; color: var(--text-main); margin-bottom: 25px; display: flex; align-items: center;}
        .section-title i { color: var(--primary); margin-right: 12px; }

        .book-card { background: white; border-radius: 16px; padding: 20px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.03); transition: 0.3s; border: none; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden;}
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        .book-cover { width: 100%; height: 200px; border-radius: 10px; object-fit: cover; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .book-title { font-weight: 600; font-size: 1.05rem; color: var(--text-main); margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
        .book-author { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 15px; flex-grow: 1;}
        
        .btn-issue { background: rgba(93, 99, 212, 0.1); color: var(--primary); font-weight: 600; border-radius: 50px; padding: 10px 20px; width: 100%; transition: 0.3s; border: none; text-decoration: none; display: inline-block; text-align: center;}
        .btn-issue:hover { background: var(--primary); color: white; }
        
        .status-badge { position: absolute; top: 15px; right: 15px; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(5px);}
        .badge-free { background: rgba(5, 205, 153, 0.9); color: white; }
        .badge-premium { background: rgba(255, 171, 0, 0.9); color: white; }

        @media (max-width: 768px) { #sidebar { margin-left: -260px; position: absolute; z-index: 1000;} #sidebar.active { margin-left: 0; } #content { padding: 15px; } .overlap-row { margin-top: 15px; } .hero-banner { padding: 30px 20px 30px 20px; } }
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
            <li class="active"><a href="index.php"><i class="bi bi-grid-1x2-fill"></i> Home</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album"></i> All Books</a></li>
            
            <li><a href="my_books.php"><i class="bi bi-bookmark-star-fill text-warning"></i> My Library</a></li>
            
            <li><a href="findbook.php"><i class="bi bi-search"></i> Find Book</a></li>
            <li><a href="history.php"><i class="bi bi-clock-history"></i> Borrow History</a></li>
            <li><a href="current.php"><i class="bi bi-list-check"></i> Currently Issued</a></li>
            <li class="mt-4"><hr style="opacity: 0.1;"></li>
            <li><a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
            <li><a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-left text-danger"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn me-3">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark d-none d-sm-block">Dashboard</h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <h6 class="mb-0 fw-bold text-dark"><?php echo explode(' ', trim($name))[0]; ?></h6>
                    <small class="text-muted">ID: <?php echo htmlspecialchars($rollno); ?></small>
                </div>
                <a href="profile.php"><img src="https://ui-avatars.com/api/?name=<?php echo urlencode($name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" alt="Profile" width="45" class="shadow-sm"></a>
            </div>
        </nav>

        <div class="hero-banner">
            <h1 class="hero-title">Ready to dive into a new world? 🌍</h1>
            <p class="hero-subtitle">"A reader lives a thousand lives before he dies." Discover your next favorite book from our catalog today.</p>
        </div>

        <div class="row g-4 overlap-row mb-5 px-md-3">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card-custom">
                    <div class="stat-icon" style="background: rgba(93, 99, 212, 0.1); color: var(--primary);">
                        <i class="bi bi-journals"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Total Borrowed</h6>
                        <h2><?php echo $total_borrowed; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stat-card-custom">
                    <div class="stat-icon" style="background: rgba(5, 205, 153, 0.1); color: #05CD99;">
                        <i class="bi bi-bookmark-check"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Currently Issued</h6>
                        <h2><?php echo $currently_issued; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="stat-card-custom">
                    <div class="stat-icon" style="background: rgba(255, 171, 0, 0.1); color: #FFAB00;">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <div class="stat-info">
                        <h6>Digital Collection</h6>
                        <h2><?php echo count($purchased_books); ?> <span class="fs-6 text-muted fw-normal">Owned</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-8 col-lg-7">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i> Reading Journey</h5>
                    </div>
                    <div id="activityChart" style="min-height: 320px;"></div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-bookshelf text-primary me-2"></i> On My Shelf</h5>
                        <a href="current.php" class="text-primary text-decoration-none fw-semibold" style="font-size: 0.85rem;">View All</a>
                    </div>
                    
                    <?php if($currently_issued > 0): 
                        $sql_shelf = "SELECT * FROM record JOIN book ON record.BookId = book.BookId WHERE RollNo='$rollno' AND Date_of_Return IS NULL LIMIT 3";
                        $res_shelf = $conn->query($sql_shelf);
                        $colors = ['#ff9a9e', '#a18cd1', '#84fab0']; 
                        $i = 0;
                        while($shelf_book = $res_shelf->fetch_assoc()) {
                            $bg_color = $colors[$i % 3];
                            $i++;
                            $b_title_shelf = $shelf_book['Title'] ?? $shelf_book['title'] ?? 'Unknown Book';
                    ?>
                        <div class="shelf-item">
                            <div class="mini-book-cover" style="background: <?php echo $bg_color; ?>;">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-semibold mb-1 text-truncate" style="font-size: 0.95rem;"><?php echo htmlspecialchars($b_title_shelf); ?></h6>
                                <small class="text-danger fw-bold" style="font-size: 0.75rem;"><i class="bi bi-clock-history"></i> Due: <?php echo date('d M Y', strtotime($shelf_book['Due_Date'])); ?></small>
                            </div>
                        </div>
                    <?php } else: ?>
                        <div class="text-center py-5">
                            <div class="mini-book-cover mx-auto mb-3" style="background: #e9ecef; color: #adb5bd;"><i class="bi bi-journal-x"></i></div>
                            <h6 class="fw-bold text-muted">Your shelf is empty</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">Time to discover a new book!</p>
                            <a href="book.php" class="btn btn-sm btn-primary rounded-pill px-4 mt-2">Browse Catalog</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h4 class="section-title"><i class="bi bi-stars"></i> Librarian's Picks</h4>
        <div class="row g-4">
            <?php
            $sql_recom = "SELECT * FROM book WHERE Availability > 0 OR is_online = 1 ORDER BY RAND() LIMIT 4";
            $res_recom = $conn->query($sql_recom);
            
            if($res_recom && $res_recom->num_rows > 0) {
                while($b = $res_recom->fetch_assoc()) {
                    $b_id = $b['BookId'] ?? '';
                    $b_title = $b['Title'] ?? $b['title'] ?? 'Unknown Book';
                    $b_pub = $b['Author'] ?? $b['author'] ?? 'Unknown Author';
                    $is_online = $b['is_online'] ?? 0;
                    $price = $b['price'] ?? 0.00;
                    $is_purchased = in_array($b_id, $purchased_books);
                    
                    $cover_url = "https://ui-avatars.com/api/?name=".urlencode($b_title)."&background=random&color=fff&size=300&font-size=0.33&bold=true&length=2";
            ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="book-card">
                        
                        <?php if($is_online == 1): ?>
                            <?php if($price > 0): ?>
                                <span class="status-badge badge-premium"><i class="bi bi-star-fill me-1"></i> Premium</span>
                            <?php else: ?>
                                <span class="status-badge badge-free"><i class="bi bi-gift-fill me-1"></i> Free</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <img src="<?php echo $cover_url; ?>" class="book-cover" alt="Book Cover">
                        <h6 class="book-title" title="<?php echo htmlspecialchars($b_title); ?>"><?php echo htmlspecialchars($b_title); ?></h6>
                        <p class="book-author"><i class="bi bi-person text-muted me-1"></i> <?php echo htmlspecialchars($b_pub); ?></p>
                        
                        <div class="mt-auto pt-2">
                            <?php if($is_online == 1): ?>
                                <?php if($price == 0 || $is_purchased): ?>
                                    <a href="viewbook.php?id=<?php echo htmlspecialchars($b_id); ?>" class="btn-issue text-white" style="background: var(--primary);"><i class="bi bi-book-half me-1"></i> Read Now</a>
                                <?php else: ?>
                                    <a href="viewbook.php?id=<?php echo htmlspecialchars($b_id); ?>" class="btn-issue text-dark" style="background: #ffab00;"><i class="bi bi-cart3 me-1"></i> Buy for $<?php echo number_format($price, 2); ?></a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="bookdetails.php?id=<?php echo htmlspecialchars($b_id); ?>" class="btn-issue"><i class="bi bi-info-circle me-1"></i> View Details</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo "<div class='col-12'><div class='glass-card text-center p-5'><h5 class='text-muted'>No recommendations available right now.</h5></div></div>";
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

        var options = {
            series: [{ name: 'Books Read/Issued', data: [1, 2, 4, 3, 5, 7, 6] }],
            chart: {
                type: 'area', height: 320, fontFamily: 'Poppins, sans-serif',
                toolbar: { show: false }, zoom: { enabled: false }
            },
            colors: ['#5D63D4'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 4 },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                axisBorder: { show: false }, axisTicks: { show: false },
                labels: { style: { colors: '#6c757d', fontWeight: 500 } }
            },
            yaxis: { labels: { style: { colors: '#6c757d', fontWeight: 500 } } },
            grid: { borderColor: '#E9EDF7', strokeDashArray: 5, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
            tooltip: { theme: 'light' }
        };

        var chart = new ApexCharts(document.querySelector("#activityChart"), options);
        chart.render();
    });
</script>
</body>
</html>