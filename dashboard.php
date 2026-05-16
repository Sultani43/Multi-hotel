<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$hotel_id = getUserHotelId();
$guest_id = $_SESSION['guest_id'];

// شمېرې راوړل
$total_rooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM rooms WHERE hotel_id=$hotel_id"))['count'];
$available_rooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM rooms WHERE hotel_id=$hotel_id AND status='available'"))['count'];
$my_bookings_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE guest_id=$guest_id"))['count'];

// 1. د بکنګونو تادیات (له بکنګ سره تړلې تادیات)
$booking_payments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(p.amount) as total 
    FROM payments p 
    JOIN bookings b ON p.booking_id = b.booking_id 
    WHERE b.guest_id = $guest_id AND p.payment_status='paid'"))['total'];
$booking_payments = $booking_payments ? $booking_payments : 0;

// 2. د خواړو تادیات
$food_payments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total 
    FROM payments 
    WHERE guest_id = $guest_id AND booking_id IS NULL AND payment_status='paid' AND description LIKE '%خواړه%'"))['total'];
$food_payments = $food_payments ? $food_payments : 0;

// 3. د نورو خدمتونو تادیات
$other_payments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total 
    FROM payments 
    WHERE guest_id = $guest_id AND booking_id IS NULL AND payment_status='paid' AND (description NOT LIKE '%خواړه%' OR description IS NULL)"))['total'];
$other_payments = $other_payments ? $other_payments : 0;

// ټول تادیات
$total_payments = $booking_payments + $food_payments + $other_payments;

// وروستي تادیات (۵ وروستي)
$recent_payments = mysqli_query($conn, "
    (SELECT p.*, 'بکنګ' as type 
     FROM payments p 
     JOIN bookings b ON p.booking_id = b.booking_id 
     WHERE b.guest_id = $guest_id AND p.payment_status='paid')
    UNION
    (SELECT p.*, 'مستقیم' as type 
     FROM payments p 
     WHERE p.guest_id = $guest_id AND p.booking_id IS NULL AND p.payment_status='paid')
    ORDER BY payment_date DESC LIMIT 5
");

// وروستي ۵ بکنګونه
$recent_bookings = mysqli_query($conn, "SELECT b.*, r.room_number, r.room_type 
                                          FROM bookings b
                                          JOIN rooms r ON b.room_id = r.room_id
                                          WHERE b.guest_id=$guest_id
                                          ORDER BY b.booking_id DESC LIMIT 5");

$page_title = "زما ډشبورډ";
include '../includes/header.php';
include '../includes/navbar.php';

if (!function_exists('format_af')) {
    function format_af($amount) { return 'Af ' . number_format($amount, 2); }
}
?>

<div class="container">
    <h1>ښه راغلاست، <?php echo $_SESSION['full_name']; ?>!</h1>
    <br>
    <br>
    <!-- لومړی قطار: عمومي شمېرې (۳ کارتونه) -->
    <div class="row">
        <div class="col-4">
            <div class="card stat-card">
                <h3>ټول اطاقونه</h3>
                <p><?php echo $total_rooms; ?></p>
            </div>
        </div>
        <div class="col-4">
            <div class="card stat-card">
                <h3>خالي  اطاقونه</h3>
                <p><?php echo $available_rooms; ?></p>
            </div>
        </div>
        <div class="col-4">
            <div class="card stat-card">
                <h3>زما بکنګونه</h3>
                <p><?php echo $my_bookings_count; ?></p>
            </div>
        </div>
    </div>

    <!-- دوهم قطار: د تادیاتو کارتونه (۴ کارتونه، هر یو col-3) -->
    <div class="row">
        <div class="col-3">
            <div class="card stat-card">
                <h3>د بکنګونو تادیات</h3>
                <p><?php echo format_af($booking_payments); ?></p>
            </div>
        </div>
        <div class="col-3">
            <div class="card stat-card">
                <h3>د خواړو تادیات</h3>
                <p><?php echo format_af($food_payments); ?></p>
            </div>
        </div>
        <div class="col-3">
            <div class="card stat-card">
                <h3>نور خدمتونه</h3>
                <p><?php echo format_af($other_payments); ?></p>
            </div>
        </div>
        <div class="col-3">
            <div class="card stat-card">
                <h3>ټول تادیات</h3>
                <p style="color: #e74c3c;"><?php echo format_af($total_payments); ?></p>
            </div>
        </div>
    </div>

    <!-- وروستي بکنګونه -->
    <div class="card">
        <h2>وروستي بکنګونه</h2>
        <br>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>خونه</th>
                        <th>ډول</th>
                        <th>د ننوتلو</th>
                        <th>د وتلو</th>
                        <th>مجموعي بیه</th>
                        <th>حالت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_bookings) > 0): ?>
                        <?php while ($b = mysqli_fetch_assoc($recent_bookings)): ?>
                        <tr>
                            <td>#<?php echo $b['booking_id']; ?></td>
                            <td><?php echo $b['room_number']; ?></td>
                            <td><?php echo $b['room_type']; ?></td>
                            <td><?php echo $b['check_in_date']; ?></td>
                            <td><?php echo $b['check_out_date']; ?></td>
                            <td><?php echo format_af($b['total_price']); ?></td>
                            <td><?php echo $b['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center;">تاسو تر اوسه کوم بکنګ ندی کړي .<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- وروستي تادیات -->
    <div class="card">
        <h2>وروستي تادیات</h2>
        <br>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>نیټه</th>
                        <th>اندازه</th>
                        <th>طریقه</th>
                        <th>ډول</th>
                        <th>تفصیل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_payments) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($recent_payments)): ?>
                        <tr>
                            <td><?php echo $p['payment_date']; ?></td>
                            <td><?php echo format_af($p['amount']); ?></td>
                            <td><?php echo $p['payment_method']; ?></td>
                            <td><?php echo $p['type']; ?></td>
                            <td><?php echo htmlspecialchars($p['description']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <td><td colspan="5" style="text-align: center;">تر اوسه کومه تادیه نسته.<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>