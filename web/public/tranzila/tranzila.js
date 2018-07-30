jQuery(document).ready(function ($) {

	Tranzila = {
		$wrap : $('#iframeWrap'),		
		iframeUrl : 'https://direct.tranzila.com/amsntest/iframe.php?currency=1&buttonLabel=שמירת פרטי אשראי&lang=il&tranmode=VK&' + 
					'nologo=1&trButtonColor=00AEEF&trTextColor=444444&customer=123&sum=1&hidesum=1',
		
		showIframe : function(){
			var iframeUrl = this.iframeUrl;

			this.$wrap.html('<iframe id="iframe" width="370" height="1000" scrolling="no" frameborder="0" src="' + iframeUrl + '"></iframe>');
		},
		
		showResult : function(data){		
			
			if(data.ok) 
				this.$wrap.html('OK: הנתונים נשמרו, תודה!');
			else {
				this.$wrap.html('ERROR: שגיאת באיבוד נתונים: ' + '<br/>' + data.error);
				var func = this.showIframe;
				setTimeout(func, 1500);
			}
		}
	};
});