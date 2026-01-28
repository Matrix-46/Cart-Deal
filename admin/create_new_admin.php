<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// Check if user is logged in and is superadmin
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (!isset($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
    header('location:dashboard.php');
    exit();
}

// Password validation function
function validatePassword($password)
{
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number";
    }
    return true;
}

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $is_super = isset($_POST['is_superadmin']) ? 1 : 0;

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    } else {
        // Validate password
        $pass_validation = validatePassword($pass);
        if ($pass_validation !== true) {
            $message[] = $pass_validation;
        } elseif ($pass != $cpass) {
            $message[] = 'Passwords do not match!';
        } else {
            // Check if email already exists
            $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE email = ?");
            $select_admin->execute([$email]);

            if ($select_admin->rowCount() > 0) {
                $message[] = 'Email already exists!';
            } else {
                // Hash password with bcrypt
                $hashed_pass = password_hash($cpass, PASSWORD_DEFAULT);

                $insert_admin = $conn->prepare("INSERT INTO `admins`(name, email, password, is_superadmin) VALUES(?,?,?,?)");
                $insert_admin->execute([$name, $email, $hashed_pass, $is_super]);
                $message[] = 'New admin created successfully!';
            }
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Admin</title>

    <link rel="stylesheet" href="../css/admin_style.css?v=1.1">
    <link rel="stylesheet" href="../css/admin_enhancement.css?v=1.1">

    <style>
        .password-strength {
            margin-top: 0.5rem;
            font-size: 1.4rem;
        }

        .strength-weak {
            color: #e74c3c;
        }

        .strength-medium {
            color: #f39c12;
        }

        .strength-strong {
            color: #27ae60;
        }

        .requirement {
            font-size: 1.4rem;
            margin: 0.3rem 0;
            color: #666;
        }

        .requirement.met {
            color: #27ae60;
        }

        .requirement i {
            margin-right: 0.5rem;
        }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="form-container">

        <form action="" method="post" id="createAdminForm">
            <h3>Create New Admin</h3>
            <p>Add a new administrator account</p>

            <input type="text" name="name" required placeholder="Enter full name" maxlength="50" class="box">

            <input type="email" name="email" required placeholder="Enter email address" class="box" id="email">

            <input type="password" name="pass" required placeholder="Enter password" maxlength="50" class="box"
                id="password">

            <div class="password-strength" id="strengthIndicator"></div>

            <div style="margin: 1rem 0; text-align: left; padding: 0 1rem;">
                <p style="font-size: 1.6rem; margin-bottom: 0.5rem; font-weight: bold;">Password Requirements:</p>
                <div class="requirement" id="req-length"><i class="fas fa-times-circle"></i> At least 8 characters</div>
                <div class="requirement" id="req-upper"><i class="fas fa-times-circle"></i> One uppercase letter</div>
                <div class="requirement" id="req-lower"><i class="fas fa-times-circle"></i> One lowercase letter</div>
                <div class="requirement" id="req-number"><i class="fas fa-times-circle"></i> One number</div>
            </div>

            <input type="password" name="cpass" required placeholder="Confirm password" maxlength="50" class="box"
                id="cpassword">

            <div style="margin: 1rem 0; text-align: left; padding: 0 1rem;">
                <label style="font-size: 1.8rem; cursor: pointer;">
                    <input type="checkbox" name="is_superadmin" value="1" style="width: auto; margin-right: 0.5rem;">
                    <span>Make Superadmin (can manage other admins)</span>
                </label>
            </div>

            <input type="submit" value="Create Admin" class="btn" name="submit" id="submitBtn">

            <p style="margin-top: 1.5rem;">
                <a href="manage_admins.php">← Back to Admin Management</a>
            </p>
        </form>

    </section>

    <script src="../js/admin_script.js"></script>

    <script>
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('strengthIndicator');
        const submitBtn = document.getElementById('submitBtn');

        // Password strength checker
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            let strength = 0;
            let strengthText = '';
            let strengthClass = '';

            // Check requirements
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            // Update requirement indicators
            document.getElementById('req-length').className = hasLength ? 'requirement met' : 'requirement';
            document.getElementById('req-length').innerHTML = hasLength ? '<i class="fas fa-check-circle"></i> At least 8 characters' : '<i class="fas fa-times-circle"></i> At least 8 characters';

            document.getElementById('req-upper').className = hasUpper ? 'requirement met' : 'requirement';
            document.getElementById('req-upper').innerHTML = hasUpper ? '<i class="fas fa-check-circle"></i> One uppercase letter' : '<i class="fas fa-times-circle"></i> One uppercase letter';

            document.getElementById('req-lower').className = hasLower ? 'requirement met' : 'requirement';
            document.getElementById('req-lower').innerHTML = hasLower ? '<i class="fas fa-check-circle"></i> One lowercase letter' : '<i class="fas fa-times-circle"></i> One lowercase letter';

            document.getElementById('req-number').className = hasNumber ? 'requirement met' : 'requirement';
            document.getElementById('req-number').innerHTML = hasNumber ? '<i class="fas fa-check-circle"></i> One number' : '<i class="fas fa-times-circle"></i> One number';

            // Calculate strength
            if (hasLength) strength++;
            if (hasUpper) strength++;
            if (hasLower) strength++;
            if (hasNumber) strength++;

            // Display strength
            if (strength === 0) {
                strengthText = '';
            } else if (strength <= 2) {
                strengthText = '⚠️ Weak Password';
                strengthClass = 'strength-weak';
            } else if (strength === 3) {
                strengthText = '🔶 Medium Password';
                strengthClass = 'strength-medium';
            } else {
                strengthText = '✅ Strong Password';
                strengthClass = 'strength-strong';
            }

            strengthIndicator.textContent = strengthText;
            strengthIndicator.className = 'password-strength ' + strengthClass;
        });

        // Form validation
        document.getElementById('createAdminForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const cpassword = document.getElementById('cpassword').value;
            const email = document.getElementById('email').value;

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }

            // Password validation
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
            if (!/[A-Z]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one uppercase letter');
                return false;
            }
            if (!/[a-z]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one lowercase letter');
                return false;
            }
            if (!/[0-9]/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one number');
                return false;
            }

            // Match validation
            if (password !== cpassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>

</body>

</html>