<!DOCTYPE html>
<html>
<head>
	<title>Makolet API - Performance test</title>
    <link href="{{ url('/css/font-awesome.min.css') }}" rel='stylesheet' type='text/css'>
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
    	.col-md-2 {
    		text-align: center;
    	}
    	.container {
    		background-color: #f5f5f5;
    		border-radius: 5px;
    		padding: 30px 50px 50px;
    		margin: 50px auto;
    		box-shadow: 0 3px 3px #ccc;
    	}
    	.progress {
    		background-color: white;
    	}
    	.time {
			background: white;
			border-radius: 4px;
			margin-top: 5px;
			box-shadow: inset 0 1px 2px #ccc;
			padding: 5px;
    	}
    </style>
</head>
<body>

	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h1>Makolet API</h1>
				<p class="lead">
					Automated performance test
					<span class="btn btn-primary pull-right" id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></span>
				</p>
				<div class="progress progress-striped active">
					<div class="progress-bar" id="progress-bar" style="width: 0%;"></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
				Login as customer
				<div class="time" id="time-1">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
			<div class="col-md-2">
				Login as shop
				<div class="time" id="time-2">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
			<div class="col-md-2">
				Create an order
				<div class="time" id="time-3">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
			<div class="col-md-2">
				Send a proposal
				<div class="time" id="time-4">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
			<div class="col-md-2">
				Accept the proposal
				<div class="time" id="time-5">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
			<div class="col-md-2">
				Close the order
				<div class="time" id="time-6">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
		</div>
		
	</div>

	<script src="{{ url('/js/jquery.min.js') }}"></script>
	<script src="{{ url('/js/bootstrap.min.js') }}"></script>
	<script>

		var reqs = [
			{
				func: login,
				params: {
					email: 'amsn2001@gmail.com',
					password: '654987'
				},
				share: {
					customerToken: 'token'
				}
			},
			{
				func: login,
				params: {
					email: 'edna@makolet.biz',
					password: 'parola'
				},
				share: {
					shopToken: 'token',
					shopId: 'user.shop.id'
				}
			},
			{
				func: createOrder,
				share: {
					orderId: 'data.id',
					proposalId: 'data.proposals.0.id'
				}
			},
			{
				func: sendProposal
			},
			{
				func: acceptProposal
			},
			{
				func: closeOrder
			}
		];

		var shared = {};

		var reqCounter = 0;

		start();

		$("#refresh").click(function() {
			if ($(this).hasClass('disabled')) return;
			$(this).addClass('disabled');
			$("#progress-bar").css("width", "0%").removeClass("progress-bar-success");
			$("#progress-bar").parent().addClass("progress-striped");
			$(".time").each(function() {
				$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
			});
			start();
		});

		function start() {
			reqCounter = 0;
			var req = reqs[0];
			req.func(req.params);
		}

		function handleResponse(xhr) {
			xhr.end = new Date();
			var response = JSON.parse(xhr.responseText);
			reqs[reqCounter].response = response;
			response.time = xhr.end.getTime() - xhr.start.getTime();
			if (reqs[reqCounter].share) {
				var share = reqs[reqCounter].share;
				for (var name in share) {
					var levels = share[name].split('.');
					var sharedValue = reqs[reqCounter].response;
					for (var i in levels) {
						sharedValue = sharedValue[levels[i]];
					}
					shared[name] = sharedValue;
				}
			}
			reqCounter++;
			updateUI(reqCounter, response.time);
			if (reqCounter < reqs.length) {
				var req = reqs[reqCounter];
				req.func(req.params);
			}
		}

		// Login

		function login(params) {
			var data = "email=" + params.email + "&password=" + params.password;
			var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					handleResponse(this);
				}
			});
			xhr.open("POST", "https://makolet.biz/api/auth/login");
			xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
			xhr.start = new Date();
			xhr.send(data);
		}

		// Create new order

		function createOrder(params = shared) {
			var data = {
				"goods":["coca-cola"],
				"quantities":[1],
				"shops":[params.shopId],
				"city":"Sofia",
				"street":"Poduevo",
				"building":"7",
				"latitude":"33.3333",
				"longitude":"33.3333"
			};
			var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					handleResponse(this);
				}
			});
			xhr.open("POST", "https://makolet.biz/api/orders");
			xhr.setRequestHeader("authorization", "Bearer " + params.customerToken);
			xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
			xhr.start = new Date();
			xhr.send(JSON.stringify(data));
		}

		// Send proposal

		function sendProposal(params = shared) {
			var data = "proposal=" + params.proposalId + "&delivery_time=30";
			var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					handleResponse(this);
				}
			});
			xhr.open("POST", "https://makolet.biz/api/proposals/propose");
			xhr.setRequestHeader("authorization", "Bearer " + params.shopToken);
			xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
			xhr.start = new Date();
			xhr.send(data);
		}

		// Accept proposal

		function acceptProposal(params = shared) {
			var data = "proposal=" + params.proposalId + "&accept=1";
			var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					handleResponse(this);
				}
			});
			xhr.open("POST", "https://makolet.biz/api/proposals/accept");
			xhr.setRequestHeader("authorization", "Bearer " + params.customerToken);
			xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
			xhr.start = new Date();
			xhr.send(data);
		}

		// Close order

		function closeOrder(params = shared) {
			var data = "order=" + params.orderId + "&price=1";
			var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					handleResponse(this);
				}
			});
			xhr.open("POST", "https://makolet.biz/api/orders/close");
			xhr.setRequestHeader("authorization", "Bearer " + params.shopToken);
			xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
			xhr.start = new Date();
			xhr.send(data);
		}

		// Delete order

		function deleteOrder(params = shared) {
			var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", function () {
				if (this.readyState === 4) {
					handleResponse(this);
				}
			});
			xhr.open("DELETE", "https://makolet.biz/api/orders/" + params.orderId);
			xhr.setRequestHeader("authorization", "Bearer " + params.customerToken);
			xhr.start = new Date();
			xhr.send();
		}

		// Update UI

		function updateUI(i, time) {
			var width = (100 / reqs.length) * i;
			$("#time-" + i).html('<i class="fa fa-clock-o" aria-hidden="true"></i> ' + time + ' ms');
			if (width >= 100) {
				$("#progress-bar").css("width", "100%").addClass("progress-bar-success");
				$("#progress-bar").parent().removeClass("progress-striped");
				$("#refresh").removeClass('disabled');
			} else {
				$("#progress-bar").css("width", width.toString() + "%");
			}
		}

	</script>
</body>
</html>