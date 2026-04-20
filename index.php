<?php
require('includes/dbconn.php');

if(isset($_POST['signin'])) {
    $u = $_POST['RollNo'];
    $p = $_POST['Password'];
    
    $sql = "SELECT * FROM LMS.user WHERE RollNo='$u'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $x = $row['Password'];
        $y = $row['Type'];
        
        if(strcasecmp($x, $p) == 0 && !empty($u) && !empty($p)) {
            $_SESSION['RollNo'] = $u;
            if($y == 'Admin') {
                header('location:admin/index.php');
                exit();
            } else {
                header('location:student/index.php');
                exit();
            }
        } else { 
            echo "<script>alert('Failed to Login! Incorrect Username or Password');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}

if(isset($_POST['signup'])) {
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $mobno = $_POST['PhoneNumber'];
    $rollno = $_POST['RollNo'];
    $type = 'Student';

    $sql = "INSERT INTO LMS.user (Name, Type, RollNo, EmailId, MobNo, Password) VALUES ('$name', '$type', '$rollno', '$email', '$mobno', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration Successful! 🎉 Please log in.');</script>";
    } else {
        echo "<script>alert('Error: Username or Email already exists! ⚠️');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Modern LMS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5D63D4;
            --primary-light: #7b81ea;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 1000px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-wrap: wrap;
        }

        .login-side {
            padding: 60px 50px;
            background-color: #ffffff;
            width: 50%;
        }

        .register-side {
            padding: 60px 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #ffffff;
            width: 50%;
        }

        .section-title {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .login-side .section-title { color: #1e293b; }
        .login-side .text-muted { color: #64748b !important; }
        
        .register-side .section-title { color: #ffffff; }
        .register-side .text-light { color: rgba(255, 255, 255, 0.8) !important; }

        .form-floating > .form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 1rem 1rem;
            height: calc(3.5rem + 10px);
            font-weight: 500;
            background-color: #f8fafc;
            box-shadow: none !important;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary);
            background-color: #ffffff;
        }

        .form-floating > label {
            color: #94a3b8;
            font-weight: 500;
        }

        .register-side .form-floating > .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }
        .register-side .form-floating > .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #ffffff;
        }
        .register-side .form-floating > label { color: rgba(255, 255, 255, 0.7); }
        .register-side .form-control::placeholder { color: transparent; }

        .btn-custom {
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            margin-top: 10px;
        }

        .btn-login {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 10px 20px rgba(93, 99, 212, 0.2);
        }
        .btn-login:hover {
            background-color: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(93, 99, 212, 0.3);
            color: white;
        }

        .btn-register {
            background-color: #ffffff;
            color: var(--primary);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-register:hover {
            background-color: #f8fafc;
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
            color: var(--primary);
        }

        @media (max-width: 991px) {
            .auth-container { flex-direction: column; }
            .login-side, .register-side { width: 100%; padding: 40px 30px; }
            .register-side { border-radius: 0 0 24px 24px; }
        }
    </style>
</head>
<body>

<div class="auth-container">
    
    <div class="login-side d-flex flex-column justify-content-center">
        <div class="mb-5 text-center text-md-start">
            <h2 class="section-title">Welcome Back! 👋</h2>
            <p class="text-muted">Enter your credentials to access the library dashboard.</p>
        </div>

        <form method="post">
            <div class="form-floating mb-4">
                <input type="text" name="RollNo" class="form-control" id="loginUsername" placeholder="ID" required>
                <label for="loginUsername"><i class="bi bi-person me-2"></i>University ID</label>
            </div>
            
            <div class="form-floating mb-4">
                <input type="password" name="Password" class="form-control" id="loginPassword" placeholder="Password" required>
                <label for="loginPassword"><i class="bi bi-lock me-2"></i>Password</label>
            </div>

            <button type="submit" class="btn btn-custom btn-login w-100" name="signin">Sign In to Dashboard</button>
        </form>
    </div>

    <div class="register-side d-flex flex-column justify-content-center">
        <div class="mb-5 text-center text-md-start">
            <h2 class="section-title">New Member? 🚀</h2>
            <p class="text-light">Create an account to start borrowing books today.</p>
        </div>

        <form method="post">
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" name="Name" class="form-control" id="regName" placeholder="Name" required>
                        <label for="regName">Full Name</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" name="RollNo" class="form-control" id="regRollNo" placeholder="ID" required>
                        <label for="regRollNo">University ID</label>
                    </div>
                </div>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="Email" class="form-control" id="regEmail" placeholder="Email" required>
                <label for="regEmail">Email Address</label>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="password" name="Password" class="form-control" id="regPassword" placeholder="Pass" required>
                        <label for="regPassword">Password</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" name="PhoneNumber" class="form-control" id="regPhone" placeholder="Phone" required>
                        <label for="regPhone">Phone Number</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-custom btn-register w-100" name="signup">Create My Account</button>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>