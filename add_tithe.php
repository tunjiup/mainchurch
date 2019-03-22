<?php
session_start();
require_once './config/config.php';
require_once './includes/auth_validate.php';



//serve POST method, After successful insert, redirect to members.php page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

        function generateTranID()

    {
    
                $regid = 'TRN'.''.mt_rand(2000000, 9999999);
                return $regid;
    
    }
    $getTranID = generateTranID();
    //echo $getappnum;
    
    
    
    function checkTranID($getregid)
    {
                        $getTranID = mysql_query("SELECT trans_id FROM tb_payment WHERE trans_id ='$getTranID'");
                                if (mysql_num_rows($getTranID) > 0):
                                echo 'Record Dey';
                                generateTranID();
                                else:
                                                    return $getTranID;
                                endif;
    }
    
    function getReceiptNumber()

    {
        $db = getDbInstance();
        $db->where("CashierAssigned", $_SESSION['username']);
        $db->where("UsuageStatus", '1');
        $row = $db->get('receiptnumberpool');
        if ($db->count >=1) {
            $ReceiptStart = $row[0]['ReceiptNumber'];
            $ReceiptUsed =  $row[0]['UsedReceiptNumber'];
            $ReceiptNumber = ltrim($ReceiptStart, '0') + $ReceiptUsed;
            $NewReceiptNumbers = sprintf('%07d', $ReceiptNumber);
            $ReceiptUsedUpdate = $ReceiptUsed + 1;
            
            $db = getDbInstance();
            $db->where('CashierAssigned', $_SESSION['username']);
            $db->where('UsuageStatus', '1');

            $update_remember = array(
                'UsedReceiptNumber'=> $ReceiptUsedUpdate
                );
            $db->update("receiptnumberpool", $update_remember);
        }
        return $NewReceiptNumbers;
    }
    $GeneratedReceiptNumber = GetReceiptNumber();
    $dat_to_store['invoicenum'] =  $GeneratedReceiptNumber;
    $dat_to_store['Dem1000'] = $_POST['1kqty'];
    //Insert timestamp
    $dat_to_store['Dem500'] = $_POST['5hqty'];
    $dat_to_store['Dem200'] = $_POST['2hchk'];
    $dat_to_store['Dem100'] = $_POST['1hqty'];
    $dat_to_store['Dem50'] = $_POST['50qty'];
    $dat_to_store['Dem20'] = $_POST['20qty'];
    $dat_to_store['Dem10'] = $_POST['10qty'];
    $dat_to_store['Dem5'] = $_POST['5qty'];
    $dat_to_store['TransactionCardNumber'] = $_POST['cardno'];
    $dat_to_store['date_received'] = date('Y-m-d H:i:s');
    $dat_to_store['PostedBy'] = $_SESSION['username']; //<!--assign the user in the 

    $db = getDbInstance();
    
    $last_id = $db->insert('denominationanalysis', $dat_to_store);
    //$NewReceiptNumbers = GetReceiptNumber();
    //Mass Insert Data. Keep "name" attribute in html form same as column name in mysql table.
  //  $data_to_store = array_filter($_POST);
    $data_to_store['trans_id'] = $getTranID;
    //Insert timestamp
    $data_to_store['invoicenum'] =  $GeneratedReceiptNumber;
        $data_to_store['memid'] = $_POST['memb_id'];
    $data_to_store['Name_member'] = $_POST['memb_name'];
    $data_to_store['branch_name'] = $_POST['memb_branch'];
    $data_to_store['band_name'] = $_POST['memb_band'];
    $data_to_store['Amount_Paid'] = $_POST['amountpaid'];
    $data_to_store['Payment_Type'] = 'Tithe';
    $data_to_store['payment_mode'] = $_POST['paymode'];
    $data_to_store['payment_description'] = "Tithe for ". $_POST['duration'];
    $data_to_store['date_received'] = date('Y-m-d H:i:s');
    $data_to_store['recusername'] = $_SESSION['username']; //<!--assign the user in the post-->

    $db = getDbInstance();
    
    $last_id = $db->insert('tb_payment', $data_to_store);

    if($last_id)
    {
    	$_SESSION['success'] = "Tithe Information added successfully!";
    	header('location: tithe_receipt.php?page=null&intv='.base64_encode($GeneratedReceiptNumber));
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
            <h2 class="page-header">Post Tithe</h2>
        </div>
        
</div>
    <form class="form" action="" method="post"  id="tithe_form" enctype="multipart/form-data">
       <?php  include_once('./forms/tithe_post.php'); ?>
    </form>
</div>


<script src="assets/js/jquery1.11.1.min.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/daterangepicker.js"></script>
<script src="assets/js/moment.min.js"></script>

<script src="assets/js/typeahead.min.js"></script>
<script src="assets/js/modepick.js"></script>
<script src="assets/js/demomination.js"></script>
<script src="assets/js/currencyvalue.js"></script>
<script src="assets/js/sum_up_values.js"></script>
<link href="assets/css/datepicker.min.css" rel="stylesheet" type="text/css">
<script src="assets/js/datepicker.min.js"></script>
<script src="assets/js/member_split.js"></script>
<style>
input.datepicker-here {width: 100%;}
    </style>

              <script src="assets/js/i18n/datepicker.en.js"></script>
<link rel="stylesheet" href="assets/css/typeahead.css">
<link rel="stylesheet" type="text/css" href="assets/css/daterangepicker.css">
<script>
    $(document).ready(function(){
    $('input.typeahead').typeahead({
        name: 'typeahead',
        remote:'search.php?key=%QUERY',
        limit : 50
    });
});
    </script>
<script type="text/javascript">

$(document).ready(function(){
   $("#tithe_form").validate({
       rules: {
            memb_id: {
                required: true,
                minlength: 3
            },
            memb_name: {
                required: true,
                minlength: 3
            }, 
            memb_band: {
                required: true,
                minlength: 3
            },  
            memb_branch: {
                required: true,
                minlength: 2
            },  
            paymode: {
                required: true,
                minlength: 1
            },   
            amountpaid: {
                required: true,
                minlength: 2
            },         
        }
    });
});
</script>
<script type="text/javascript">
document.getElementById('cashmode').style.display="none";
document.getElementById('cardmode').style.display="none";


</script>


<?php include_once ('includes/footer.php'); ?>
