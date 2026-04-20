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

$row = $result_check->fetch_assoc();
$name = $row['Name'] ?? 'Librarian';

$count_students = ($conn->query("SELECT COUNT(*) as t FROM LMS.user WHERE Type='Student'"))->fetch_assoc()['t'];
$count_books = ($conn->query("SELECT COUNT(*) as t FROM LMS.book"))->fetch_assoc()['t'];
$count_requests = ($conn->query("SELECT COUNT(*) as t FROM LMS.record WHERE Date_of_Issue IS NULL"))->fetch_assoc()['t'];
$total_issued = ($conn->query("SELECT COUNT(*) as t FROM LMS.record WHERE Date_of_Issue IS NOT NULL AND Date_of_Return IS NULL"))->fetch_assoc()['t'];

$sales_sql = "SELECT SUM(b.price) as total_revenue, COUNT(*) as total_sold 
              FROM LMS.purchased_books p 
              JOIN LMS.book b ON p.BookId = b.BookId";
$sales_result = $conn->query($sales_sql);
$sales_data = $sales_result->fetch_assoc();

$total_revenue = $sales_data['total_revenue'] ?? 0.00;
$total_sold = $sales_data['total_sold'] ?? 0;

$buyers_sql = "SELECT COUNT(DISTINCT RollNo) as unique_buyers FROM LMS.purchased_books";
$buyers_result = $conn->query($buyers_sql);
$total_buyers = $buyers_result->fetch_assoc()['unique_buyers'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Pro - Librarian Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root { --primary: #5D63D4; --primary-light: #7b81ea; --bg: #f4f7fa; --card: #ffffff; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg); color: #2b3452; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* Sidebar */
        #sidebar { min-width: 280px; max-width: 280px; background: var(--card); transition: 0.3s; box-shadow: 4px 0 20px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -280px; }
        .sidebar-header { padding: 30px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; }
        #sidebar ul li a { padding: 15px 30px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; transition: 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 5px solid var(--primary); }

        /* Main Content */
        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: #fff; padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }

        /* Hero & Boxes */
        .hero-banner { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); border-radius: 24px; padding: 40px; color: white; position: relative; overflow: hidden; margin-bottom: 40px; }
        .hero-banner::after { content: '\F1BD'; font-family: 'bootstrap-icons'; position: absolute; right: -20px; bottom: -40px; font-size: 12rem; opacity: 0.1; }

        /* Quick Search Boxes */
        .big-box {
            background: white; border-radius: 20px; padding: 25px; text-align: center;
            transition: 0.3s; border: 1px solid rgba(0,0,0,0.02); box-shadow: 0 10px 20px rgba(0,0,0,0.02);
            height: 100%; text-decoration: none; color: inherit; display: block;
        }
        .big-box:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(93, 99, 212, 0.1); border-color: var(--primary); }
        .box-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 15px; }

        /* Status Cards */
        .stat-card { background: white; border-radius: 18px; padding: 20px; display: flex; align-items: center; transition: 0.3s; border: none; box-shadow: 0 8px 20px rgba(0,0,0,0.02); height: 100%; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon-circle { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-right: 15px; flex-shrink: 0;}

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
            <li class="active"><a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="student.php"><i class="bi bi-people me-2"></i> Manage Students</a></li>
            <li><a href="book.php"><i class="bi bi-journal-album me-2"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle me-2"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper me-2"></i>Requests 
                <?php if($count_requests > 0): ?><span class="badge bg-danger rounded-pill ms-1"><?php echo $count_requests; ?></span><?php endif; ?>
            </a></li>
            <li><a href="current.php"><i class="bi bi-journal-check me-2"></i> Issued Materials</a></li>
            
            <li class="mt-3"><small class="text-muted px-4 fw-bold text-uppercase" style="font-size: 0.7rem;">Analytics</small></li>
            <li><a href="sales.php"><i class="bi bi-cash-coin me-2"></i> Sales & Revenue</a></li>
            
            <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-list fs-5"></i></button>
            <div class="dropdown">
                <div class="d-flex align-items-center bg-white rounded-pill p-1 pe-3 border shadow-sm cursor-pointer" data-bs-toggle="dropdown" style="cursor: pointer;">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="35" class="me-2 rounded-circle">
                    <div class="d-none d-md-block">
                        <h6 class="mb-0 fw-bold" style="font-size: 0.8rem;"><?php echo $name; ?></h6>
                        <small class="text-muted" style="font-size: 0.65rem;">Account Settings <i class="bi bi-chevron-down ms-1"></i></small>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px;">
                    <li><a class="dropdown-item py-2" href="edit_admin_details.php"><i class="bi bi-person-gear me-2"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Secure Logout</a></li>
                </ul>
            </div>
        </nav>

        <div class="hero-banner">
            <h2 class="fw-bold mb-2">Welcome Back, <?php echo explode(' ', $name)[0]; ?>! 👋</h2>
            <p class="opacity-75 mb-0">Here's what's happening in your library today. You have <?php echo $count_requests; ?> requests waiting for approval.</p>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-primary"></i> Library Overview</h5>
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon-circle" style="background: rgba(93, 99, 212, 0.1); color: var(--primary);"><i class="bi bi-people-fill"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Students</small><h4 class="mb-0 fw-bold"><?php echo $count_students; ?></h4></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon-circle" style="background: rgba(5, 205, 153, 0.1); color: #05CD99;"><i class="bi bi-book-half"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total Books</small><h4 class="mb-0 fw-bold"><?php echo $count_books; ?></h4></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon-circle" style="background: rgba(255, 171, 0, 0.1); color: #FFAB00;"><i class="bi bi-clock-history"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Pending</small><h4 class="mb-0 fw-bold"><?php echo $count_requests; ?></h4></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon-circle" style="background: rgba(238, 93, 80, 0.1); color: #EE5D50;"><i class="bi bi-arrow-up-right-circle"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">On Loan</small><h4 class="mb-0 fw-bold"><?php echo $total_issued; ?></h4></div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-2 text-success"></i> Sales & Financials</h5>
        <div class="row g-4 mb-5">
            <div class="col-xl-4 col-md-6">
                <div class="stat-card" style="border-left: 4px solid #05CD99;">
                    <div class="stat-icon-circle" style="background: rgba(5, 205, 153, 0.1); color: #05CD99;"><i class="bi bi-currency-dollar"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Total Revenue</small><h3 class="mb-0 fw-bold text-dark">$<?php echo number_format($total_revenue, 2); ?></h3></div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="stat-card" style="border-left: 4px solid var(--primary);">
                    <div class="stat-icon-circle" style="background: rgba(93, 99, 212, 0.1); color: var(--primary);"><i class="bi bi-cloud-arrow-down-fill"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Digital Books Sold</small><h3 class="mb-0 fw-bold text-dark"><?php echo $total_sold; ?></h3></div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12">
                <div class="stat-card" style="border-left: 4px solid #FFAB00;">
                    <div class="stat-icon-circle" style="background: rgba(255, 171, 0, 0.1); color: #FFAB00;"><i class="bi bi-person-check-fill"></i></div>
                    <div><small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Active Buyers</small><h3 class="mb-0 fw-bold text-dark"><?php echo $total_buyers; ?></h3></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">Library Asset Overview</h5>
                    <div id="librarianChart" style="min-height: 300px;"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">Sales Performance</h5>
                    <div id="salesChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-4"><i class="bi bi-search me-2 text-primary"></i> Fast Search Tools</h5>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <a href="findbook.php" class="big-box">
                    <div class="box-icon" style="background: rgba(93, 99, 212, 0.1); color: var(--primary);"><i class="bi bi-journal-text"></i></div>
                    <h5>Find Book</h5>
                    <p class="text-muted small mb-0">Search library catalog by Title or ID</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="findbookissue.php" class="big-box">
                    <div class="box-icon" style="background: rgba(5, 205, 153, 0.1); color: #05CD99;"><i class="bi bi-bookmark-star"></i></div>
                    <h5>Find Book Issue</h5>
                    <p class="text-muted small mb-0">Track which student has which book</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="finduser.php" class="big-box">
                    <div class="box-icon" style="background: rgba(255, 184, 0, 0.1); color: #FFB800;"><i class="bi bi-person-bounding-box"></i></div>
                    <h5>Find User</h5>
                    <p class="text-muted small mb-0">Quick lookup for student records</p>
                </a>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function(){
        const sidebarCollapseBtn = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        sidebarCollapseBtn.addEventListener('click', function () { sidebar.classList.toggle('active'); });

        var optionsAssets = {
            series: [<?php echo max(0, $count_books - $total_issued); ?>, <?php echo $total_issued; ?>, <?php echo $count_requests; ?>],
            chart: { type: 'donut', height: 320, fontFamily: 'Poppins, sans-serif' },
            labels: ['Available', 'Issued', 'Pending Requests'],
            colors: ['#5D63D4', '#05CD99', '#EE5D50'],
            legend: { position: 'bottom' },
            plotOptions: { pie: { donut: { size: '75%', labels: { show: true, name: {show: true}, value: {show: true}, total: { show: true, label: 'Total Books', formatter: () => '<?php echo $count_books; ?>' } } } } }
        };
        new ApexCharts(document.querySelector("#librarianChart"), optionsAssets).render();

        var optionsSales = {
            series: [{
                name: 'Metrics',
                data: [<?php echo $total_revenue; ?>, <?php echo $total_sold; ?>, <?php echo $total_buyers; ?>]
            }],
            chart: { type: 'bar', height: 320, fontFamily: 'Poppins, sans-serif', toolbar: {show: false} },
            plotOptions: { bar: { borderRadius: 8, columnWidth: '45%', distributed: true } },
            colors: ['#05CD99', '#5D63D4', '#FFAB00'],
            dataLabels: { enabled: true, formatter: function (val, opt) { if(opt.dataPointIndex === 0) return "$" + val; return val; }, style: { fontSize: '14px', colors: ["#fff"] } },
            xaxis: { categories: ['Revenue ($)', 'Books Sold', 'Unique Buyers'], labels: { style: { fontSize: '12px', fontWeight: 600 } } },
            yaxis: { title: { text: 'Count / Amount' } },
            legend: { show: false }
        };
        new ApexCharts(document.querySelector("#salesChart"), optionsSales).render();
    });
</script>
</body>
</html>