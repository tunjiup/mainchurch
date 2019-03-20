<?php
session_start();
require_once './config/config.php';
require_once 'includes/auth_validate.php';

//Only super admin is allowed to access this page
if ($_SESSION['admin_type'] !== 'super') {
    // show permission denied message
    echo 'Permission Denied';
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$data_to_store = filter_input_array(INPUT_POST);
    $db = getDbInstance();
    //Check whether the user name already exists ;
    $db->where('user_name',$data_to_store['user_name']);
    $db->get('admin_accounts');

    if($db->count >=1){
        $_SESSION['failure'] = "User name already exists";
        header('location: add_admin.php');
        exit();
    }

    //Encrypt password
    $data_to_store['passwd'] = password_hash($data_to_store['passwd'],PASSWORD_DEFAULT);
    //reset db instance
    $db = getDbInstance();
    $last_id = $db->insert ('admin_accounts', $data_to_store);
    if($last_id)
    {
        $_SESSION['success'] = "Admin user added successfully!";
        $db->where('id', $_SESSION['id']);
        $active_admin_user = $db->getOne("admin_accounts");

        if($data_to_store['admin_type']==='super') {
            $newUserRole = 'Super Admin';
        } else {
            $newUserRole = 'Admin';
        }

        //Adding of User Successful. write activity and store in admin_activity db table
        $active_admin_names = $active_admin_user['surname'] . ' ' . $active_admin_user['firstname'] . '(' . $active_admin_user['user_name'] . ')';

        $new_user_names = $data_to_store['surname'] . ' ' . $data_to_store['firstname'] . '(' . $data_to_store['user_name'] . ')';

        $writeActivity = $active_admin_names . ' added a New Person - ' . $new_user_names . ' as ' . $newUserRole . ' on ' . date('D, M, d, Y h:i:s: A');

        $_SESSION['add-admin_activity'] = $writeActivity;

        $user_data = array(
            'admin_id'     => $_SESSION['id'],
            'session_id'   => session_id(),
            'date'         => date('Y-m-d H:i:s'),
            'activity'     => $_SESSION['add-admin_activity'],

        );
        $returned_id = $db->insert('admin_activity', $user_data);

        //Store the activity information in a log file
        $file = 'logs/log.txt';
        if($handle = fopen($file, 'a')) {
            fwrite($handle, "\n" . $_SESSION['add-admin_activity']);
            fclose($handle);
        }
    	header('location: admin_users.php');
    	exit();
    }

}

$edit = false;


require_once 'includes/header.php';
?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h2 class="page-header">Add User</h2>
		</div>
	</div>
	 <?php
    include_once('includes/flash_messages.php');
    ?>
	<form class="well form-horizontal" action=" " method="post"  id="contact_form" enctype="multipart/form-data">
		<?php include_once './forms/admin_users_form.php'; ?>
	</form>
</div>




<?php include_once 'includes/footer.php'; ?>