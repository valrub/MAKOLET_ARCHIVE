<?php
$isOk = (int)$_GET['ok'] == 1;
$err = '';
if(!$isOk) {
	$respCode = $_POST['Response'];
	switch($respCode) {
		case '004':
			$err = 'Credit card company error'; //סירוב חברת האשראי
			
		case '061':
			$err = 'Invalid card';
			break;
		
		case '039':
			$err = 'Invalid card'; //ספרת ביקורת לא תקינה
			break;

		case '500':
			$err = $resp['error'];
			break;
		
		default:
			$err = 'Payment error';
	}
}

//additional data from $_POST:
//credit card data:
//expmonth [example: 07]
//expyear [example: 17]
//last 4 digits of a number
//user ID [תעודת זהות]
//any other parameter you've passed [in this example it's "data" with a value "xxx"
//Tranzila's reference number [example: 01340001]

//please note: expmonth and expyear must be saved in order to charge the token; can be 2 different field or a single one in format "$expmonth$expyear" e.g. "0117"

//also, for successful transfer:
//ConfirmationCode [for error, it also exists with a value of 0000000]
//TranzilaTK - the token

?>

<html>
	<body>
		<script>
			top.Tranzila.showResult({
				ok : <?= $isOk ? 'true' : 'false' ?>,
				error : '<?= $err ?>'
			});
		</script>
	</body>
</html>