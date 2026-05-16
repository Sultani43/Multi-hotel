<?php
require_once 'includes/config.php';
if (!isHotelAdmin() && !isSuperAdmin()) {
    die('لاسرسی نلری');
}

if (!isset($_GET['booking_id'])) {
    die('د بکنګ ID نشته');
}

$booking_id = (int)$_GET['booking_id'];
$hotel_id = isHotelAdmin() ? getUserHotelId() : null;

// د بکنګ معلومات راوړل
$query = "SELECT b.*, g.first_name, g.last_name, g.phone, g.email, g.permanent_address, g.current_address, 
                 r.room_number, r.price_per_night, h.hotel_name, h.hotel_address, h.hotel_phone, h.hotel_email 
          FROM bookings b
          JOIN guests g ON b.guest_id = g.guest_id
          JOIN rooms r ON b.room_id = r.room_id
          JOIN hotels h ON b.hotel_id = h.hotel_id
          WHERE b.booking_id = $booking_id";
if ($hotel_id) {
    $query .= " AND b.hotel_id = $hotel_id";
}
$result = mysqli_query($conn, $query);
$booking = mysqli_fetch_assoc($result);
if (!$booking) {
    die('بکنګ پیدا نشو');
}

// د خونې بیه محاسبه
$check_in = new DateTime($booking['check_in_date']);
$check_out = new DateTime($booking['check_out_date']);
$nights = $check_in->diff($check_out)->days;
$room_total = $nights * $booking['price_per_night'];

// د خواړو امرونه
$food_orders = mysqli_query($conn, "SELECT o.*, f.item_name, f.price 
                                      FROM food_orders o
                                      JOIN food_items f ON o.item_id = f.item_id
                                      WHERE o.guest_id = {$booking['guest_id']} AND o.status='served'");
$food_items = [];
$food_total = 0;
while ($fo = mysqli_fetch_assoc($food_orders)) {
    $subtotal = $fo['quantity'] * $fo['price'];
    $food_total += $subtotal;
    $food_items[] = [
        'name' => $fo['item_name'],
        'qty' => $fo['quantity'],
        'price' => $fo['price'],
        'total' => $subtotal
    ];
}

$grand_total = $room_total + $food_total;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>بیل #<?php echo $booking_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        .bill-box { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; background: white; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .hotel-name { font-size: 24px; font-weight: bold; color: #4361ee; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; border-collapse: collapse; }
        .info td { padding: 5px; }
        .items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items th { background: #4361ee; color: white; padding: 10px; text-align: left; }
        .items td { padding: 10px; border-bottom: 1px solid #ddd; }
        .total-row { font-weight: bold; background: #f8fafc; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #777; }
        .print-btn { text-align: center; margin-top: 20px; }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="bill-box">
        <div class="header">
            <div class="hotel-name"><?php echo htmlspecialchars($booking['hotel_name']); ?></div>
            <div><?php echo htmlspecialchars($booking['hotel_address']); ?></div>
            <div>تلیفون: <?php echo htmlspecialchars($booking['hotel_phone']); ?> | برېښنالیک: <?php echo htmlspecialchars($booking['hotel_email']); ?></div>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td><strong>د بکنګ نمبر:</strong> #<?php echo $booking['booking_id']; ?></td>
                    <td><strong>نیټه:</strong> <?php echo date('Y-m-d H:i'); ?></td>
                </tr>
                <tr>
                    <td><strong>میلمه:</strong> <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                    <td><strong>تلیفون:</strong> <?php echo htmlspecialchars($booking['phone']); ?></td>
                </tr>
                <tr>
                    <td><strong>خونه:</strong> <?php echo $booking['room_number']; ?></td>
                    <td><strong>برېښنالیک:</strong> <?php echo htmlspecialchars($booking['email']); ?></td>
                </tr>
                <tr>
                    <td><strong>د ننوتلو:</strong> <?php echo $booking['check_in_date']; ?></td>
                    <td><strong>د وتلو:</strong> <?php echo $booking['check_out_date']; ?></td>
                </tr>
                <?php if (!empty($booking['current_address'])): ?>
                <tr>
                    <td colspan="2"><strong>اوسنی استوګنځای:</strong> <?php echo htmlspecialchars($booking['current_address']); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <h3>د بیل توضیحات</h3>
        <table class="items">
            <thead>
                <tr>
                    <th>تفصیل</th>
                    <th>تعداد</th>
                    <th>واحد نرخ</th>
                    <th>مجموعه</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>خونه (<?php echo $nights; ?> شپې)</td>
                    <td>1</td>
                    <td>$<?php echo number_format($booking['price_per_night'], 2); ?></td>
                    <td>$<?php echo number_format($room_total, 2); ?></td>
                </tr>
                <?php foreach ($food_items as $food): ?>
                <tr>
                    <td><?php echo htmlspecialchars($food['name']); ?></td>
                    <td><?php echo $food['qty']; ?></td>
                    <td>$<?php echo number_format($food['price'], 2); ?></td>
                    <td>$<?php echo number_format($food['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">مجموعه:</td>
                    <td>$<?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>له تاسو مننه چې زموږ میلمه یاست. هیله ده بیا مو ښه راغلاست وایو.</p>
        </div>

        <div class="print-btn">
            <button onclick="window.print()">چاپ</button>
            <button onclick="window.close()">تړل</button>
        </div>
    </div>
</body>
</html>