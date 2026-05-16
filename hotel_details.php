<?php
// hotel_details.php
require_once 'includes/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$hotel_id = (int)$_GET['id'];
$query = "SELECT * FROM hotels WHERE hotel_id = $hotel_id AND is_active = 1";
$result = mysqli_query($conn, $query);
$hotel = mysqli_fetch_assoc($result);
if (!$hotel) {
    header('Location: index.php');
    exit;
}

$gallery = [];
if (!empty($hotel['gallery_images'])) {
    $gallery = json_decode($hotel['gallery_images'], true);
}

$facilities = explode("\n", $hotel['facilities']);

$page_title = $hotel['hotel_name'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <h1 class="card-title"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h1>
        
        <!-- د عکسونو ګالري (Swiper یا ساده Grid) -->
        <div style="margin-bottom: 2rem;">
            <?php if (!empty($gallery)): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                    <?php foreach ($gallery as $img): ?>
                        <img src="/multi-hotel/uploads/hotels/<?php echo $img; ?>" style="width:100%; height:150px; object-fit: cover; border-radius: 8px;">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="height: 200px; background: #ddd; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <i class="fas fa-hotel" style="font-size: 4rem; color: #aaa;"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- د هوټل معلومات -->
        <p><strong><i class="fas fa-location-dot"></i> ادرس:</strong> <?php echo htmlspecialchars($hotel['hotel_address']); ?></p>
        <p><strong><i class="fas fa-phone"></i> تلیفون:</strong> <?php echo htmlspecialchars($hotel['hotel_phone']); ?></p>
        <p><strong><i class="fas fa-envelope"></i> ایمیل ادرس:</strong> <?php echo htmlspecialchars($hotel['hotel_email']); ?></p>

        <!-- زموږ په اړه -->
        <div style="margin-top: 2rem;">
            <h2>زموږ په اړه</h2>
            <p><?php echo nl2br(htmlspecialchars($hotel['about_us'])); ?></p>
        </div>

        <!-- اسانتیاوې -->
        <div style="margin-top: 2rem;">
            <h2>اسانتیاوې</h2>
            <div class="row">
                <?php foreach ($facilities as $facility): ?>
                    <?php $facility = trim($facility); if (!empty($facility)): ?>
                        <div class="col-4">
                            <div class="card" style="text-align: center;">
                                <i class="fas fa-check-circle" style="color: var(--success); font-size: 2rem;"></i>
                                <p><?php echo htmlspecialchars($facility); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- د بکنګ تڼۍ -->
        <div style="text-align: center; margin-top: 2rem;">
            <?php if (!isLoggedIn()): ?>
                <a href="login.php" class="btn btn-primary">د اطاق نیولو لپاره داخل سي</a>
            <?php else: ?>
                <?php if (isUser()): ?>
                    <a href="user/rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="btn btn-primary">خونې وګورئ</a>
                <?php else: ?>
                    <a href="index.php" class="btn btn-primary">عمومي پاڼې ته لاړ شئ</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>