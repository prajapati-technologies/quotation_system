<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header-container {
            border-bottom: 2px solid #1a56db;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header {
            text-align: right;
        }

        .header h1 {
            font-size: 28px;
            margin: 0;
            color: #1a56db;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 2px 0 0;
            color: #666;
            font-size: 11px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 0;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .info-box {
            padding: 10px;
            background-color: #f8fafc;
            border-radius: 8px;
            min-height: 100px;
        }

        .info-box h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .info-box p {
            margin: 3px 0;
            line-height: 1.4;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin: 25px 0 10px 0;
            padding-left: 5px;
            border-left: 4px solid #1a56db;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .items-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 8px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .item-details strong {
            display: block;
            font-size: 12px;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .item-details span {
            color: #64748b;
            font-size: 10px;
        }

        .drawing-container {
            width: 80px;
            height: 80px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .drawing-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .specs-badge {
            display: inline-block;
            background-color: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            color: #475569;
            font-size: 10px;
            margin-top: 4px;
        }

        .price-column {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
            white-space: nowrap;
        }

        .qty-column {
            text-align: center;
            color: #475569;
        }

        .totals-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .totals-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }

        .totals-table .label {
            color: #64748b;
            text-align: right;
        }

        .totals-table .value {
            text-align: right;
            font-weight: 600;
            width: 100px;
        }

        .totals-table .discount {
            color: #ef4444;
        }

        .totals-table .grand-total {
            background-color: #1a56db;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 800;
            border-radius: 0 0 8px 8px;
        }

        .totals-table .grand-total .label {
            color: #ffffff;
            opacity: 0.9;
        }

        .notes-box {
            margin-top: 40px;
            padding: 15px;
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 8px;
            width: 60%;
        }

        .notes-box h4 {
            margin: 0 0 5px 0;
            color: #92400e;
            font-size: 11px;
            text-transform: uppercase;
        }

        .notes-box p {
            margin: 0;
            color: #b45309;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 9px;
        }

        .clearfix::after {
            content: '';
            display: block;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header-container clearfix">
        <div style="float: left; width: 40%;">
            @php
                $logoPath = public_path('images/logo.png');
            @endphp
            @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" style="max-height: 80px; max-width: 100%; object-fit: contain;">
            @else
                <div style="font-size: 24px; font-weight: 800; color: #1e293b;">MODA</div>
            @endif
        </div>
        <div class="header" style="float: right; width: 60%;">
            <h1>QUOTATION</h1>
            <p>Expert Window &amp; Door Solutions</p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td style="padding-right: 15px;">
                <div class="info-box">
                    <h3>Customer Details</h3>
                    <p><strong>{{ $quotation->project->customer->name ?? 'N/A' }}</strong></p>
                    <p>{{ $quotation->project->customer->address ?? '' }}</p>
                    @if($quotation->project->customer->mobile)
                        <p>Tel: {{ $quotation->project->customer->mobile }}</p>
                    @endif
                </div>
            </td>
            <td>
                <div class="info-box">
                    <h3>Quotation Info</h3>
                    <p style="margin-top: 0;"><span style="color: #64748b;">Quotation No:</span> #{{ sprintf('%05d', $quotation->id) }}</p>
                    <p><span style="color: #64748b;">Date:</span> {{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') : $quotation->created_at->format('d M Y') }}</p>
                    <p><span style="color: #64748b;">Project:</span> {{ $quotation->project->name ?? 'N/A' }}</p>
                    <p><span style="color: #64748b;">Status:</span> {{ $quotation->status ?? 'Draft' }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Schedule of Line Items</div>

    @php
        $goodsGrossTotal = 0;
        $totalDiscountSum = 0;
        $installationTotal = 0;

        foreach ($quotation->items as $item) {
            $itemW = floatval($item->width ?? 1);
            $itemH = floatval($item->height ?? 1);
            $areaSqm = ($itemW / 1000) * ($itemH / 1000);
            $itemQty = floatval($item->quantity ?? 1);
            
            $discountInput = $item->discount ?? '0';
            $itemDiscountTotal = 0;
            $itemPrice = floatval($item->price ?? 0);

            if (is_string($discountInput) && strpos($discountInput, '%') !== false) {
                $percentage = floatval(str_replace('%', '', $discountInput));
                if ($percentage < 100) {
                    // Logic: BaseAmount = itemPrice / (1 - percentage)
                    // DiscountTotal = BaseAmount * percentage
                    $itemDiscountTotal = ($itemPrice / (1 - ($percentage / 100))) * ($percentage / 100);
                } else {
                    // 100% or more discount means the base total is ambiguous if price is 0,
                    // but we can assume price is 0 and base total was whatever it was.
                    // However, we don't store BaseAmount. This is a limitation of not storing the formula.
                    $itemDiscountTotal = 0; 
                }
            } else {
                $discountRate = floatval($discountInput);
                $itemDiscountTotal = ($discountRate * $areaSqm) * $itemQty;
            }
            $totalDiscountSum += $itemDiscountTotal;

            $itemPrice = floatval($item->price ?? 0);
            $goodsGrossTotal += ($itemPrice + $itemDiscountTotal);

            $installRate = floatval($item->installation_cost ?? 0);
            $installationTotal += ($installRate * $areaSqm) * $itemQty;
        }

        $vatPercent = floatval($quotation->vat_percent ?? 0);
        $vatAmount = floatval($quotation->vat_amount ?? 0);
        $finalPrice = floatval($quotation->final_price ?? 0);
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th width="30">Item</th>
                <th width="85" style="text-align:center;">Drawing</th>
                <th>Description &amp; Specifications</th>
                <th width="40" style="text-align:center;">Qty</th>
                <th width="100" style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
                @php
                    $maxW = 75; $maxH = 75;
                @endphp
                <tr>
                    <td style="text-align:center; color: #94a3b8; font-weight: bold;">{{ sprintf('%02d', $index + 1) }}</td>
                    
                    <td style="text-align:center;">
                        <div class="drawing-container">
                            @php
                                $imgPath = $item->product->drawing_path ?? null;
                                $fullPath = $imgPath ? public_path('storage/' . $imgPath) : null;
                            @endphp
                            @if($fullPath && file_exists($fullPath))
                                <img src="{{ $fullPath }}">
                            @else
                                <div style="font-size: 8px; color:#cbd5e1; padding: 10px;">NO IMAGE</div>
                            @endif
                        </div>
                    </td>

                    <td class="item-details">
                        <strong>{{ $item->product->name ?? ($item->product_type ?? 'N/A') }}</strong>
                        <span>
                            {{ $item->brand->name ?? '' }} Series — {{ $item->materialType->name ?? '' }}
                        </span>
                        <div style="margin-top: 6px;">
                            <span style="display: block; margin-bottom: 2px;">
                                <i style="color: #94a3b8;">Color:</i> {{ $item->color->name ?? 'N/A' }} | 
                                <i style="color: #94a3b8;">Glass:</i> {{ $item->glass->name ?? 'N/A' }}
                            </span>
                            <div class="specs-badge">
                                {{ number_format($item->width, 0) }}mm (W) × {{ number_format($item->height, 0) }}mm (H)
                            </div>
                        </div>
                    </td>

                    <td class="qty-column">
                        {{ $item->quantity }}
                    </td>

                    <td class="price-column">
                        ฿{{ number_format($item->price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section clearfix">
        <table class="totals-table">
            <tr>
                <td class="label">Product Subtotal</td>
                <td class="value">฿{{ number_format($goodsGrossTotal, 2) }}</td>
            </tr>
            @if($totalDiscountSum > 0)
            <tr>
                <td class="label discount">Discount</td>
                <td class="value discount">- ฿{{ number_format($totalDiscountSum, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Installation</td>
                <td class="value">฿{{ number_format($installationTotal, 2) }}</td>
            </tr>
            <tr>
                <td class="label">VAT ({{ number_format($vatPercent, 1) }}%)</td>
                <td class="value">฿{{ number_format($vatAmount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label" style="border-bottom: none;">Grand Total</td>
                <td class="value" style="border-bottom: none;">฿{{ number_format($finalPrice, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($quotation->notes)
        <div class="notes-box">
            <h4>Important Notes / Remarks:</h4>
            <p>{{ $quotation->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>This is a computer generated quotation. Valid for 30 days from issued date.</p>
        <p>© {{ date('Y') }} Expert Window &amp; Door Solutions | All Rights Reserved</p>
    </div>
</body>

</html>
</body>

</html>