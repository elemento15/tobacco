<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Ventas por Rango</title>
    <style type="text/css">
        body {
            font-family: 'sans-serif';
        }
        h3, h5 {
            text-align: center;
            margin: 0px 0px 3px 0px;
        }
        .cls-content {
            font-size: 12px;
        }
        .cls-content thead tr {
            background-color: #dedede;
            font-size: 11px;
        }
        .cls-content td {
            border-bottom: 1px solid #aaa;
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
            background-color: #dedede;
        }
    </style>
</head>
<body>
    <h3>Reporte de Ventas por Rango</h3>
    <h5>
        <div>Del {{ $ini_date }} al {{ $end_date }}</div>
    </h5>
    <hr>

    <div style="text-align: center; margin: 0px 30px;">
        <table class="cls-content" cellspacing="0" cellpadding="4" width="100%">
            <thead>
                <tr>
                    <th align="center">Vendedor</th>
                    <th align="center" width="40">%</th>
                    <th align="center" width="65">Liquidaciones</th>
                    <th align="center" width="65">Costo</th>
                    <th align="center" width="65">Utilidad</th>
                    <th align="center" width="40">Cajas</th>
                    <th align="center" width="50">Paquetes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $item)
                    @php
                    $sum_price += $item['price'];
                    $sum_cost  += $item['cost'];
                    $sum_items += $item['items'];
                    $sum_packs += $item['packs'];
                    @endphp
                    
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td align="right">{{ number_format($item['percent'] * 100, 1) }}%</td>
                        <td align="right">$ {{ number_format($item['price'], 2) }}</td>
                        <td align="right">$ {{ number_format($item['cost'], 2) }}</td>
                        <td align="right">$ {{ number_format($item['price'] - $item['cost'], 2) }}</td>
                        <td align="right">{{ number_format($item['items'], 1) }}</td>
                        <td align="right">{{ number_format($item['packs'], 0) }}</td>
                    </tr>
                @endforeach

                <tr class="cls-total">
                    <td colspan="2">Total</td>
                    <td align="right">$ {{ number_format($sum_price, 2) }}</td>
                    <td align="right">$ {{ number_format($sum_cost, 2) }}</td>
                    <td align="right">$ {{ number_format($sum_price - $sum_cost, 2) }}</td>
                    <td align="right">{{ number_format($sum_items, 1) }}</td>
                    <td align="right">{{ number_format($sum_packs, 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</body>
</html>