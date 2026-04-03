<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
        }

        .ticket {
            width: 100%;
            height: 100%;
            display: flex;
            border: 2px solid #1d4ed8;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Left colored strip */
        .ticket-strip {
            width: 12px;
            background: linear-gradient(180deg, #1d4ed8, #3b82f6);
            flex-shrink: 0;
        }

        /* Main content */
        .ticket-body {
            flex: 1;
            display: flex;
            padding: 20px;
            gap: 20px;
        }

        .ticket-info {
            flex: 1;
        }

        .brand {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1d4ed8;
            margin-bottom: 8px;
        }

        .event-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .ticket-type {
            font-size: 12px;
            font-weight: bold;
            color: #1d4ed8;
            background: #eff6ff;
            display: inline-block;
            padding: 2px 10px;
            border-radius: 100px;
            margin-bottom: 14px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .meta-item label {
            display: block;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .meta-item span {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        .divider {
            border-top: 1.5px dashed #cbd5e1;
            margin: 12px 0;
        }

        .code-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ticket-code {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1d4ed8;
        }

        .order-no {
            font-size: 9px;
            color: #94a3b8;
        }

        /* QR side */
        .ticket-qr {
            width: 130px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-left: 1.5px dashed #cbd5e1;
            padding-left: 20px;
        }

        .ticket-qr img {
            width: 100px;
            height: 100px;
        }

        .ticket-qr p {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 6px;
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 2px 8px;
            border-radius: 100px;
            background: #dcfce7;
            color: #166534;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="ticket">
    <div class="ticket-strip"></div>
    <div class="ticket-body">
        <div class="ticket-info">
            <p class="brand">Zeeroh · E-Ticket</p>
            <h1 class="event-title">{{ $ticket->event->title }}</h1>
            <span class="ticket-type">{{ $ticket->ticketType->name }}</span>

            <div class="meta-grid">
                <div class="meta-item">
                    <label>Date</label>
                    <span>{{ $ticket->event->start_date->format('D, M j, Y') }}</span>
                </div>
                <div class="meta-item">
                    <label>Time</label>
                    <span>{{ $ticket->event->start_time }}</span>
                </div>
                <div class="meta-item">
                    <label>Venue</label>
                    <span>{{ $ticket->event->is_virtual ? 'Online' : $ticket->event->venue_name }}</span>
                </div>
                <div class="meta-item">
                    <label>Status</label>
                    <span class="status-badge">{{ ucfirst($ticket->status) }}</span>
                </div>
                <div class="meta-item">
                    <label>Holder</label>
                    <span>{{ $ticket->holder_name }}</span>
                </div>
                <div class="meta-item">
                    <label>Email</label>
                    <span>{{ $ticket->holder_email }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <div class="code-row">
                <div>
                    <p class="order-no">Order: {{ $ticket->order->order_number }}</p>
                    <p class="ticket-code">{{ $ticket->ticket_code }}</p>
                </div>
            </div>
        </div>

        <div class="ticket-qr">
            @if($qrBase64)
                <img src="{{ $qrBase64 }}" alt="QR Code">
            @endif
            <p>Scan at the door</p>
        </div>
    </div>
</div>
</body>
</html>
