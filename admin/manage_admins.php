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

// Delete admin
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    // Check if trying to delete superadmin
    $check_super = $conn->prepare("SELECT is_superadmin FROM `admins` WHERE id = ?");
    $check_super->execute([$delete_id]);
    $admin_to_delete = $check_super->fetch(PDO::FETCH_ASSOC);

    if ($admin_to_delete['is_superadmin'] == 1) {
        $message[] = 'Cannot delete superadmin account!';
    } elseif ($delete_id == $admin_id) {
        $message[] = 'Cannot delete your own account!';
    } else {
        $delete_admin = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
        $delete_admin->execute([$delete_id]);
        $message[] = 'Admin deleted successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css?v=1.1">
    <link rel="stylesheet" href="../css/admin_enhancement.css?v=1.1">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="accounts">

        <h1 class="heading">Admin Accounts Management</h1>

        <div class="box-container">

            <?php
            $select_admins = $conn->prepare("SELECT * FROM `admins` ORDER BY id ASC");
            $select_admins->execute();
            if ($select_admins->rowCount() > 0) {
                while ($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)) {
                    $role_badge = $fetch_admins['is_superadmin'] == 1 ? '<span style="background: #d90429; color: #fff; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 1.4rem;"><i class="fas fa-crown"></i> Superadmin</span>' : '<span style="background: #2b2d42; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 1.4rem;">Admin</span>';

                    $is_current = ($fetch_admins['id'] == $admin_id) ? '<span style="color: #d90429; font-size: 1.4rem;">(You)</span>' : '';
                    ?>
                    <div class="box">
                        <p>Admin ID : <span>
                                <?= htmlspecialchars($fetch_admins['id']); ?>
                            </span></p>
                        <p>Name : <span>
                                <?= htmlspecialchars($fetch_admins['name']); ?>
                                <?= $is_current; ?>
                            </span></p>
                        <p>Email : <span>
                                <?= htmlspecialchars($fetch_admins['email']); ?>
                            </span></p>
                        <p>Role :
                            <?= $role_badge; ?>
                        </p>
                        <p>Created : <span>
                                <?= date('M d, Y', strtotime($fetch_admins['created_at'])); ?>
                            </span></p>
                        <div class="flex-btn">
                            <?php if ($fetch_admins['is_superadmin'] != 1 && $fetch_admins['id'] != $admin_id): ?>
                                <a href="manage_admins.php?delete=<?= htmlspecialchars($fetch_admins['id']); ?>" class="delete-btn"
                                    onclick="return confirm('Delete this admin account?');">Delete</a>
                            <?php else: ?>
                                <span class="option-btn" style="opacity: 0.5; cursor: not-allowed;">Protected</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">No admin accounts found!</p>';
            }
            ?>

        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="create_new_admin.php" class="btn" style="display: inline-block; width: auto;">
                <i class="fas fa-user-plus"></i> Create New Admin
            </a>
        </div>

    </section>

    <script src="../js/admin_script.js"></script>

</body>

</html>