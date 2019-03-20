<?php
session_start();
require_once './config/config.php';
require_once './includes/auth_validate.php';


//serve POST method, After successful insert, redirect to members.php page.
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    //Mass Insert Data. Keep "name" attribute in html form same as column name in mysql table.
    $data_to_store = array_filter($_POST);

    //Insert timestamp-
    $data_to_store['created_at'] = date('Y-m-d H:i:s');
   //$data_to_store['user_name'] = $_SESSION['user_name']; <!--assign the user in the post-->
    $data_to_store['user_name'] = '';

    $db = getDbInstance();
    $user_name = $_SESSION['username'];

    $last_id = $db->insert('members', $data_to_store, $user_name);
    //$last_id = $db->insert('members', $data_to_store);

    if($last_id)
    {
        $_SESSION['success'] = "member added successfully!";


        $db->where('id', $_SESSION['id']);
        $active_admin_user = $db->getOne("admin_accounts");

        //Adding of Member Successful. write activity and store in admin_activity db table
        $active_admin_names = $active_admin_user['surname'] . ' ' . $active_admin_user['firstname'] . '(' . $active_admin_user['user_name'] . ')';

        $new_user_names = $data_to_store['f_name'] . ' ' . $data_to_store['l_name'];

        $write_activity = $active_admin_names . ' added a New Member (' . $new_user_names . ') on ' . date('D, M, d, Y h:i:s: A');

        $_SESSION['add-member_activity'] = $write_activity;

        $user_data = array(
            'admin_id'     => $_SESSION['id'],
            'session_id'   => session_id(),
            'date'         => date('Y-m-d H:i:s'),
            'activity'     => $_SESSION['add-member_activity'],

        );
        $returned_id = $db->insert('admin_activity', $user_data);

        //Store the activity information in a log file
        $file = 'logs/log.txt';
        if($handle = fopen($file, 'a')) {
            fwrite($handle, "\n" . $_SESSION['add-member_activity']);
            fclose($handle);
        }

    	header('location: members.php');
    	exit();
    }
    else
    {
        echo 'insert failed: ' . $db->getLastError();
        exit();
    }
}

//We are using same form for adding and editing. This is a create form so declare $edit = false.
$edit = false;

require_once 'includes/header.php';
?>
<div id="page-wrapper">
<div class="row">
     <div class="col-lg-12">
            <h2 class="page-header">Add member</h2>
    </div>

</div>
    <form class="form" action="" method="post"  id="member_form" enctype="multipart/form-data">
       <?php  include_once('./forms/member_form.php'); ?>
    </form>
</div>


<script type="text/javascript">
$(document).ready(function(){
   $("#member_form").validate({
       rules: {
            f_name: {
                required: true,
                minlength: 3
            },
            l_name: {
                required: true,
                minlength: 3
            },
        }
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>