<?php
require_once './config/config.php';
session_start();

if ($_SESSION['admin_type'] !== 'super'
    && $_SESSION['admin_type'] !== 'supercashr'
) {
    echo "
            <script type='text/javascript'>alert('Unauthorized to access this page');
                window.location='logout.php';
            </script>
        ";
}
?>

<head>
    <link rel="stylesheet" href="assets/css/special-notification-and-extras.css">
</head>

<?php

$db = getDbInstance();
$db->where("day", date('Y-m-d'));

$data = array(
    'day_ended'=> true,
    'time_day_ended' => date('Y-m-d H:i:s'),
);
$update = $db->update("start_and_end_day_controller", $data);

if($update) {

    ?>
<div class="special_div">
    <h1 class="notification">Day Ended Successfully</h1>
    <a class="special_btn" href="/logout.php">Log Out</a>
</div>
    <?php

}