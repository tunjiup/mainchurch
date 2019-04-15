<?php
//Get DB instance.
$db = getDbInstance();



$db->where("day", date('Y-m-d'));
$db->where("day_started", true);
$db->where("day_ended", "NOT YET");
$db->where("time_day_ended", "NULL");
$db->where("endorsed_by", 'supercashr');

$row = $db->get('start_and_end_day_controller');

if ($db->count<1) {
    //If admin is a supercashier or superadmin, then go to a page to "start the day"
    if ($_SESSION['admin_type']==="supercashr" || $_SESSION['admin_type']==="super") {
        header('Location:start_the_day.php');
    } else {
        // Redirect user
        header('Location: day_not_started.php');
    }

}