<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = PayoutRequest::with('organizer.organizerProfile')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20);

        $stats = [
            'pending'  => PayoutRequest::where('status', 'pending')->sum('amount'),
            'approved' => PayoutRequest::where('status', 'approved')->sum('amount'),
            'paid'     => PayoutRequest::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.payouts.index', compact('requests', 'stats'));
    }

    public function approve(PayoutRequest $payout)
    {
        abort_unless($payout->status === 'pending', 422, 'Only pending requests can be approved.');

        $payout->update(['status' => 'approved']);

        return back()->with('success', 'Payout request approved.');
    }

    public function markPaid(PayoutRequest $payout)
    {
        abort_unless(in_array($payout->status, ['pending', 'approved']), 422, 'Cannot mark as paid.');

        $payout->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Payout marked as paid.');
    }

    public function reject(Request $request, PayoutRequest $payout)
    {
        abort_unless($payout->status === 'pending', 422, 'Only pending requests can be rejected.');

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ]);

        $payout->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Payout request rejected.');
    }
}
