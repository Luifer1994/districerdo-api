<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        Factura-{{ $data['code'] }}
    </title>

    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }
    </style>

</head>

<body>
    @php
        $colors = [
            'Pagada' => '#61F065', // Verde para pagado
            'Pendiente' => '#FFBA42', // Naranja para pendiente
            'Cancelada' => '#FF5E42', // Rojo para vencido
            'default' => 'rgb(153, 153, 153)', // Gris para otros estados
        ];
    @endphp

    <table width="100%">
        <tr>
            <td valign="top">
                <img src="{{ public_path('images/logo-login.png') }}" alt="" width="200" />
            </td>
            <td align="right">
                <pre>
                    Distribución y venta de cerdo y sus partes.
                </pre>
                <h3>
                    NIT. 1102872831-6
                    <br>
                    Contacto: 3002729614 - 3244732691
                    <br>
                    Dirección: Carrera 21. Corozal - Sucre
                </h3>

            </td>
        </tr>
    </table>
    <table width="100%">
        <tr style="border: 1px">
            <td valign="top">
                <h3>
                    FECHA: {{ $data['created_at'] }}
                </h3>
            </td>
            <td align="right">
                <h3>
                    FACTURA DE VENTA N° <span
                        style="background-color: rgb(153, 153, 153); padding:2px; border-radius:4%">
                        {{ $data['code'] }}
                    </span>
                </h3>
                <h3>
                    FACTURA
                    <span
                        style="background-color: {{ $colors[$data['state']] ?? $colors['default'] }}; padding:2px; border-radius:4%">
                        {{ $data['state'] }}
                    </span>
                </h3>
            </td>
        </tr>
    </table>
    <p>

    </p>
    <table width="100%">
        <tr>
            <td>
                <strong>Cliente:</strong>
                {{ $data['client']['full_name'] }}
                <br>
                <strong>Documento:</strong>
                {{ $data['client']['document_number'] }}
                <br>
                <strong>Dirección:</strong>
                {{ $data['client']['address'] }}
                <br>
                <strong>Teléfono:</strong>
                {{ $data['client']['phone'] }}
            </td>
        </tr>

    </table>

    <br />

    <table width="100%">
        <thead style="background-color: lightgray;">
            <tr>
                <th>#</th>
                <th align="left">PRODUCTO</th>
                <th align="left">LOTE</th>
                <th align="right">KILOS</th>
                <th align="right">PRECIO_UNITARIO</th>
                <th align="right">TOTAL_PRODUCTO</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data['invoice_lines'] as $index => $product)
                <tr>
                    <th scope="row">
                        {{ $index + 1 }}
                    </th>
                    <td>
                        {{ $product['product']['sku'] }} - {{ $product['product']['name'] }}
                    </td>
                    <td>
                        {{ $product['batch']['code'] }}
                    </td>
                    <td align="right">
                        {{ $product['quantity'] }}
                    </td>
                    <td align="right">
                        $ {{ number_format($product['price'], 0, ',', '.') }}
                    </td>
                    <td align="right">
                        $ {{ number_format($product['price'] * $product['quantity'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

        </tbody>

        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td align="right">TOTAL FACTURA</td>
                <td align="right" class="gray">$
                    {{ number_format($data['total'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td align="right">TOTAL PAGADO</td>
                <td align="right" class="gray">
                 - $ {{ number_format($data['total_paid'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td align="right">PENDIENTE POR PAGAR</td>
                <td align="right" class="gray">$
                    {{ number_format($data['total_for_pay'], 0, ',', '.') }}
                </td>
            </tr>

        </tfoot>
    </table>

</body>

</html>
