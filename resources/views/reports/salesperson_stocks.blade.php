<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Reporte de Existencias</title>
	<style type="text/css">
		body {
			font-family: 'sans-serif';
		}
		h3, h5 {
			text-align: center;
			margin:	0px 0px 3px 0px;
		}
		.cls-content {
			font-size: 12px;
		}
		.cls-content thead tr {
			background-color: #dedede;
			font-size: 11px;
		}
		.cls-negative {
			color: red;
			font-weight: bold;
		}
		.cls-small {
			font-size: 11px;
		}
		.cls-total {
			font-weight: bold;
			font-style: italic;
			background-color: #dedede;
		}
	</style>
</head>
<body>
	<h3>Reporte de Existencias</h3>
	<h5>Vendedor: {{ $salesperson }}</h5>
	<hr>

	<div style="text-align: center; margin: 0px 60px;">
		<table class="cls-content" cellspacing="0" cellpadding="4" border="1" width="100%">
			<thead>
				<tr>
					<th align="center" width="20">#</th>
					<th align="center">MARCA</th>
					<th align="center" width="60">TIPO</th>
					<th align="center" width="55">CAJAS</th>
					<th align="center" width="55">PAQUETES</th>
					<th align="center" width="65">IMPORTE</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($stocks as $key => $stock)
					@php
					$boxes = $stock['quantity'] / $stock['packs_per_box'];
					$item_price = $stock['quantity'] * $stock['price'];

					$sum_boxes += $boxes;
					$sum_packs += $stock['quantity'];
					$sum_price += $item_price;
					@endphp
					
					<tr>
						<td align="center" style="font-size: 11px;">{{ $key + 1 }}</td>
						<td>{{ $stock['name'] }}</td>
						<td class="cls-small">{{ $stock['type'] }}</td>
						<td align="right">
							@if ($boxes >= 0)
							<span>{{ number_format($boxes, 2) }}</span>
							@else
							<span class="cls-negative">{{ number_format($boxes, 2) }}</span>
							@endif
						</td>
						<td align="right">
							@if ($stock['quantity'] >= 0)
							<span>{{ number_format($stock['quantity'], 0) }}</span>
							@else
							<span class="cls-negative">{{ number_format($stock['quantity'], 0) }}</span>
							@endif
						</td>
						<td align="right">
							@if ($item_price >= 0)
							<span>$ {{ number_format($item_price, 2) }}</span>
							@else
							<span class="cls-negative">$ {{ number_format($item_price, 2) }}</span>
							@endif
						</td>
					</tr>
				@endforeach

				<tr class="cls-total">
					<td colspan="3" align="right">Total: </td>
					<td align="right">{{ number_format($sum_boxes, 2) }}</td>
					<td align="right">{{ number_format($sum_packs, 0) }}</td>
					<td align="right">$ {{ number_format($sum_price, 2) }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</body>
</html>