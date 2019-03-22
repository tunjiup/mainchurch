function select_mode()
{
	if(document.getElementById('paymode').value=="Cash")
		{
                 
            document.getElementById('amountpaid').readOnly = true;
            document.getElementById('amountpaid').value = "";
            document.getElementById('cashmode').style.display="block";
            document.getElementById('cardmode').style.display="none";
            }
    else if(document.getElementById('paymode').value=="Card")
		{
                  
            document.getElementById('amountpaid').readOnly = false;
            document.getElementById('amountpaid').value = "";
            document.getElementById('cashmode').style.display="none";
            document.getElementById('cardmode').style.display="block";
            }
      
      else if(document.getElementById('paymode').value=="")
		{
             alert("Please select a payment mode!");     
            document.getElementById('amountpaid').readOnly = true;
            document.getElementById('amountpaid').value = "";
            document.getElementById('cashmode').style.display="none";
            document.getElementById('cardmode').style.display="none";
            }
      
	
}
