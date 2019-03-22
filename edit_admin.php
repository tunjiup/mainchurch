<?php
session_start();
require_once './config/config.php';
require_once 'includes/auth_validate.php';

//User ID for which we are performing operation
$admin_user_id = filter_input(INPUT_GET, 'admin_user_id');
$operation = filter_input(INPUT_GET, 'operation', FILTER_SANITIZE_STRING);
($operation == 'edit') ? $edit = true : $edit = false;
//Serve POST request.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// If non-super user accesses this script via url. Stop the exexution
	if ($_SESSION['admin_type'] !== 'super') {
		// show permission denied message
		echo 'Permission Denied';
		exit();
	}

	// Sanitize input post if we want
	$data_to_update = filter_input_array(INPUT_POST);
	//Check whether the user name already exists ;
	$db = getDbInstance();
	$db->where('user_name', $data_to_update['user_name']);
	$db->where('id', $admin_user_id, '!=');
	//print_r($data_to_update['user_name']);die();
	$row = $db->getOne('admin_accounts');
	//print_r($data_to_update['user_name']);
	//print_r($row); die();

	if (!empty($row['user_name'])) {

		$_SESSION['failure'] = "User name already exists";

		$query_string = http_build_query(array(
			'admin_user_id' => $admin_user_id,
			'operation' => $operation,
		));
		header('location: edit_admin.php?'.$query_string );
		exit;
	}

	$admin_user_id = filter_input(INPUT_GET, 'admin_user_id', FILTER_VALIDATE_INT);
	//Encrypting the password
	$data_to_update['passwd'] = password_hash($data_to_update['passwd'], PASSWORD_DEFAULT);

	$db = getDbInstance();
	$db->where('id', $admin_user_id);
	$stat = $db->update('admin_accounts', $data_to_update);

	if ($stat) {
		$_SESSION['success'] = "Admin user has been updated successfully";

		$db->where('id', $_SESSION['id']);
		$active_admin_user = $db->getOne("admin_accounts");

		$db->where('id', $admin_user_id);
		$edited_admin_info = $db->getOne("admin_accounts");


        //Edit Admin Successful. write and store activity in admin_activity db table
        $active_admin_names = $active_admin_user['surname'] . ' ' . $active_admin_user['firstname'] . '(' . $active_admin_user['user_name'] . ')';

        $edited_admin = $edited_admin_info['surname'] . ' ' . $edited_admin_info['firstname'];

        $write_activity = $active_admin_names . ' edited Admin with username ' . $edited_admin_info['user_name'] . '  on ' . date('D, M, d, Y h:i:s: A');

        $_SESSION['edit-admin_activity'] = $write_activity;

        $user_data = array(
            'admin_id'     => $_SESSION['id'],
            'session_id'   => session_id(),
            'date'         => date('Y-m-d H:i:s'),
            'activity'     => $_SESSION['edit-admin_activity'],
        );
        $returned_id = $db->insert('admin_activity', $user_data);

        //Store the activity information in a log file
        $file = 'logs/log.txt';
        if($handle = fopen($file, 'a')) {
            fwrite($handle, "\n" . $_SESSION['edit-admin_activity']);
            fclose($handle);
        }
	} else {
		$_SESSION['failure'] = "Failed to update Admin user : " . $db->getLastError();
	}

	header('location: admin_users.php');
	exit;

}

//Select where clause
$db = getDbInstance();
$db->where('id', $admin_user_id);

$admin_account = $db->getOne("admin_accounts");

// Set values to $row

// import header
require_once 'includes/header.php';
?>
<div id="page-wrapper">

    <div class="row">
     <div class="col-lg-12">
            <h2 class="page-header">Update User</h2>
        </div>

    </div>
    <?php include_once 'includes/flash_messages.php';?>
    <form class="well form-horizontal" action="" method="post"  id="contact_form" enctype="multipart/form-data">
        <?php include_once './forms/admin_users_form.php';?>
    </form>
</div>




<?php include_once 'includes/footer.php';?>