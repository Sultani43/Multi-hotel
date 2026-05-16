<?php
// index.php - د کور پاڼه د ښه راغلاست پیغام، لټون ساحې، او د هوټلونو لیست سره
require_once 'includes/config.php';

$page_title = "کور";
include 'includes/header.php';
include 'includes/navbar.php';

// د لټون عبارت ترلاسه کول
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_condition = " WHERE (hotel_name LIKE '%$search%' OR hotel_address LIKE '%$search%') AND is_active = 1";
} else {
    $search_condition = " WHERE is_active = 1";
}

// د فعالو هوټلونو راوړل
$hotels_query = "SELECT hotel_id, hotel_name, hotel_address, hotel_phone, about_us, gallery_images 
                 FROM hotels 
                 $search_condition 
                 ORDER BY hotel_name";
$hotels_result = mysqli_query($conn, $hotels_query);
?>

<div class="container">
    <!-- د ښه راغلاست او لټون برخه -->
    <div style="text-align: center; margin: 30px 0 40px;">
        <h1 style="color: #1e293b; font-size: 2.5rem; margin-bottom: 15px;">د کندهار ښار هوټلونه</h1>
        <p style="color: #64748b; font-size: 1.1rem; margin-bottom: 25px;">د خپلې خوښې هوټل کې انلاین اطاق ونیسي</p>
        
        <!-- د لټون فورمه (ښکلی سټایل) -->
        <form method="GET" action="" style="max-width: 600px; margin: 0 auto;">
            <div style="display: flex; gap: 10px; background: white; border-radius: 60px; padding: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <input type="text" name="search" placeholder="د هوټل په نوم یا ادرس پسې لټون وکړي" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       style="flex: 1; padding: 14px 20px; border: none; border-radius: 60px; outline: none; font-size: 1rem;">
                <button type="submit" class="btn btn-primary" style="border-radius: 60px; padding: 0 30px; background: linear-gradient(135deg, #0f27df, #0a74a2); border: none;">
                    <i class="fas fa-search">لټون</i> 
                </button>
            </div>
        </form>
    </div>

    <!-- د ننوتلو/راجستر تڼۍ (که کارونکی لاګ ان نه وي) -->
    <?php if (!isLoggedIn()): ?>
        <!-- دلته موږ تڼۍ نه ښیو، ځکه چې د هوټل کارتونو کې د بکنګ لپاره ننوتلو ته لارښوونه شوې -->
    <?php else: ?>
        <div style="text-align: center; margin-bottom: 30px;">
            <a href="<?php 
                if (isSuperAdmin()) echo 'super_admin/dashboard.php';
                elseif (isHotelAdmin()) echo 'hotel_admin/dashboard.php';
                else echo 'user/dashboard.php';
            ?>" class="btn btn-primary">ډشبورډ ته لاړ شي</a>
        </div>
    <?php endif; ?>

    <!-- د هوټلونو لیست -->
    <h2 style="margin-bottom: 1rem; color: #1e293b;">په سیسټم کې موجود هوټلونه</h2>
    
    <?php if (mysqli_num_rows($hotels_result) > 0): ?>
        <div class="row">
            <?php while ($hotel = mysqli_fetch_assoc($hotels_result)): 
                // د ګالري عکسونه ترلاسه کول
                $gallery = [];
                if (!empty($hotel['gallery_images'])) {
                    $gallery = json_decode($hotel['gallery_images'], true);
                }
                $cover_image = (!empty($gallery)) ? $gallery[0] : 'default-hotel.jpg';
            ?>
            <div class="col-4">
                <div class="card room-card" onclick="window.location.href='hotel_details.php?id=<?php echo $hotel['hotel_id']; ?>'" style="cursor: pointer;">
                    <div class="room-image">
                        <img src="/multi-hotel/uploads/hotels/<?php echo $cover_image; ?>" alt="Hotel Image" style="width:100%; height:100%; object-fit: cover;">
                    </div>
                    <div class="room-details">
                        <h3 class="room-title"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h3>
                        <p class="room-price"><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($hotel['hotel_address']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($hotel['hotel_phone']); ?></p>
                        <?php if (!empty($hotel['about_us'])): ?>
                            <p><?php echo nl2br(htmlspecialchars(substr($hotel['about_us'], 0, 100))); ?>...</p>
                        <?php endif; ?>
                        <?php if (!isLoggedIn()): ?>
                            <span class="btn btn-primary" style="margin-top: 10px;">د اطاق نیولو لپاره داخل سي</span>
                        <?php else: ?>
                            <span class="btn btn-primary" style="margin-top: 10px;">خونې وګوري</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" style="text-align: center;">
            <i class="fas fa-info-circle"></i> 
            <?php if (!empty($search)): ?>
                د "<?php echo htmlspecialchars($search); ?>" په نامه او ادرس کوم هوټل پیدا نه سو. مهرباني وکړي بله جمله کار کي.
            <?php else: ?>
            اوس مهال کوم فعال هوټل نسته. مهرباني وکړئ وروسته بیا هڅه وګړي.
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>