<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    @php
        $documentType = $documentType ?? 'quotation';
        if (! in_array($documentType, ['quotation', 'invoice', 'receipt'], true)) {
            $documentType = 'quotation';
        }
        $docTitle = match ($documentType) {
            'invoice' => 'Invoice',
            'receipt' => 'Receipt',
            default => 'Quotation',
        };
        $docSection = match ($documentType) {
            'invoice' => 'Invoice line items',
            'receipt' => 'Receipt line items',
            default => 'Quotation line items',
        };
        $dateLabel = match ($documentType) {
            'invoice' => 'Invoice date',
            'receipt' => 'Receipt date',
            default => 'Quotation date',
        };
        $docFooter = match ($documentType) {
            'invoice' => 'This is a computer-generated invoice. Please pay according to the agreed terms.',
            'receipt' => 'This is a computer-generated receipt. Thank you for your business.',
            default => 'This is a computer-generated quotation. Valid for 30 days from the issue date.',
        };
        $grandTotalLabel = match ($documentType) {
            'invoice' => 'AMOUNT DUE',
            'receipt' => 'TOTAL RECEIVED',
            default => 'GRAND TOTAL',
        };
        $companyName = \App\Models\Setting::get('company_name', 'MODA');
        $companyTagline = \App\Models\Setting::get('company_tagline', 'Premium Window & Door Solutions');
        $companyLegalSuffix = \App\Models\Setting::get('company_legal_suffix');
        $pdfCustomer = $quotation->customer ?? $quotation->project->customer;
    @endphp
    <title>{{ $docTitle }} — {{ match ($documentType) {
        'invoice' => $quotation->invoice_number,
        'receipt' => $quotation->receipt_number,
        default => $quotation->quotation_number,
    } }}@if($pdfCustomer?->customer_number) ({{ $pdfCustomer->customer_number }})@endif</title>
    <style>
        @page {
            margin: 1cm;
        }

        /* DejaVu Sans ships with DomPDF and includes the Thai Baht sign (฿); Helvetica does not. */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-container {
            border-bottom: 2px solid #1a56db;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #1a56db;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 0;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .info-box {
            padding: 10px;
            background-color: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .info-box h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #1e293b;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 5px;
            text-align: left;
        }

        .items-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .drawing-container {
            width: 60px;
            height: 60px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drawing-container img {
            max-width: 100%;
            max-height: 100%;
        }

        .totals-section {
            margin-top: 20px;
        }

        .totals-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
            /* Keep same face as body; fw:600 can make DomPDF swap to Helvetica-Bold (no ฿ glyph). */
            font-family: 'DejaVu Sans', sans-serif;
        }

        .totals-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-table .label {
            color: #64748b;
            text-align: right;
        }

        .totals-table .value {
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            background-color: #1a56db;
            color: #ffffff !important;
            font-size: 13px;
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: bold;
        }

        .grand-total .label {
            color: #ffffff;
        }

        .clearfix::after {
            content: '';
            display: block;
            clear: both;
        }

        .doc-title {
            margin: 0 0 4px 0;
            font-size: 26px;
            font-weight: 700;
            color: #1a56db;
            line-height: 1.1;
        }

        .doc-ref-block {
            margin-top: 8px;
        }

        .doc-ref-line {
            margin: 3px 0 0 0;
            color: #334155;
            font-size: 10px;
            line-height: 1.35;
        }

        .doc-ref-label {
            display: inline-block;
            min-width: 92px;
            color: #94a3b8;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 16px 0 6px 0;
        }
    </style>
</head>

