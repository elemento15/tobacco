<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Reporte de Kardex</title>
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
    <h3>Reporte de Kardex</h3>
    <h5>
        <div style="float: left">Marca: {{ $brand }}</div>
        <div style="float: right">Almacén: {{ $warehouse }}</div>
        <div style="clear: both;"></div>
    </h5>
    <hr>

    <div style="text-align: center; margin: 0px;">
        <table class="cls-content" cellspacing="0" cellpadding="4" width="100%">
            <thead>
                <tr>
                    <th align="center" width="28">Folio</th>
                    <th align="center" width="52">Fecha</th>
                    <th align="center" width="30">Tipo</th>
                    <th>Concepto</th>
                    <th align="right" width="42">Cantidad</th>
                    <th align="right" width="42">Saldo</th>
                    <th width="1">&nbsp;</th>
                    <th>Comentarios</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $key => $item)
                    @php
                    $quantity = ($use_boxes) ? $item['quantity'] / $item['packs_per_box'] : $item['quantity'];
                    $balance  = ($item['type'] == 'E') ? $balance + $quantity : $balance - $quantity;

                    switch ($item['type']) {
                        case 'E' : $type = 'Entrada'; break;
                        case 'S' : $type = 'Salida'; break;
                        case 'T' : $type = 'Traspaso'; break;
                        default  : $type = '';
                    }
                    @endphp
                    
                    <tr>
                        <td align="right">{{ $item['movID'] }}</td>
                        <td align="center">{{ substr($item['mov_date'], 0, 10) }}</td>
                        <td align="center" class="cls-small">{{ $type }}</td>
                        <td class="cls-small">{{ $item['name'] }}</td>
                        <td align="right">{{ number_format($quantity, 2) }}</td>
                        @if ($balance >= 0)
                        <td align="right">{{ number_format($balance, 2) }}</td>
                        @else
                        <td align="right" class="cls-negative">{{ number_format($balance, 2) }}</td>
                        @endif
                        <td>&nbsp;</td>
                        <td class="cls-small">{{ $item['comments'] }}</td>
                    </tr>
                @endforeach

                <tr class="cls-total">
                    <td colspan="5" align="right">Existencia:</td>
                    @if ($balance >= 0)
                    <td align="right">{{ number_format($balance, 2) }}</td>
                    @else
                    <td align="right" class="cls-negative">{{ number_format($balance, 2) }}</td>
                    @endif
                    <td colspan="2">{{ ($use_boxes) ? 'Cajas' : 'Paquetes' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</body>
</html>