<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
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
                <div style="font-size: 32px; font-weight: 800; color: #1e293b; letter-spacing: -1px;">MODA</div>
                <div style="font-size: 10px; color: #64748b; margin-top: -5px; text-transform: uppercase; letter-spacing: 2px;">Windows & Doors</div>
            @endif
        </div>
        <div class="header" style="float: right; width: 60%;">
            <h1 style="margin-bottom: 5px;">QUOTATION</h1>
            <p style="color: #64748b;">Premium Window & Door Solutions</p>
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
                    <p style="margin-top: 0;"><span style="color: #64748b;">Quotation No:</span> {{ $quotation->quotation_number }}</p>
                    <p><span style="color: #64748b;">Customer No:</span> {{ $quotation->project->customer->customer_number ?? 'N/A' }}</p>
                    <p><span style="color: #64748b;">Date:</span> {{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') : $quotation->created_at->format('d M Y') }}</p>
                    <p><span style="color: #64748b;">Project:</span> {{ $quotation->project->name ?? 'N/A' }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Schedule of Line Items</div>

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
            @php 
                $totalDiscountVal = 0;
                $grossItemsTotal = 0;
            @endphp
            @foreach($quotation->items as $index => $item)
                @php
                    $itemW = floatval($item->width ?? 1);
                    $itemH = floatval($item->height ?? 1);
                    $area = ($itemW / 1000) * ($itemH / 1000);
                    $qty = floatval($item->quantity ?? 1);
                    
                    // Fetch Original Pricing to calculate the absolute discount
                    $priceData = \App\Models\ProductColorPrice::where('product_id', $item->product_id)
                        ->where('main_color_id', $item->color_id)
                        ->first();
                    $unitPrice = floatval($priceData?->price ?? 0);
                    $glassPrice = floatval($item->glass?->price_per_sqm ?? 0);
                    
                    // Accessories Total
                    $accPrice = 0;
                    if($item->accessories && is_array($item->accessories)) {
                        $accPrice = \App\Models\Accessory::whereIn('id', $item->accessories)->sum('price');
                    }

                    $itemGrossTotal = (($unitPrice + $glassPrice) * max(1.0, $area) * $qty) + ($accPrice * $qty);
                    $grossItemsTotal += $itemGrossTotal;

                    $discVal = $itemGrossTotal * (floatval($item->discount_amount ?? 0) / 100);
                    $totalDiscountVal += $discVal;
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
                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                        <span>{{ $item->material->name ?? '' }} — {{ $item->materialType->name ?? '' }}</span>
                        <div style="margin-top: 6px;">
                            <span style="display: block; margin-bottom: 2px;">
                                <i style="color: #94a3b8;">Finsh:</i> {{ $item->color->name ?? 'N/A' }} | 
                                <i style="color: #94a3b8;">Sub:</i> {{ \App\Models\Color::find($item->sub_color_id)?->name ?? 'N/A' }}
                            </span>
                            <span style="display: block; margin-bottom: 2px;">
                                <i style="color: #94a3b8;">Glass:</i> {{ $item->glass->name ?? 'N/A' }}
                            </span>
                            <div class="specs-badge">
                                {{ number_format($item->width, 0) }}mm (W) x {{ number_format($item->height, 0) }}mm (H)
                            </div>
                        </div>
                    </td>
                    <td class="qty-column">{{ $item->quantity }}</td>
                    <td class="price-column">฿{{ number_format($item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section clearfix">
        <table class="totals-table">
            <tr>
                <td class="label">Goods Subtotal (Gross)</td>
                <td class="value">฿{{ number_format($grossItemsTotal, 2) }}</td>
            </tr>
            @if($totalDiscountVal > 0)
            <tr>
                <td class="label discount">Item Discounts (Sum)</td>
                <td class="value discount">- ฿{{ number_format($totalDiscountVal, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Installation Total</td>
                <td class="value">฿{{ number_format($quotation->installation_total, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Subtotal (Net)</td>
                <td class="value">฿{{ number_format($quotation->total_price, 2) }}</td>
            </tr>
            <tr>
                <td class="label">VAT ({{ \App\Models\Setting::get('vat_percent', 7) }}%)</td>
                <td class="value">฿{{ number_format($quotation->vat_total, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label" style="border-bottom: none;">Grand Total</td>
                <td class="value" style="border-bottom: none;">฿{{ number_format($quotation->final_price, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer generated quotation. Valid for 30 days from issued date.</p>
        <p>© {{ date('Y') }} MODA Windows & Doors | All Rights Reserved</p>
    </div>
</body>

</html>