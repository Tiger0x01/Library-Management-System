<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الخروج - جامعة المنصورة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Cairo', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .logout-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border-top: 5px solid #002147; 
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #5D63D4;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 { color: #002147; margin-bottom: 10px; }
        p { color: #666; font-size: 14px; }
    </style>
    <meta http-equiv="refresh" content="2;url=../index.php">
</head>
<body>

    <div class="logout-card">
        <i class="bi bi-shield-check" style="font-size: 3rem; color: #28c76f;"></i>
        <h2>تم تسجيل الخروج</h2>
        <p>جارٍ تأمين جلستك والعودة للرئيسية...</p>
        <div class="spinner"></div>
        <small style="color: #999;">نظام إدارة المكتبة - كلية الحاسبات والمعلومات</small>
    </div>

</body>
</html>