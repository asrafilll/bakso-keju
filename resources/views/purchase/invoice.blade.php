<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $purchase->purchase_number }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1px;
        }
    </style>
</head>

<body>
    <p style="font-size: 14px; margin: 0; text-align: center; font-weight: bold; padding: 4px 8px;">BAKSO KEJU GREENVILLE
    </p>
    <div style="font-size: 10px; margin: 0; text-align: center; padding: 4px 8px;">
        <p>Premium Homemade Halal</p>
        <p>Bakso Sapi</p>
        <p>0813 1582 7396</p>
    </div>
    <table style="font-size: 8px; padding: 4px 8px; text-align: right;">
        <tr>
            <td>{{ __('Date') }}</td>
            <td width="3px">:</td>
            <td>{{ $purchase->created_at->format('d-M-y') }}</td>
        </tr>
        <tr>
            <td>{{ __('Name') }}</td>
            <td>:</td>
            <td style="background-color: #fee795;">{{ $purchase->customer_name }}</td>
        </tr>
        <tr>
            <td>{{ __('Order number') }}</td>
            <td>:</td>
            <td>{{ $purchase->purchase_number }}</td>
        </tr>
        <tr>
            <td>{{ __('Total (Qty)') }}</td>
            <td>:</td>
            <td>{{ $purchase->idr_total_line_items_quantity }}</td>
        </tr>
        <tr>
            <td>{{ __('Total (IDR)') }}</td>
            <td>:</td>
            <td>{{ $purchase->idr_total_price }}</td>
        </tr>
    </table>
    <table style="font-size: 8px; padding: 4px 8px;">
        <tr style="background-color: #fee795;">
            <td
                width="20px"
                style="text-align: center;"
            >No</td>
            <td style="text-align: center;">Item</td>
        </tr>
        @foreach ($purchase->purchaseLineItems as $purchaseLineItem)
            <tr>
                <td style="text-align: center; vertical-align: top;">{{ $loop->iteration }}</td>
                <td>
                    <table>
                        <tr>
                            <td colspan="2">{{ $purchaseLineItem->item_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Price') }}</td>
                            <td style="text-align: right;">{{ $purchaseLineItem->idr_price }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Qty') }}</td>
                            <td style="text-align: right;">{{ $purchaseLineItem->idr_quantity }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Total') }}</td>
                            <td style="text-align: right;">{{ $purchaseLineItem->idr_total }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    </table>
</body>

</html>
