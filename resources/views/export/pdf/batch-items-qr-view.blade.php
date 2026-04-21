<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 10mm;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 10px;
        }

        .sheet {
            width: 100%;
        }

        table.sticker-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.sticker-grid td {
            width: 25%;
            padding: 8px 6px 12px;
            vertical-align: top;
            text-align: center;
        }

        .sticker {
            width: 100%;
            min-height: 120px;
        }

        .qr-image {
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto 6px;
        }

        .item-code {
            font-size: 9px;
            line-height: 1.25;
            font-weight: 600;
            letter-spacing: 0.2px;
            word-break: break-word;
            white-space: normal;
        }

        .empty {
            height: 120px;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <table class="sticker-grid">
            <tbody>
                @forelse($items->values()->chunk(4) as $row)
                    <tr>
                        @foreach($row as $item)
                            <td>
                                <div class="sticker">
                                    <img
                                        class="qr-image"
                                        src="data:image/svg+xml;base64,{{ base64_encode((string) \Akira\QrCode\Facades\QrCode::size(300)->generate($item->code)) }}"
                                        alt="QR Code"
                                    >
                                    <div class="item-code">{{ $item->code }}</div>
                                </div>
                            </td>
                        @endforeach

                        @for($i = $row->count(); $i < 4; $i++)
                            <td><div class="empty"></div></td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 24px 0;">No batch items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
