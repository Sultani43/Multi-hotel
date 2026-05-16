<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$hotel_id = getUserHotelId();
$guest_id = $_SESSION['guest_id'];

// د لټون عبارت ترلاسه کول
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    // د توکي نوم، کټګورۍ، یا تفصیل له مخې لټون
    $search_condition = " AND (item_name LIKE '%$search%' OR category LIKE '%$search%' OR description LIKE '%$search%')";
}

// د امر ثبتول (که د POST له لارې راشي)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    $instructions = mysqli_real_escape_string($conn, $_POST['instructions']);
    
    $item_check = mysqli_query($conn, "SELECT * FROM food_items WHERE item_id = $item_id AND hotel_id = $hotel_id AND is_available = 1");
    if (mysqli_num_rows($item_check) > 0) {
        $query = "INSERT INTO food_orders (hotel_id, guest_id, item_id, quantity, special_instructions, status, payment_status) 
                  VALUES ($hotel_id, $guest_id, $item_id, $quantity, '$instructions', 'pending', 'unpaid')";
        if (mysqli_query($conn, $query)) {
            $success = "ستاسو امر ثبت سو!";
        } else {
            $error = "تېروتنه: " . mysqli_error($conn);
        }
    } else {
        $error = "غوښتل شوی خواړه شتون نلري.";
    }
}

// د خواړو توکو لیست (د لټون شرط سره)
$food_items = mysqli_query($conn, "SELECT * FROM food_items WHERE hotel_id=$hotel_id AND is_available = 1 $search_condition ORDER BY category, item_name");

$page_title = "د خواړو مینو";
include '../includes/header.php';
include '../includes/navbar.php';

if (!function_exists('format_af')) {
    function format_af($amount) { return 'Af ' . number_format($amount, 2); }
}
?>

<div class="container">
    <h1>د خواړو مینو</h1>
    
    <!-- د لټون فورمه -->
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: center;">
        <form method="GET" action="" style="display: flex; gap: 10px; max-width: 500px; width: 100%;">
            <input type="text" name="search" placeholder="د خواړو نوم، ډول، یا تفصیل له مخې لټون..." 
                   value="<?php echo htmlspecialchars($search); ?>" 
                   style="flex: 1; padding: 10px 15px; border-radius: 30px; border: 1px solid #ddd;">
            <button type="submit" class="btn btn-primary" style="border-radius: 30px; padding: 0 20px;">
                <i class="fas fa-search"></i> لټون
            </button>
            <?php if (!empty($search)): ?>
                <a href="food_menu.php" class="btn btn-secondary" style="border-radius: 30px;">ټول</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($food_items) > 0): ?>
        <div class="row">
            <?php while ($item = mysqli_fetch_assoc($food_items)): ?>
            <div class="col-4">
                <div class="card">
                    <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <p><strong><?php echo format_af($item['price']); ?></strong></p>
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                        <div class="form-group">
                            <label>تعداد:</label>
                            <input type="number" name="quantity" value="1" min="1" required style="width: 80px;">
                        </div>
                        <div class="form-group">
                            <label>اضافي توضیحات:</label>
                            <input type="text" name="instructions" placeholder="لکه: پرته له پیاز" style="width: 100%;">
                        </div>
                        <button type="submit" name="order" class="btn btn-primary">امر</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <?php if (!empty($search)): ?>
                د "<?php echo htmlspecialchars($search); ?>" لپاره کوم خواړه ونه موندل شول.
            <?php else: ?>
                اوس مهال کوم خواړه شتون نلري.
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>