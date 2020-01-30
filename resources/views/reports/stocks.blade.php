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
			font-size: 14px;
		}
		.cls-content thead tr {
			background-color: #dedede;
			font-size: 11px;
		}
		.cls-negative {
			color: red;
			font-weight: bold;
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
	<h5>Almacén: {{ $warehouse }}</h5>
	<hr>

	<div style="text-align: center; margin: 0px 160px;">
		<table class="cls-content" cellspacing="0" cellpadding="4" border="1" width="100%">
			<thead>
				<tr>
					<th align="center" width="20">#</th>
					<th align="center">MARCA</th>
					@if ($use_boxes)
					<th align="center" width="55">CAJAS</th>
					@else
					<th align="center" width="55">PAQUETES</th>
					@endif
				</tr>
			</thead>
			<tbody>
				@foreach ($stocks as $key => $stock)
					@php
					$quantity = $stock['quantity'] / (($use_boxes) ? $stock['packs_per_box'] : 1);
					$total += $quantity;
					@endphp
					
					<tr>
						<td align="center" style="font-size: 11px;">{{ $key }}</td>
						<td>{{ $stock['name'] }}</td>
						<td align="right">
							@if ($quantity >= 0)
							<span>{{ number_format($quantity) }}</span>
							@else
							<span class="cls-negative">{{ number_format($quantity) }}</span>
							@endif
						</td>
					</tr>
				@endforeach

				<tr class="cls-total">
					<td colspan="2" align="right">Total: </td>
					<td align="right">{{ number_format($total) }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</body>
</html>