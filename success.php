<?php
include 'dbConfig.php';

//$ss=file_REQUEST_contents('php://input');

//Get payment information from PayPal
$item_number = $_REQUEST['item_number'];
$txn_id = $_REQUEST['txn_id'];
$payment_gross = $_REQUEST['mc_gross'];
$currency_code = $_REQUEST['mc_currency'];
$payment_status = $_REQUEST['payment_status'];

//Get product price from database
$productResult = $db->query("SELECT price FROM products WHERE id = ".$item_number);
$productRow = $productResult->fetch_assoc();
$productPrice = $productRow['price'];

if(!empty($txn_id) && $payment_gross == $productPrice){
	//Check if payment data exists with the same TXN ID.
    $prevPaymentResult = $db->query("SELECT payment_id FROM payments WHERE txn_id = '".$txn_id."'");

    if($prevPaymentResult->num_rows > 0){
        $paymentRow = $prevPaymentResult->fetch_assoc();
        $last_insert_id = $paymentRow['payment_id'];
    }else{
        //Insert tansaction data into the database
        $insert = $db->query("INSERT INTO payments(item_number,txn_id,payment_gross,currency_code,payment_status) VALUES('".$item_number."','".$txn_id."','".$payment_gross."','".$currency_code."','".$payment_status."')");
        $last_insert_id = $db->insert_id;
    }
?>
	<h1>Your payment has been successful.</h1>
    <h1>Your Payment ID - <?php echo $last_insert_id; ?>.</h1>
<?php }else{ ?>
	<h1>Your payment has failed.</h1>
<?php } ?>
