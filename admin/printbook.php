<?php 
require('../includes/dbconn.php');

if (!isset($_SESSION['RollNo'])) {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Inventory Report - Mansoura University</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --mu-blue: #002147; 
            --mu-gold: #c5a059; 
            --text-dark: #333;
            --bg-light: #f8f9fa;
        }

        * { box-sizing: border-box; }

        body {
            background-color: #d1d5db;
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            position: relative;
        }

        .academic-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double var(--mu-blue);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header-left, .header-right {
            flex: 1;
        }

        .header-left { text-align: left; }
        .header-right { text-align: right; }

        .header-left h3, .header-right h3 {
            margin: 0;
            font-size: 14pt;
            color: var(--mu-blue);
            font-weight: 700;
        }

        .header-left p, .header-right p {
            margin: 2px 0;
            font-size: 10pt;
            color: #555;
        }

        .header-center {
            text-align: center;
            padding: 0 20px;
        }

        .mu-logo {
            width: 90px;
            height: auto;
        }

        .report-title-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-title-section h1 {
            font-size: 20pt;
            color: var(--mu-blue);
            margin: 10px 0;
            text-decoration: underline;
            text-underline-offset: 8px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
            color: #666;
            margin-top: 15px;
            border: 1px solid #eee;
            padding: 10px;
            background: #fafafa;
        }

        .academic-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10.5pt;
        }

        .academic-table thead th {
            background-color: var(--mu-blue);
            color: white;
            padding: 12px;
            border: 1px solid #000;
            text-align: center;
            text-transform: uppercase;
        }

        .academic-table tbody td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .academic-table tbody tr:nth-child(even) {
            background-color: #f2f4f6;
        }

        .status-text {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .text-available { color: #155724; background: #d4edda; }
        .text-issued { color: #721c24; background: #f8d7da; }

        .signatures {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .sig-block {
            text-align: center;
            width: 250px;
        }

        .sig-block p {
            margin: 5px 0;
            font-weight: 600;
        }

        .sig-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 100%;
        }

        #print-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: var(--mu-blue);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
        }

        #print-btn:hover { background: #003366; }

        @media print {
            body { background: none; padding: 0; }
            .a4-container { box-shadow: none; margin: 0; width: 100%; padding: 10mm; }
            #print-btn { display: none; }
            .academic-table thead th { background-color: var(--mu-blue) !important; color: white !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <button id="print-btn" onclick="window.print()">
        <i class="bi bi-printer"></i> Print Official Report
    </button>

    <div class="a4-container">
        <div class="academic-header">
            <div class="header-left">
                <h3>Mansoura University</h3>
                <p>Faculty of Computer & Info. Sciences</p>
                <p>Library Department</p>
            </div>
            
            <div class="header-center">
                <img src="assets/logo.png" class="mu-logo" alt="MU Logo">
            </div>

            <div class="header-right">
                <h3>جامعة المنصورة</h3>
                <p>كلية الحاسبات والمعلومات</p>
                <p>إدارة المكتبة</p>
            </div>
        </div>

        <div class="report-title-section">
            <h1>Library Inventory Report</h1>
            <div class="meta-info">
                <span><strong>Date:</strong> <?php echo date('d/m/Y'); ?></span>
                <span><strong>Report No:</strong> MU-CIS-<?php echo date('Ymd'); ?></span>
                <span><strong>System:</strong> LMS v3.0</span>
            </div>
        </div>

        <table class="academic-table">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 40%;">Book Title</th>
                    <th style="width: 25%;">Author</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Current Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM LMS.book ORDER BY BookId ASC";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                
                if(mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $is_avail = (strtolower($row['status']) == 'available');
                        $status_label = $is_avail ? 'Available' : 'Issued';
                        $status_class = $is_avail ? 'text-available' : 'text-issued';
                ?>
                    <tr>
                        <td><strong>#<?php echo $row['BookId']; ?></strong></td>
                        <td style="text-align: left;"><?php echo $row['Title']; ?></td>
                        <td><?php echo $row['Author']; ?></td>
                        <td><?php echo $row['Availability']; ?></td>
                        <td>
                            <span class="status-text <?php echo $status_class; ?>">
                                <?php echo $status_label; ?>
                            </span>
                        </td>
                    </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5'>No books found in the inventory.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-block">
                <p>Librarian Signature</p>
                <div class="sig-line"></div>
                <p>(..........................................)</p>
            </div>
            
            <div class="sig-block">
                <p>Vice Dean for Student Affairs</p>
                <div class="sig-line"></div>
                <p>(..........................................)</p>
            </div>
        </div>

        <div style="position: absolute; bottom: 15mm; width: calc(100% - 30mm); text-align: center; border-top: 1px solid #eee; padding-top: 10px; font-size: 8pt; color: #999;">
            This is a computer-generated document from the Faculty of Computer and Information Sciences - Mansoura University.
        </div>
    </div>

</body>
</html>