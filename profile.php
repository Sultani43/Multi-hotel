<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// د کارن اوسني معلومات ترلاسه کول
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($user_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    
    // د نوم تازه کول
    $update_query = "UPDATE users SET full_name = '$full_name' WHERE user_id = $user_id";
    mysqli_query($conn, $update_query);
    
    // د پاسورډ تازه کول (که داخل شوی وي)
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['new_password'] == $_POST['confirm_password']) {
            $new_password = md5($_POST['new_password']);
            mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE user_id = $user_id");
            $message = "پاسورډ په بریالیتوب سره تازه شو!";
        } else {
            $error = "نوی پاسورډ او د هغه تکرار سره برابر نه دي!";
        }
    }
    
    // د عکس تازه کول (که پورته شوی وي)
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $file_type = $_FILES['profile_pic']['type'];
        $file_size = $_FILES['profile_pic']['size'];
        
        if (in_array($file_type, $allowed_types)) {
            if ($file_size <= 2 * 1024 * 1024) { // 2MB حد
                $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                $new_filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
                $upload_path = '../uploads/profile_pics/' . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                    // د زوړ عکس حذف کول (که شتون ولري)
                    if (!empty($user['profile_pic']) && file_exists('../uploads/profile_pics/' . $user['profile_pic'])) {
                        unlink('../uploads/profile_pics/' . $user['profile_pic']);
                    }
                    
                    mysqli_query($conn, "UPDATE users SET profile_pic = '$new_filename' WHERE user_id = $user_id");
                    $message = "پېژندګلوي په بریالیتوب سره تازه شوه!";
                } else {
                    $error = "د عکس پورته کولو کې ستونزه رامنځته شوه.";
                }
            } else {
                $error = "د عکس اندازه باید له 2MB کمه وي.";
            }
        } else {
            $error = "یوازې JPG، JPEG، PNG، او GIF فایلونو ته اجازه ده.";
        }
    }
    
    // د کارن تازه معلومات بیا ترلاسه کول
    $user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
    $user = mysqli_fetch_assoc($user_query);
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['profile_pic'] = $user['profile_pic'];
    
    if (empty($error) && empty($message)) {
        $message = "پېژندګلوي په بریالیتوب سره تازه شوه!";
    }
}

$page_title = "زما پېژندګلوي";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h1 class="card-title">زما پېژندګلوي</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <?php if (!empty($user['profile_pic']) && file_exists('../uploads/profile_pics/' . $user['profile_pic'])): ?>
                <img src="/multi-hotel/uploads/profile_pics/<?php echo $user['profile_pic']; ?>" 
                     alt="Profile Picture" 
                     style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #4361ee;">
            <?php else: ?>
                <div style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #4361ee, #7209b7); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <span style="font-size: 3rem; color: white; font-weight: bold;"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>کارن نوم</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control" readonly disabled style="background: #f0f0f0;">
            </div>
            
            <div class="form-group">
                <label>برېښنالیک</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" readonly disabled style="background: #f0f0f0;">
            </div>
            
            <div class="form-group">
                <label>بشپړ نوم</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>د پېژندګلوي عکس</label>
                <input type="file" name="profile_pic" accept="image/jpeg,image/png,image/gif,image/jpg">
                <small style="color: #666;">JPG, PNG, GIF - تر 2MB پورې</small>
            </div>
            
            <hr style="margin: 2rem 0;">
            
            <h3>پاسورډ بدلول</h3>
            <p style="color: #666; margin-bottom: 1rem;">که پاسورډ نه بدلوئ، نو دا ساحې خالي پرېږدئ.</p>
            
            <div class="form-group">
                <label>نوی پاسورډ</label>
                <input type="password" name="new_password">
            </div>
            
            <div class="form-group">
                <label>د نوي پاسورډ تکرار</label>
                <input type="password" name="confirm_password">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">تازه کول</button>
                <a href="/multi-hotel/user/dashboard.php" class="btn">لغوه کول</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>