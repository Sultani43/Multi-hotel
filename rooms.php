<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$hotel_id = getUserHotelId();

// د لټون عبارت ترلاسه کول
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    // د خونې نمبر، ډول، یا بیه (د شمېرې په توګه) له مخې لټون
    $search_condition = " AND (room_number LIKE '%$search%' OR room_type LIKE '%$search%' OR price_per_night LIKE '%$search%')";
}

$rooms = mysqli_query($conn, "SELECT * FROM rooms WHERE hotel_id=$hotel_id AND status='available' $search_condition ORDER BY room_number");

$page_title = "زموږ خونې";
include '../includes/header.php';
include '../includes/navbar.php';

if (!function_exists('format_af')) {
    function format_af($amount) { return 'Af ' . number_format($amount, 2); }
}
?>

<div class="container">
    <h1>زموږ اطاقونه</h1>
    
    <!-- د لټون فورمه -->
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: center;">
        <form method="GET" action="" style="display: flex; gap: 10px; max-width: 500px; width: 100%;">
            <input type="text" name="search" placeholder="د خونه نمبر، ډول، یا بیه له مخې لټون..." 
                   value="<?php echo htmlspecialchars($search); ?>" 
                   style="flex: 1; padding: 10px 15px; border-radius: 30px; border: 1px solid #ddd;">
            <button type="submit" class="btn btn-primary" style="border-radius: 30px; padding: 0 20px;">
                <i class="fas fa-search"></i> لټون
            </button>
            <?php if (!empty($search)): ?>
                <a href="rooms.php" class="btn btn-secondary" style="border-radius: 30px;">ټول ښکاره که</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($rooms) > 0): ?>
            <?php while ($room = mysqli_fetch_assoc($rooms)): ?>
            <div class="col-4">
                <div class="room-card">
                    <div class="room-image">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div class="room-details">
                        <h3 class="room-title">اطاق نمبر <?php echo $room['room_number']; ?></h3>
                        <p class="room-price"><?php echo format_af($room['price_per_night']); ?> / شپه</p>
                        <p><strong>ډول:</strong> <?php echo $room['room_type']; ?></p>
                        <p><strong>ظرفیت:</strong> <?php echo $room['capacity']; ?> کسان</p>
                        <p><?php echo $room['description']; ?></p>
                        <a href="book_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-primary">بکنګ</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <?php if (!empty($search)): ?>
                    د "<?php echo htmlspecialchars($search); ?>" په  نوم کوم اطاق نسته!
                <?php else: ?>
                    اوس مهال کوم اطاق نسته.
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>