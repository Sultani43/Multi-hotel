<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    if (isSuperAdmin()) header('Location: super_admin/dashboard.php');
    elseif (isHotelAdmin()) header('Location: hotel_admin/dashboard.php');
    else header('Location: user/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND is_active=1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['hotel_id'] = $user['hotel_id'];
        $_SESSION['profile_pic'] = $user['profile_pic'];

        mysqli_query($conn, "UPDATE users SET last_login=NOW() WHERE user_id={$user['user_id']}");

        if ($user['role'] == 'user') {
            $guest = mysqli_query($conn, "SELECT guest_id FROM guests WHERE user_id={$user['user_id']}");
            if (mysqli_num_rows($guest) > 0) {
                $g = mysqli_fetch_assoc($guest);
                $_SESSION['guest_id'] = $g['guest_id'];
            } else {
                mysqli_query($conn, "INSERT INTO guests (hotel_id, user_id, first_name, email) 
                                      VALUES ('{$user['hotel_id']}', '{$user['user_id']}', '{$user['full_name']}', '{$user['email']}')");
                $_SESSION['guest_id'] = mysqli_insert_id($conn);
            }
        }

        if ($user['role'] == 'super_admin') {
            header('Location: super_admin/dashboard.php');
        } elseif ($user['role'] == 'hotel_admin') {
            header('Location: hotel_admin/dashboard.php');
        } else {
            header('Location: user/dashboard.php');
        }
        exit;
    } else {
        $error = "د استعمال نوم یا پاسورډ غلط دي!";
    }
}

$page_title = "داخلیدل";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">
    <div class="row" style="justify-content: center;">
        <div class="col-6">
            <div class="card">
                <h1 style="text-align: center;"  class="card-title">سیسټم ته د داخلیدو پاڼه</h1>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" data-validate="true">
                    <div class="form-group">
                        <label> استعمال نوم</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>پاسورډ</label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="password" required style="padding-right: 40px;">
                            <i  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">داخلیدل</button>
                </form>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="register.php">که نوي استعمالوونکي یاست؟ ځان ثبت کړي</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>