<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Existencia por Vendedor</title>
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
            /*font-weight: bold;*/
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
    <h3>Reporte de Existencias por Vendedor</h3>
    <h5>
        <div>Fecha: {{ date('d/m/Y') }} | Hora: {{ date('h:i a') }}</div>
    </h5>
    <hr>

    <div style="text-align: center; margin: 0px 100px;">
        <table class="cls-content" cellspacing="0" cellpadding="4" width="100%">
            <thead>
                <tr>
                    <th align="center">Vendedor</th>
                    <th align="center" width="50">Cajas</th>
                    <th align="center" width="50">Paquetes</th>
                    <th align="center" width="70">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $item)
                    @php
                    $sum_boxes  += $item['boxes'];
                    $sum_packs  += $item['packs'];
                    $sum_amount += $item['amount'];
                    @endphp
                    
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        
                        @if ($item['packs'] >= 0)
                        <td align="right">{{ number_format($item['boxes'], 1) }}</td>
                        <td align="right">{{ number_format($item['packs'], 0) }}</td>
                        <td align="right">$ {{ number_format($item['amount'], 2) }}</td>
                        @else
                        <td align="right" class="cls-negative">{{ number_format($item['boxes'], 1) }}</td>
                        <td align="right" class="cls-negative">{{ number_format($item['packs'], 0) }}</td>
                        <td align="right" class="cls-negative">$ {{ number_format($item['amount'], 2) }}</td>
                        @endif
                    </tr>
                @endforeach

                <tr class="cls-total">
                    <td>Total</td>

                    @if ($sum_packs >= 0)
                    <td align="right">{{ number_format($sum_boxes, 1) }}</td>
                    <td align="right">{{ number_format($sum_packs, 0) }}</td>
                    <td align="right">$ {{ number_format($sum_amount, 2) }}</td>
                    @else
                    <td align="right" class="cls-negative">{{ number_format($sum_boxes, 1) }}</td>
                    <td align="right" class="cls-negative">{{ number_format($sum_packs, 0) }}</td>
                    <td align="right" class="cls-negative">$ {{ number_format($sum_amount, 2) }}</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</body>
</body>
</html>