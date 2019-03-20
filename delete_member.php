<?php
session_start();
require_once 'includes/auth_validate.php';
require_once './config/config.php';
$del_id = filter_input(INPUT_POST, 'del_id');
if ($del_id && $_SERVER['REQUEST_METHOD'] == 'POST')
{

	if($_SESSION['admin_type']!='super'){
		$_SESSION['failure'] = "You don't have permission to perform this action";
    	header('location: members.php');
        exit;

	}
    $member_id = $del_id;

    $db = getDbInstance();
    $db->where('id', $member_id);
    $deleted_member = $db->getOne('members'); //get the member info before deleting so as to save activity to the admin_activity table
    $db->where('id', $member_id);
    $status = $db->delete('members');

    if ($status)
    {
        $_SESSION['info'] = "member deleted successfully!";

        $db->where('id', $_SESSION['id']);
        $active_admin_user = $db->getOne("admin_accounts");

        //Delete Member Successful. write activity and store in admin_activity db table
        $active_admin_names = $active_admin_user['surname'] . ' ' . $active_admin_user['firstname'] . '(' . $active_admin_user['user_name'] . ')';

        $deleted_member_names = $deleted_member['f_name'] . ' ' . $deleted_member['l_name'];

        $write_activity = $active_admin_names . ' deleted a Member (' . $deleted_member_names . ')  with the email ' . $deleted_member['email'] .  ' on ' . date('D, M, d, Y h:i:s: A');

        $_SESSION['delete-member_activity'] = $write_activity;

        $user_data = array(
            'admin_id'     => $_SESSION['id'],
            'session_id'   => session_id(),
            'date'         => date('Y-m-d H:i:s'),
            'activity'     => $_SESSION['delete-member_activity'],

        );
        $returned_id = $db->insert('admin_activity', $user_data);

        //Store the activity information in a log file
        $file = 'logs/log.txt';
        if($handle = fopen($file, 'a')) {
            fwrite($handle, "\n" . $_SESSION['delete-member_activity']);
            fclose($handle);
        }
        header('location: members.php');
        exit;
    }
    else
    {
    	$_SESSION['failure'] = "Unable to delete member";
    	header('location: members.php');
        exit;

    }

}