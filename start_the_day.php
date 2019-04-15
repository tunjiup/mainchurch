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
<div class="special_div">
    <p class="notification green">You are logged in</p>
    <?php
        $db = getDbInstance();
        $db->where("day", date('Y-m-d'));
        $db->where("day_started", true);
        $db->where("day_ended", 1, "!=");
        $db->where("endorsed_by", 'supercashr');

        $row = $db->get('start_and_end_day_controller');

        if ($db->count>=1) {
        ?>
        <h1 class="notification red">The day is currently ongoing</h1>
        <h2 class="notification green">You can end the day using the button below</h2>
        <a class="special_btn end" href="day_end.php">End the day</a>
        <?php } else {
        ?>
        <h1 class="notification red">The day hasn't been started for the cahsiers to begin work</h1>
        <h2 class="notification green">You can start the day using the button below</h2>
        <a class="special_btn" href="day_start.php">Start the day</a>
        <a class="special_btn end" href="logout.php">Log Out</a>
        <?php } ?>

</div>