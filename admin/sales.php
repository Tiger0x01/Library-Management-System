<?php
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

$stats_sql = "SELECT SUM(b.price) as total_revenue, COUNT(p.id) as total_sold 
              FROM purchased_books p 
              JOIN book b ON p.BookId = b.BookId";
$stats_res = $conn->query($stats_sql);
$stats = $stats_res->fetch_assoc();
$total_revenue = $stats['total_revenue'] ?? 0.00;
$total_sold = $stats['total_sold'] ?? 0;

$top_books_sql = "SELECT b.Title, SUM(b.price) as revenue, COUNT(p.id) as sales_count 
                  FROM purchased_books p 
                  JOIN book b ON p.BookId = b.BookId 
                  GROUP BY b.BookId 
                  ORDER BY sales_count DESC LIMIT 5";
$top_books_res = $conn->query($top_books_sql);

$book_names = [];
$book_revenues = [];
while ($tb = $top_books_res->fetch_assoc()) {
    $book_names[] = $tb['Title'];
    $book_revenues[] = (float)$tb['revenue'];
}

$transactions_sql = "SELECT p.id, p.purchase_date, p.transaction_id, u.Name as student_name, u.RollNo, b.Title, b.price 
                     FROM purchased_books p 
                     JOIN user u ON p.RollNo = u.RollNo 
                     JOIN book b ON p.BookId = b.BookId 
                     ORDER BY p.purchase_date DESC";