<body>
    <div class="header-container clearfix">
        <div style="float: left; width: 50%;">
            <div style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ $companyName }}</div>
            <div style="font-size: 9px; color: #64748b; text-transform: uppercase;">{{ $companyTagline }}</div>
        </div>
        <div style="float: right; width: 50%; text-align: right;">
            <div class="doc-title">{{ $docTitle }}</div>
            <div class="doc-ref-block">
                @if($pdfCustomer?->customer_number)
                    <p class="doc-ref-line"><span class="doc-ref-label">Customer No.</span>{{ $pdfCustomer->customer_number }}</p>
                @endif
                @if($documentType === 'invoice')
                    <p class="doc-ref-line"><span class="doc-ref-label">Invoice No.</span>{{ $quotation->invoice_number }}</p>
                @elseif($documentType === 'receipt')
                    <p class="doc-ref-line"><span class="doc-ref-label">Receipt No.</span>{{ $quotation->receipt_number }}</p>
                @else
                    <p class="doc-ref-line"><span class="doc-ref-label">Quotation No.</span>{{ $quotation->quotation_number }}</p>
                @endif
            </div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="info-box">
                    <h3>Customer details</h3>
                    @if($pdfCustomer?->customer_number)
                        <p style="margin:0 0 4px 0;font-size:9px;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;">Customer No. {{ $pdfCustomer->customer_number }}</p>
                    @endif
                    <p style="margin:0;"><strong>{{ $pdfCustomer?->name ?? 'N/A' }}</strong></p>
                    <p>{{ $pdfCustomer?->address ?? '' }}</p>
                    <p>Tel: {{ $pdfCustomer?->mobile ?? 'N/A' }}</p>
                </div>
            </td>
            <td style="padding-left: 20px;">
                <div class="info-box">
                    <h3>Project &amp; status</h3>
                    <p><strong>Project name:</strong> {{ $quotation->project->name ?? 'N/A' }}</p>
                    <p><strong>{{ $dateLabel }}:</strong> {{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') : $quotation->created_at->format('d M Y') }}</p>
                    <p><strong>Order status:</strong> {{ $quotation->status }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">{{ $docSection }}</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="20">No.</th>
                <th width="65">Drawing</th>
                <th>Product Description</th>
                <th width="60">Dimensions</th>
                <th width="30" style="text-align:center;">Qty</th>
                <th width="70" style="text-align:right;">Goods Value</th>
                <th width="70" style="text-align:right;">Install Fee</th>
            </tr>
        </thead>
        <tbody>
            @php $grossSubTotal = 0; $totalDisc = 0; @endphp
            @foreach($quotation->items as $index => $item)
                @php
                    $wVal = floatval($item->width ?? 0);
                    $hVal = floatval($item->height ?? 0);
                    $area = max(1.0, ($wVal / 1000) * ($hVal / 1000));
                    $qty = intval($item->quantity ?? 1);
                    $installFee = floatval($item->installation_rate ?? 0) * $area * $qty;
                    
                    // We need to show the Gross Goods Total before discount for clarity
                    // current $item->price is already discounted (NET)
                    $discPercent = floatval($item->discount_amount ?? 0);
                    $netPrice = floatval($item->price ?? 0);
                    $grossPrice = ($discPercent > 0 && $discPercent < 100) ? ($netPrice / (1 - ($discPercent / 100))) : $netPrice;
                    
                    $grossSubTotal += $grossPrice;
                    $totalDisc += ($grossPrice - $netPrice);
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td>
                        <div class="drawing-container">
                            @php $path = $item->product->drawing_path ?? null; @endphp
                            @if($path && file_exists(public_path('storage/'.$path)))
                                <img src="{{ public_path('storage/'.$path) }}">
                            @else
                                <span style="font-size: 7px; color:#ccc;">NO IMG</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <strong>{{ $item->product->name ?? 'Product' }}</strong><br>
                        <span style="font-size:8.5px; color:#666;">
                            Color: {{ $item->color->name ?? 'N/A' }}<br>
                            Glass: {{ $item->glass->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="font-size: 8.5px;">
                        {{ $wVal }} x {{ $hVal }} mm<br>
                        ({{ number_format($area, 2) }} sqm)
                    </td>
                    <td style="text-align:center;">{{ $qty }}</td>
                    <td style="text-align:right;">฿{{ number_format($netPrice, 2) }}</td>
                    <td style="text-align:right;">฿{{ number_format($installFee, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section clearfix">
        <table class="totals-table">
            <tr>
                <td class="label">Goods Subtotal (Gross)</td>
                <td class="value">฿{{ number_format($grossSubTotal, 2) }}</td>
            </tr>
            @if($totalDisc > 0)
            <tr>
                <td class="label" style="color: #ef4444;">Item Discounts Total (-)</td>
                <td class="value" style="color: #ef4444;">- ฿{{ number_format($totalDisc, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Total Installation Fees</td>
                <td class="value">฿{{ number_format($quotation->installation_total, 2) }}</td>
            </tr>
            <tr style="border-top: 1px solid #333;">
                <td class="label">Subtotal (Net)</td>
                <td class="value">฿{{ number_format($quotation->total_price, 2) }}</td>
            </tr>
            <tr>
                <td class="label">VAT ({{ \App\Models\Setting::get('vat_percent', 7) }}%)</td>
                <td class="value">฿{{ number_format($quotation->vat_total, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label" style="border: none;">{{ $grandTotalLabel }}</td>
                <td class="value" style="border: none;">฿{{ number_format($quotation->final_price, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer" style="position: absolute; bottom: 0; width: 100%; text-align: center; border-top: 1px solid #eee; padding-top: 10px;">
        <p>{{ $docFooter }}</p>
        <p>© {{ date('Y') }} {{ $companyName }}{{ $companyLegalSuffix ? ' | '.$companyLegalSuffix : '' }} | Thank you for your business!</p>
    </div>
</body>
</html>