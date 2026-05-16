<?php
require_once '../includes/config.php';
if (!isUser()) {
    header('Location: ../login.php');
    exit;
}

$hotel_id = getUserHotelId();
$guest_id = $_SESSION['guest_id'];

// د اصلي میلمه اوسنی معلومات (د guests جدول څخه) – د مخکې ډکولو لپاره
$guest_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name, last_name, email, phone, id_card_number, permanent_address, country FROM guests WHERE guest_id = $guest_id"));

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: rooms.php');
    exit;
}

$room_id = (int)$_GET['id'];
$room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE room_id=$room_id AND hotel_id=$hotel_id"));
if (!$room) {
    header('Location: rooms.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $check_in_time = mysqli_real_escape_string($conn, $_POST['check_in_time']);

    // د اصلي میلمه معلومات له فورمې څخه
    $primary_full_name = mysqli_real_escape_string($conn, $_POST['primary_full_name']);
    $primary_id_card = mysqli_real_escape_string($conn, $_POST['primary_id_card']);
    $primary_age = (int)$_POST['primary_age'];
    $primary_province = mysqli_real_escape_string($conn, $_POST['primary_province']);
    $primary_perm_addr = mysqli_real_escape_string($conn, $_POST['primary_permanent_address']);
    $primary_curr_addr = mysqli_real_escape_string($conn, $_POST['primary_current_address']);
    $primary_gender = mysqli_real_escape_string($conn, $_POST['primary_gender']);
    $primary_travel_purpose = mysqli_real_escape_string($conn, $_POST['primary_travel_purpose']);

    $today = date('Y-m-d');
    if ($check_in < $today) {
        $error = "د ننوتلو نیټه باید نن یا راتلونکې وي.";
    } elseif ($check_out <= $check_in) {
        $error = "د وتلو نیټه باید د ننوتلو نیټې څخه وروسته وي.";
    } else {
        $datetime1 = new DateTime($check_in);
        $datetime2 = new DateTime($check_out);
        $nights = $datetime1->diff($datetime2)->days;
        $total_price = $nights * $room['price_per_night'];

        $conflict = mysqli_query($conn, "SELECT booking_id FROM bookings 
                                          WHERE room_id=$room_id 
                                            AND status NOT IN ('cancelled', 'checked_out')
                                            AND ( (check_in_date <= '$check_out' AND check_out_date >= '$check_in') )");
        if (mysqli_num_rows($conflict) > 0) {
            $error = "بخښنه غواړم، دا خونه په دې نیټو کې نیول سوي ده.";
        } else {
            $insert = "INSERT INTO bookings (hotel_id, guest_id, room_id, check_in_date, check_out_date, check_in_time, total_price, status) 
                       VALUES ($hotel_id, $guest_id, $room_id, '$check_in', '$check_out', '$check_in_time', $total_price, 'pending')";
            if (mysqli_query($conn, $insert)) {
                $booking_id = mysqli_insert_id($conn);
                
                // 1. د اصلي میلمه ریکارډ په booking_occupants کې (is_primary = 1)
                $primary_query = "INSERT INTO booking_occupants 
                                  (booking_id, is_primary, full_name, id_card_number, age, province, permanent_address, current_address, gender, travel_purpose, relation_to_primary) 
                                  VALUES ($booking_id, 1, '$primary_full_name', '$primary_id_card', $primary_age, '$primary_province', '$primary_perm_addr', '$primary_curr_addr', '$primary_gender', '$primary_travel_purpose', 'خپل ځان')";
                mysqli_query($conn, $primary_query);
                
                // 2. د اضافي اوسېدونکو ریکارډونه (is_primary = 0)
                if (isset($_POST['occupants']) && is_array($_POST['occupants'])) {
                    foreach ($_POST['occupants'] as $occ) {
                        if (empty($occ['full_name'])) continue;
                        $full_name = mysqli_real_escape_string($conn, $occ['full_name']);
                        $id_card = mysqli_real_escape_string($conn, $occ['id_card_number']);
                        $age = (int)$occ['age'];
                        $province = mysqli_real_escape_string($conn, $occ['province']);
                        $perm_addr = mysqli_real_escape_string($conn, $occ['permanent_address']);
                        $curr_addr = mysqli_real_escape_string($conn, $occ['current_address']);
                        $gender = mysqli_real_escape_string($conn, $occ['gender']);
                        $travel_purpose = mysqli_real_escape_string($conn, $occ['travel_purpose']);
                        $relation = mysqli_real_escape_string($conn, $occ['relation']);
                        
                        $occ_query = "INSERT INTO booking_occupants 
                                      (booking_id, is_primary, full_name, id_card_number, age, province, permanent_address, current_address, gender, travel_purpose, relation_to_primary) 
                                      VALUES ($booking_id, 0, '$full_name', '$id_card', $age, '$province', '$perm_addr', '$curr_addr', '$gender', '$travel_purpose', '$relation')";
                        mysqli_query($conn, $occ_query);
                    }
                }
                
                header('Location: my_bookings.php?msg=success');
                exit;
            } else {
                $error = "تېروتنه: " . mysqli_error($conn);
            }
        }
    }
}

$page_title = "د خونې بکنګ";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <h1 class="card-title">اطاق<?php echo $room['room_number']; ?> بکنګ</h1>
        <p><strong>بیه فی شپه:</strong> $<?php echo number_format($room['price_per_night'], 2); ?></p>
        <p><strong>ټول ظرفیت:</strong> <?php echo $room['capacity']; ?> کسان</p>

        <br>
        <br>

        <form method="POST" id="bookingForm">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>د ننوتلو نیټه <span style="color: #e74c3c;">*</span></label>
                        <input type="date" name="check_in" id="check_in" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>د وتلو نیټه <span style="color: #e74c3c;">*</span></label>
                        <input type="date" name="check_out" id="check_out" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>د ننوتلو وخت (بجي)</label>
                        <input type="time" name="check_in_time" value="14:00" step="60">
                    </div>
                </div>
            </div>

            <!-- === د اصلي میلمه برخه (کارونکی خپل معلومات) === -->
            <div class="card" style="margin-bottom: 20px; background: #f8fafc;">
                <h3>ستاسو (اصلي میلمه) معلومات</h3>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>بشپړ نوم</label>
                            <input type="text" name="primary_full_name" class="form-control" value="<?php echo htmlspecialchars($guest_info['first_name'] . ' ' . $guest_info['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>د تذکرې نمبر</label>
                            <input type="text" name="primary_id_card" class="form-control" value="<?php echo htmlspecialchars($guest_info['id_card_number']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>عمر</label>
                            <input type="number" name="primary_age" class="form-control" min="0">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>ولایت</label>
                            <input type="text" name="primary_province" class="form-control" placeholder="اصلي ولایت" value="<?php echo htmlspecialchars($guest_info['country']); ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>جنس</label>
                            <select name="primary_gender" class="form-control">
                                <option value="male">نر</option>
                                <option value="female">ښځه</option>
                                <option value="child">ماشوم</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>اصلي استوګنځای</label>
                    <input type="text" name="primary_permanent_address" class="form-control" value="<?php echo htmlspecialchars($guest_info['permanent_address']); ?>">
                </div>
                <div class="form-group">
                    <label>اوسنی استوګنځای</label>
                    <input type="text" name="primary_current_address" class="form-control" value="<?php echo htmlspecialchars($guest_info['current_address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>د سفر موخه</label>
                    <input type="text" name="primary_travel_purpose" class="form-control" placeholder="سوداګري، درملنه، ګرځندوي، ...">
                </div>
            </div>

            <!-- د نورو اوسېدونکو کانتینر -->
            <div id="occupants-container"></div>

            <button type="submit" class="btn btn-primary">د بکنګ تایید </button>
            <a href="rooms.php" class="btn">لغوه کول</a>
        </form>
    </div>
</div>

<script>
    const roomCapacity = <?php echo (int)$room['capacity']; ?>;
    const occupantsContainer = document.getElementById('occupants-container');

    function generateOccupantFields(capacity) {
        if (!capacity || capacity <= 1) {
            occupantsContainer.innerHTML = '';
            return;
        }
        let extra = capacity - 1;
        let html = '<h3>د فرعي میلمنو معلومات</h3>';
        

        for (let i = 1; i <= extra; i++) {
            html += `
                <div class="card" style="margin-bottom: 15px; padding: 10px;">
                    <h4>اوسېدونکی ${i}</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>بشپړ نوم</label>
                                <input type="text" name="occupants[${i}][full_name]" class="form-control" placeholder="بشپړ نوم">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>د تذکرې نمبر</label>
                                <input type="text" name="occupants[${i}][id_card_number]" class="form-control" placeholder="تذکره / پاسپورټ">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>عمر</label>
                                <input type="number" name="occupants[${i}][age]" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>ولایت</label>
                                <input type="text" name="occupants[${i}][province]" class="form-control" placeholder="اصلي ولایت">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>جنس</label>
                                <select name="occupants[${i}][gender]" class="form-control">
                                    <option value="male">نر</option>
                                    <option value="female">ښځه</option>
                                    <option value="child">ماشوم</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>اصلي استوګنځای</label>
                        <input type="text" name="occupants[${i}][permanent_address]" class="form-control" placeholder="ولایت، ولسوالۍ، کلي...">
                    </div>
                    <div class="form-group">
                        <label>اوسنی استوګنځای</label>
                        <input type="text" name="occupants[${i}][current_address]" class="form-control" placeholder="چي اوس پکښي اوسیږي">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>د سفر موخه</label>
                                <input type="text" name="occupants[${i}][travel_purpose]" class="form-control" placeholder="سوداګري، ګرځندوي، ...">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>اړیکه له اصلي میلمه سره</label>
                                <input type="text" name="occupants[${i}][relation]" class="form-control" placeholder="ورور، مور، ...">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        occupantsContainer.innerHTML = html;
    }

    generateOccupantFields(roomCapacity);
</script>

<?php include '../includes/footer.php'; ?>