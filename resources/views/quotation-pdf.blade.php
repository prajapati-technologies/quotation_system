<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 22px;
            margin: 0;
            letter-spacing: 2px;
        }

        .header p {
            margin: 4px 0 0;
            color: #555;
            font-size: 12px;
        }

        .info {
            width: 100%;
            margin-bottom: 18px;
        }

        .info td {
            vertical-align: top;
            padding: 4px 8px;
            font-size: 12px;
        }

        .section-title {
            background-color: #1a56db;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 20px;
            margin-bottom: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .table th {
            background-color: #e8f0fe;
            border: 1px solid #c5cfe8;
            padding: 7px 6px;
            text-align: left;
            font-size: 11px;
            color: #1a3a6b;
        }

        .table td {
            border: 1px solid #dde3f0;
            padding: 7px 6px;
            vertical-align: top;
            font-size: 11px;
        }

        .table tr:nth-child(even) td {
            background-color: #f7f9ff;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-special { background: #fde68a; color: #78350f; }
        .badge-normal  { background: #d1fae5; color: #065f46; }

        .total-box {
            margin-top: 24px;
            text-align: right;
        }

        .total-box table {
            width: 260px;
            float: right;
            border-collapse: collapse;
        }

        .total-box table td {
            padding: 5px 10px;
            border: 1px solid #dde3f0;
            font-size: 12px;
        }

        .total-box table tr:last-child td {
            font-weight: bold;
            font-size: 14px;
            background: #e8f0fe;
            color: #1a3a6b;
        }

        .clearfix::after { content: ''; display: block; clear: both; }

        .signature-box {
            margin-top: 50px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="header">
        <h1>QUOTATION</h1>
        <p>Expert Window &amp; Door Solutions</p>
    </div>

    <!-- INFO TABLE -->
    <table class="info">
        <tr>
            <td width="50%">
                <strong>Customer:</strong><br>
                {{ $quotation->project->customer->name ?? 'N/A' }}<br>
                {{ $quotation->project->customer->address ?? '' }}<br>
                @if($quotation->project->customer->mobile)
                    Mobile: {{ $quotation->project->customer->mobile }}
                @endif
            </td>
            <td>
                <strong>Quotation Details:</strong><br>
                Quote #: {{ $quotation->id }}<br>
                Date: {{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') : $quotation->created_at->format('d M Y') }}<br>
                Project: {{ $quotation->project->name ?? 'N/A' }}<br>
                Status: {{ $quotation->status ?? 'Draft' }}<br>
                @if($quotation->project->expected_delivery_date)
                    Delivery: {{ $quotation->project->expected_delivery_date }}
                @endif
            </td>
        </tr>
    </table>

    <!-- LINE ITEMS -->
    @php
        // Calculate total area and discount total for summary
        $totalArea = 0;
        $installationTotal = 0;

        foreach ($quotation->items as $areaItem) {
            $w = floatval($areaItem->width ?? 0);
            $h = floatval($areaItem->height ?? 0);
            $calculatedArea = ($w / 1000) * ($h / 1000);
            $areaSqm = max($calculatedArea, 1.0); // area per piece in Sqm (min 1)

            $qty = floatval($areaItem->quantity ?? 1);
            $totalArea += $areaSqm * $qty; // total Sqm = area per piece × quantity

            // Installation total = per Sqm install fee × total Sqm
            $installPerSqm = floatval($areaItem->installation_cost ?? 0);
            $installationTotal += $installPerSqm * $areaSqm * $qty;
        }

        $discountPerSqm = floatval($quotation->discount ?? 0);
        $discountTotal = $discountPerSqm * $totalArea;

        // Total after discount but before VAT
        $preVatTotal = max(0, floatval($quotation->total_price ?? 0) - $discountTotal);
    @endphp

    <div class="section-title">Line Items</div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Drawing</th>
                <th>Material / Brand / Type</th>
                <th>Specs (W × H mm)</th>
                <th>Color / Glass</th>
                <th>Class</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
                @php
                    $maxW = 70; $maxH = 70;
                    $scale = min($maxW / max($item->width, 1), $maxH / max($item->height, 1));
                    $w = $item->width * $scale;
                    $h = $item->height * $scale;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>

                    {{-- Product Name --}}
                    <td>
                        <strong>{{ $item->product->name ?? ($item->product_type ?? 'N/A') }}</strong>
                    </td>

                    {{-- Drawing / Image --}}
                    <td style="text-align:center;">
                        @if($item->product && $item->product->drawing_path)
                            <img src="{{ public_path('storage/' . $item->product->drawing_path) }}"
                                 width="{{ $maxW }}" height="{{ $maxH }}"
                                 style="object-fit:contain; border:1px solid #ddd;">
                        @else
                            <svg width="{{ $maxW }}" height="{{ $maxH }}">
                                <rect x="{{ ($maxW - $w) / 2 }}" y="{{ ($maxH - $h) / 2 }}"
                                      width="{{ $w }}" height="{{ $h }}"
                                      fill="none" stroke="#333" stroke-width="2" />
                                @if(($item->product->name ?? $item->product_type ?? '') === 'Window')
                                    <line x1="{{ $maxW / 2 }}" y1="{{ ($maxH - $h) / 2 }}"
                                          x2="{{ $maxW / 2 }}" y2="{{ ($maxH + $h) / 2 }}"
                                          stroke="#333" stroke-width="1" />
                                @endif
                            </svg>
                        @endif
                    </td>

                    {{-- Material / Brand / Type --}}
                    <td>
                        {{ $item->material->name ?? 'N/A' }}<br>
                        <small style="color:#555;">
                            Brand: {{ $item->brand->name ?? 'N/A' }}<br>
                            Type: {{ $item->materialType->name ?? 'N/A' }}
                        </small>
                    </td>

                    {{-- Specs --}}
                    <td style="white-space:nowrap;">
                        {{ number_format($item->width, 0) }} × {{ number_format($item->height, 0) }}
                    </td>

                    {{-- Color / Glass / Film --}}
                    <td>
                        <small>
                            Color: {{ $item->color->name ?? 'N/A' }}<br>
                            Glass: {{ $item->glass->name ?? 'N/A' }}<br>
                            Film: {{ $item->glassFilm->name ?? 'N/A' }}
                        </small>
                    </td>

                    {{-- Classification --}}
                    <td>
                        @if(($item->classification ?? '') === 'SPECIAL')
                            <span class="badge badge-special">SPECIAL</span>
                        @else
                            <span class="badge badge-normal">NORMAL</span>
                        @endif
                    </td>

                    {{-- Qty --}}
                    <td style="text-align:center;">{{ $item->quantity }}</td>

                    {{-- Price --}}
                    <td style="text-align:right; white-space:nowrap;">
                        ฿{{ number_format($item->price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="total-box clearfix">
        <table>
            <tr>
                <td>Total</td>
                <td style="text-align:right;">฿{{ number_format($quotation->total_price, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td style="text-align:right;">- ฿{{ number_format($discountTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Install Price</td>
                <td style="text-align:right;">฿{{ number_format($installationTotal, 2) }}</td>
            </tr>
            <tr>
                <td>VAT ({{ number_format($quotation->vat_percent ?? 0, 2) }}%)</td>
                <td style="text-align:right;">฿{{ number_format($quotation->vat_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Grand Total</strong></td>
                <td style="text-align:right;"><strong>฿{{ number_format($quotation->final_price, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Generated on {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp; Expert Window &amp; Door Solutions
    </div>
</body>

</html>