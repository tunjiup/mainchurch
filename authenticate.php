<?php
require_once './config/config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = filter_input(INPUT_POST, 'username');
	$passwd = filter_input(INPUT_POST, 'passwd');
	$remember = filter_input(INPUT_POST, 'remember');

	//Get DB instance.
	$db = getDbInstance();

	$db->where("user_name", $username);

	$row = $db->get('admin_accounts');

	if ($db->count >= 1) {

		$db_password = $row[0]['passwd'];
		$user_id = $row[0]['id'];

		if (password_verify($passwd, $db_password)) {

			$_SESSION['user_logged_in'] = TRUE;
			$_SESSION['admin_type'] = $row[0]['admin_type'];
			$_SESSION['username']	= $row[0]['user_name'];

			if ($remember) {

				$series_id = randomString(16);
				$remember_token = getSecureRandomToken(20);
				$encryted_remember_token = password_hash($remember_token,PASSWORD_DEFAULT);


				$expiry_time = date('Y-m-d H:i:s', strtotime(' + 30 days'));

				$expires = strtotime($expiry_time);

				setcookie('series_id', $series_id, $expires, "/");
				setcookie('remember_token', $remember_token, $expires, "/");

				$db = getDbInstance();
				$db->where ('id',$user_id);

				$update_remember = array(
					'series_id'=> $series_id,
					'remember_token' => $encryted_remember_token,
					'expires' =>$expiry_time
				);
				$db->update("admin_accounts", $update_remember);
			}
			//Authentication successful, wrtte user login activity and store in admin_activity db table
			$_SESSION['user_activity'] = $row[0]['surname'] . ' ' . $row[0]['firstname'] . ' logged in as ' . $row[0]['user_name'] . ' on ' . date('D, M, d, Y h:i:s: A');

			$user_data = array(
				'admin_id'		=> $row[0]['id'],
				'session_id'	=> session_id(),
				'date'			=> date('Y-m-d H:i:s'),
				'activity'		=> $_SESSION['user_activity'],

			);
			$returned_id = $db->insert('admin_activity', $user_data);

			//Store the activity information in a log file
			$file = 'logs/log.txt';
			if($handle = fopen($file, 'a')) {
				fwrite($handle, "\n" . $_SESSION['user_activity']);
				fclose($handle);
			}

			// Redirect user
			header('Location:index.php');

		} else {
			$_SESSION['login_failure'] = "Invalid user name or password";
			header('Location:login.php');
		}

		exit;
	} else {
		$_SESSION['login_failure'] = "Invalid user name or password";
		header('Location:login.php');
		exit;
	}

}
else {
	die('Method Not allowed');
}