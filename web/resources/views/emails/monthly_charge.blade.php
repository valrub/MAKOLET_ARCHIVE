<div style="font-family: Arial, Verdana, sans-serif;">
	<table style="background: #00ace1; width: 100%;">
		<tr>
			<td style="padding: 20px 20px 20px;">
				<h1 style="color: white; margin: 0;">Invoice</h1>
				<span style="color: white; font-size: 16px;">
					#{{ $payment->id }} / {{ date('d.m.Y') }}
				</span>
			</td>
			<td style="text-align: right;">
				<img src="https://makolet.biz/img/logo-medium.png" style="margin: 10px 20px 20px;" />
			</td>
		</tr>
	</table>
	<br>
	<table style="width: 100%; padding: 15px 30px;">
		<tr>
			<td style="background: #eee; border: none; border-bottom: 3px solid #aaa; width: 50%; padding: 15px;">
				<strong>SUPPLIER</strong>
				<hr style="border: 0; height: 1px; background: #fff;">
				Makolet.biz
			</td>
			<td style="background: #eee; border: none; border-bottom: 3px solid #aaa; width: 50%; padding: 15px;">
				<strong>CUSTOMER</strong>
				<hr style="border: 0; height: 1px; background: #fff;">
				{{ $customer['name'] }}
			</td>
		</tr>
	</table>
	<br>
	<table style="width: 100%; padding: 15px 30px;">
		<thead>
			<tr>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">Order #</th>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">Shop</th>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">Created</th>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">Delivery time</th>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">Total products</th>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">Total shipping</th>
				<th style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 5px 15px;">TOTAL</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($customer['orders'] as $order_id => $order)
			<tr>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">{{ $order_id }}</td>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">{{ $order->name }}</td>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">{{ $order->created_at }}</td>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">{{ $order->delivery_time }}</td>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">{{ $order->price }}</td>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">{{ $order->delivery_price }}</td>
				<td style="border: none; border-bottom: 1px solid #aaa; text-align: left; padding: 5px 15px;">@if ($order->price) {{ $order->price + $order->delivery_price }} @else 0 @endif NIS</td>
			</tr>
			@endforeach
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td style="border: none; border-bottom: 3px solid #aaa; background: #eee; text-align: left; padding: 15px; font-weight: bold;">{{ number_format((float)$customer['total'], 2, '.', '') }} NIS</td>
			</tr>
		</tbody>
	</table>
	<br>
	<p style="color: #777; font-size: 13px; text-align: center; margin: 0; padding: 10px; background: #eee;">If you think that this email is not intended for you, please <a href="https://makolet.biz/contact-us">contact us</a>.</p>
</div>