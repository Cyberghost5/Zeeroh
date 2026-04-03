@component('mail::message')

# Your tickets are confirmed! 🎉

Hi {{ $order->user->name }},

Your booking for **{{ $order->event->title }}** is confirmed. Here are your order details:

---

**Event:** {{ $order->event->title }}
**Date:** {{ $order->event->start_date->format('l, F j, Y') }} at {{ $order->event->start_time }}
**Venue:** {{ $order->event->is_virtual ? 'Online / Virtual' : ($order->event->venue_name . ', ' . $order->event->city . ', ' . $order->event->state) }}
**Order #:** {{ $order->order_number }}

---

## Your Tickets

@foreach($order->tickets as $ticket)
- **{{ $ticket->ticketType->name }}** — `{{ $ticket->ticket_code }}`
@endforeach

@component('mail::button', ['url' => route('orders.success', $order->order_number), 'color' => 'primary'])
View & Download Tickets
@endcomponent

You can view and download your PDF tickets from the link above. Please have your QR code ready at the venue entrance.

**Total paid:** {{ $order->total_amount > 0 ? '₦' . number_format($order->total_amount) : 'Free' }}

Thank you for using Zeeroh!

The Zeeroh Team
@endcomponent
