<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$hotel_id = getUserHotelId();
$hotel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT facilities FROM hotels WHERE hotel_id=$hotel_id"));

// اسانتیاوې په نویو کرښو وېشل (هره کرښه یوه اسانتیا)
$facilities = [];
if (!empty($hotel['facilities'])) {
    $facilities = explode("\n", trim($hotel['facilities']));
}

$page_title = "اسانتیاوې";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <h1>زموږ اسانتیاوي</h1>
    <br>
    <br>
    <?php if (empty($facilities) || (count($facilities) == 1 && trim($facilities[0]) == '')): ?>
        <div class="alert alert-warning">د اسانتیاوو معلومات نسته.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($facilities as $facility): ?>
                <?php $facility = trim($facility); if (!empty($facility)): ?>
                <div class="col-4">
                    <div class="card" style="text-align: center;">
                        <i class="fas fa-check-circle" style="color: var(--success); font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h3><?php echo $facility; ?></h3>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
