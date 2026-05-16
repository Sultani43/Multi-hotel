<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

$hotels = mysqli_query($conn, "SELECT hotel_id, hotel_name FROM hotels WHERE is_active=1");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $hotel_id = (int)$_POST['hotel_id'];
    $password = md5($_POST['password']);
    $confirm = md5($_POST['confirm_password']);

    if ($_POST['password'] != $_POST['confirm_password']) {
        $error = "پاسورډونه سره برابر نه دي!";
    } else {
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE username='$username' OR email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "کارن نوم یا برېښنالیک دمخه شتون لري!";
        } else {
            $insert = "INSERT INTO users (username, email, password, full_name, role, hotel_id) 
                       VALUES ('$username', '$email', '$password', '$full_name', 'user', '$hotel_id')";
            if (mysqli_query($conn, $insert)) {
                $user_id = mysqli_insert_id($conn);
                $guest_insert = "INSERT INTO guests (hotel_id, user_id, first_name, email) 
                                 VALUES ('$hotel_id', '$user_id', '$full_name', '$email')";
                mysqli_query($conn, $guest_insert);
                $success = "راجستر بریالی شو! اوس ننوځئ.";
            } else {
                $error = "تېروتنه: " . mysqli_error($conn);
            }
        }
    }
}

$page_title = "راجستر";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">
    <div class="row" style="justify-content: center;">
        <div class="col-6">
            <div class="card">
                <h1 style="text-align: center;" class="card-title">په سیسټم کي نوي اکاونټ جوړ کړي</h1>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <form method="POST" data-validate="true">
                    <div class="form-group">
                        <label>بشپړ نوم <span style="color: #e74c3c;">*</span></label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label> استعمال نوم <span style="color: #e74c3c;">*</span></label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>ایمیل ادرس <span style="color: #e74c3c;">*</span></label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>هوټل <span style="color: #e74c3c;">*</span></label>
                        <select name="hotel_id" required>
                            <option value="">یو هوټل انتخاب کړي</option>
                            <?php while ($hotel = mysqli_fetch_assoc($hotels)): ?>
                                <option value="<?php echo $hotel['hotel_id']; ?>"><?php echo $hotel['hotel_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>پاسورډ <span style="color: #e74c3c;">*</span></label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="password" required style="padding-right: 40px;">
                            <i  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>پاسورډ تکرار<span style="color: #e74c3c;">*</span></label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">ثبت</button>
                </form>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="login.php">که مخکي اکاونټ لري. نو داخل سي</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>