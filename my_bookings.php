<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$guest_id = $_SESSION['guest_id'];

$bookings = mysqli_query($conn, "SELECT b.*, r.room_number, r.room_type, h.hotel_name 
                                   FROM bookings b
                                   JOIN rooms r ON b.room_id = r.room_id
                                   JOIN hotels h ON b.hotel_id = h.hotel_id
                                   WHERE b.guest_id=$guest_id
                                   ORDER BY b.booking_id DESC");

$page_title = "زما بکنګونه";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <h1>زما بکنګونه</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success">بکنګ په بریالیتوب سره ثبت سو!</div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($bookings) == 0): ?>
        <div class="alert alert-warning">تاسو تر اوسه کوم بکنګ ندی ثبت کړي.</div>
    <?php else: ?>
        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>هوټل</th>
                            <th>اطاق</th>
                            <th>ډول</th>
                            <th>د ننوتلو</th>
                            <th>د وتلو</th>
                            <th>مجموعي بیه</th>
                            <th>حالت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($b = mysqli_fetch_assoc($bookings)): ?>
                        <tr>
                            <td>#<?php echo $b['booking_id']; ?></td>
                            <td><?php echo $b['hotel_name']; ?></td>
                            <td><?php echo $b['room_number']; ?></td>
                            <td><?php echo $b['room_type']; ?></td>
                            <td><?php echo $b['check_in_date']; ?></td>
                            <td><?php echo $b['check_out_date']; ?></td>
                            <td>$<?php echo number_format($b['total_price'], 2); ?></td>
                            <td><?php echo $b['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