$transactions_res = $conn->query($transactions_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Revenue Analytics - LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root { --primary: #5D63D4; --primary-light: #7b81ea; --bg-color: #f4f7fa; --card-bg: #ffffff; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: #2b3452; overflow-x: hidden; }
        
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        /* Sidebar Styling */
        #sidebar { min-width: 280px; max-width: 280px; background: var(--card-bg); transition: 0.3s; box-shadow: 4px 0 15px rgba(0,0,0,0.03); min-height: 100vh; z-index: 999; }
        #sidebar.active { margin-left: -280px; }
        .sidebar-header { padding: 30px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center; }
        #sidebar ul li a { padding: 15px 30px; display: block; color: #6c757d; text-decoration: none; font-weight: 500; transition: 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: var(--primary); background: rgba(93, 99, 212, 0.05); border-left: 5px solid var(--primary); }

        /* Main Content */
        #content { width: 100%; padding: 20px 40px; }
        .top-navbar { background: var(--card-bg); padding: 15px 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px; }
        
        /* Stats Cards */
        .revenue-card { background: linear-gradient(135deg, #05CD99 0%, #04b083 100%); color: white; border-radius: 20px; padding: 30px; box-shadow: 0 15px 30px rgba(5, 205, 153, 0.2); position: relative; overflow: hidden; }
        .revenue-card::after { content: '\F2B6'; font-family: 'bootstrap-icons'; position: absolute; right: -10px; bottom: -20px; font-size: 8rem; opacity: 0.15; transform: rotate(-15deg); }
        
        .sales-card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.02); display: flex; align-items: center; }
        .sales-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 20px; }

        /* Table Styling */
        .table-container { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .table thead th { background: transparent; color: #888; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; border-bottom: 2px solid #f4f7fa; padding-bottom: 15px; }
        .table tbody td { vertical-align: middle; padding: 15px 10px; border-bottom: 1px solid #f4f7fa; font-weight: 500; }
        .table tbody tr:hover { background-color: #f8f9fc; }
        
        .vf-badge { background: rgba(230, 0, 0, 0.1); color: #e60000; padding: 6px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(230, 0, 0, 0.2); display: inline-flex; align-items: center;}

        /* Print Specific Styles */
        @media print {
            #sidebar, .top-navbar, .btn-print { display: none !important; }
            #content { padding: 0 !important; width: 100% !important; margin: 0 !important; }
            .revenue-card, .sales-card, .table-container { box-shadow: none !important; border: 1px solid #ddd !important; }
        }

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
            <li><a href="book.php"><i class="bi bi-journal-album me-2"></i> Library Books</a></li>
            <li><a href="addbook.php"><i class="bi bi-plus-circle me-2"></i> Add New Book</a></li>
            <li><a href="requests.php"><i class="bi bi-envelope-paper me-2"></i>Requests</a></li>
            <li><a href="current.php"><i class="bi bi-journal-check me-2"></i> Issued Materials</a></li>
            
            <li class="mt-3"><small class="text-muted px-4 fw-bold text-uppercase" style="font-size: 0.7rem;">Analytics</small></li>
            <li class="active"><a href="sales.php"><i class="bi bi-cash-coin me-2"></i> Sales & Revenue</a></li>
            
            <li class="mt-5 border-top pt-3"><a href="logout.php" class="text-danger fw-bold"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle shadow-sm me-3"><i class="bi bi-list fs-5"></i></button>
                <h5 class="fw-bold mb-0">Financial Analytics</h5>
            </div>
            <div class="d-flex align-items-center">
                <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 fw-bold me-3 btn-print">
                    <i class="bi bi-printer me-2"></i> Export Report
                </button>
                <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=5D63D4&color=fff&rounded=true&bold=true" width="35" class="me-2 rounded-circle">
                    <span class="fw-bold" style="font-size: 0.85rem;"><?php echo $admin_name; ?></span>
                </div>
            </div>
        </nav>

        <div class="row g-4 mb-5">
            <div class="col-xl-4">
                <div class="revenue-card h-100">
                    <p class="text-uppercase fw-bold mb-1 opacity-75" style="letter-spacing: 1px; font-size: 0.8rem;">Total Net Revenue</p>
                    <h1 class="display-4 fw-bold mb-0">$<?php echo number_format($total_revenue, 2); ?></h1>
                    <p class="mt-3 mb-0 fs-6"><i class="bi bi-arrow-up-right-circle-fill me-1"></i> Generated from digital sales</p>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6">
                <div class="sales-card h-100">
                    <div class="sales-icon" style="background: rgba(93, 99, 212, 0.1); color: var(--primary);">
                        <i class="bi bi-cloud-download-fill"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Books Sold</p>
                        <h2 class="fw-bold mb-0 text-dark"><?php echo $total_sold; ?> <span class="fs-6 text-muted fw-normal">copies</span></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="sales-card h-100">
                    <div class="sales-icon" style="background: rgba(255, 171, 0, 0.1); color: #FFAB00;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Payment Method</p>
                        <h4 class="fw-bold mb-0 text-dark">Vodafone Cash</h4>
                        <small class="text-muted fw-semibold">100% of transactions</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">Top Grossing Books</h5>
                    <div id="revenueChart" style="min-height: 350px;"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-center text-center bg-light">
                    <i class="bi bi-shield-check text-success" style="font-size: 4rem;"></i>
                    <h4 class="fw-bold mt-3">Secure Payments</h4>
                    <p class="text-muted">All transactions are securely logged and tied to student ID numbers for complete transparency.</p>
                </div>
            </div>
        </div>

        <div class="table-container mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff text-primary me-2"></i> Transaction Ledger</h5>
                <span class="badge bg-primary rounded-pill"><?php echo $transactions_res->num_rows; ?> Records</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student Details</th>
                            <th>Book Purchased</th>
                            <th>Date & Time</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($transactions_res && $transactions_res->num_rows > 0): ?>
                            <?php while($row = $transactions_res->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <span class="vf-badge">
                                            <i class="bi bi-phone-vibrate me-1"></i> <?php echo htmlspecialchars($row['transaction_id'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($row['student_name']); ?></h6>
                                        <small class="text-muted">ID: <?php echo htmlspecialchars($row['RollNo']); ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2 text-primary"><i class="bi bi-book"></i></div>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($row['Title']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted"><i class="bi bi-calendar2-week me-1"></i> <?php echo date('M d, Y', strtotime($row['purchase_date'])); ?></span><br>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i> <?php echo date('h:i A', strtotime($row['purchase_date'])); ?></small>
                                    </td>
                                    <td class="text-end fw-bold text-success fs-5">
                                        $<?php echo number_format($row['price'], 2); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                    <h6 class="text-muted">No transactions recorded yet.</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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

    
        var options = {
            series: [{
                name: 'Revenue ($)',
                data: <?php echo json_encode($book_revenues); ?>
            }],
            chart: { type: 'bar', height: 350, fontFamily: 'Poppins, sans-serif', toolbar: {show: false} },
            plotOptions: { bar: { borderRadius: 8, horizontal: true, distributed: true, dataLabels: { position: 'bottom' } } },
            colors: ['#5D63D4', '#05CD99', '#FFAB00', '#EE5D50', '#00b0ff'],
            dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, formatter: function (val, opt) { return "$" + val }, offsetX: 0 },
            xaxis: { categories: <?php echo json_encode($book_names); ?> },
            yaxis: { labels: { show: true, style: { fontWeight: 600 } } },
            tooltip: { theme: 'light', y: { formatter: function (val) { return "$" + val } } },
            legend: { show: false }
        };

        var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
        chart.render();
    });
</script>
</body>
</html>