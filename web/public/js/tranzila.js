$(function () {

	$('#tranzila-box button').click(function(e) {
		e.preventDefault();
		var customerId = $(this).attr('data-customer-id');
		var iframeUrl = 'https://direct.tranzila.com/amsn2001/iframe.php?currency=1&buttonLabel=שמירת פרטי אשראי&lang=il&tranmode=VK&' + 
					'nologo=1&trButtonColor=00AEEF&trTextColor=444444&sum=1&hidesum=1&customer=' + customerId;
		$('#tranzila-box').html('<iframe id="iframe" width="100%" height="240" scrolling="no" frameborder="0" src="' + iframeUrl + '"></iframe>');
	});

});