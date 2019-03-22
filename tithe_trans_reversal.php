<?php
session_start();
require_once './config/config.php';
require_once './includes/auth_validate.php';


echo filter_input(INPUT_POST, 'reverse_id');
echo '<br>';
echo $_SERVER['REQUEST_METHOD'];

//serve POST method, After successful insert, redirect to members.php page.

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $invoicenum = filter_input(INPUT_POST, 'reverse_id');   // return $NewReceiptNumbers;   
            $db = getDbInstance();
            $db->where('invoicenum', $invoicenum);
            $db->where('recusername', $_SESSION['username']);
            $update_remember = array(
                'reversal_status'=> '1'
                );
            $db->update("tb_payment", $update_remember);
        }
        
   header('location: reverse_tithe.php');
       
        
    

   
    //	exit();
?>



