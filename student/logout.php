<?php
session_start();

$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الخروج - نظام إدارة المكتبة</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --navy: #002147; 
            --bg: #f4f7fa;
        }

        body {
            background-color: var(--bg);
            font-family: 'Cairo', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .logout-card {
            background: white;
            padding: 50px 40px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 450px;
            width: 90%;
            position: relative;
            border-top: 6px solid var(--navy);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(40, 199, 111, 0.1);
            color: #28c76f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 25px;
            animation: scaleUp 0.5s ease-out;
        }

        @keyframes scaleUp {
            0% { transform: scale(0); }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 25px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h2 { color: var(--navy); font-weight: 700; margin-bottom: 10px; }
        p { color: #6c757d; font-size: 15px; margin-bottom: 0; }
        
        .footer-text {
            margin-top: 30px;
            display: block;
            color: #adb5bd;
            font-size: 12px;
            font-weight: 600;
        }
    </style>

    <meta http-equiv="refresh" content="2;url=../index.php">
</head>
<body>

    <div class="logout-card shadow-lg">
        <div class="icon-box">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        
        <h2>تم تسجيل الخروج بنجاح</h2>
        <p>نعمل على تأمين حسابك الآن..</p>
        <p><small>سيتم تحويلك للرئيسية خلال لحظات</small></p>
        
        <div class="spinner"></div>
        
        <span class="footer-text">
            جامعة المنصورة <br>
            كلية الحاسبات والمعلومات - نظام LMS
        </span>
    </div>

</body>
</html>