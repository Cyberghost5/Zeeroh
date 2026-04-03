<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicOrganizerController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TicketTransferController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\RevenueController as AdminRevenueController;
use App\Http\Controllers\Admin\OrganizerController as AdminOrganizerController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Organizer\DashboardController as OrganizerDashboard;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Organizer\AttendeeController as OrganizerAttendeeController;
use App\Http\Controllers\Organizer\ProfileController as OrganizerProfileController;
use App\Http\Controllers\Organizer\PromoCodeController;
use App\Http\Controllers\Organizer\PayoutController as OrganizerPayoutController;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────────────
Route::get('/', [PublicEventController::class, 'home'])->name('home');
Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
Route::get('/events/category/{slug}', [PublicEventController::class, 'category'])->name('events.category');
Route::get('/events/{slug}', [PublicEventController::class, 'show'])->name('events.show');
Route::get('/organizers/{slug}', [PublicOrganizerController::class, 'show'])->name('organizers.show');
Route::get('/search/suggestions', [PublicEventController::class, 'suggestions'])->name('search.suggestions');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Paystack webhook (no auth/CSRF)
Route::post('/webhooks/paystack', [CheckoutController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhooks.paystack');

// Public ticket transfer acceptance (no auth required)
Route::get('/transfer/accept/{token}', [TicketTransferController::class, 'accept'])->name('tickets.transfer.accept');
Route::post('/transfer/confirm/{token}', [TicketTransferController::class, 'confirm'])->name('tickets.transfer.confirm');

// ── Authenticated attendee routes ──────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Attendee dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout
    Route::get('/events/{slug}/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/events/{slug}/checkout', [CheckoutController::class, 'initiate'])->name('checkout.initiate');
    Route::post('/checkout/validate-promo', [CheckoutController::class, 'validatePromo'])->name('checkout.validate-promo');
    Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
    Route::get('/orders/{orderNumber}/success', [CheckoutController::class, 'success'])->name('orders.success');

    // Tickets
    Route::get('/tickets/{ticketCode}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticketCode}/download', [TicketController::class, 'download'])->name('tickets.download');

    // Ticket transfers
    Route::get('/tickets/{ticketCode}/transfer', [TicketTransferController::class, 'create'])->name('tickets.transfer.show');
    Route::post('/tickets/{ticketCode}/transfer', [TicketTransferController::class, 'store'])->name('tickets.transfer.store');
    Route::delete('/tickets/{ticketCode}/transfer', [TicketTransferController::class, 'cancel'])->name('tickets.transfer.cancel');

    // Wishlist (save events)
    Route::post('/wishlist/{event}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Waitlist
    Route::post('/waitlist/join', [WaitlistController::class, 'join'])->name('waitlist.join');
    Route::post('/waitlist/leave', [WaitlistController::class, 'leave'])->name('waitlist.leave');

    // Reviews
    Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Dashboard extra pages
    Route::get('/dashboard/saved', [DashboardController::class, 'saved'])->name('dashboard.saved');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
});

// Organizer routes
Route::middleware(['auth', 'verified', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerDashboard::class, 'index'])->name('dashboard');
    Route::resource('events', OrganizerEventController::class);

    // Attendees & check-in
    Route::get('/events/{event}/attendees', [OrganizerAttendeeController::class, 'index'])->name('events.attendees');
    Route::get('/events/{event}/scanner', [OrganizerAttendeeController::class, 'scanner'])->name('events.scanner');
    Route::post('/check-in', [OrganizerAttendeeController::class, 'checkIn'])->name('check-in');
    // Profile settings
    Route::get('/profile-settings', [OrganizerProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile-settings', [OrganizerProfileController::class, 'update'])->name('profile.update');

    // Promo codes
    Route::get('/promos', [PromoCodeController::class, 'index'])->name('promos.index');
    Route::get('/promos/create', [PromoCodeController::class, 'create'])->name('promos.create');
    Route::post('/promos', [PromoCodeController::class, 'store'])->name('promos.store');
    Route::patch('/promos/{promoCode}/toggle', [PromoCodeController::class, 'toggle'])->name('promos.toggle');
    Route::delete('/promos/{promoCode}', [PromoCodeController::class, 'destroy'])->name('promos.destroy');

    // Payouts
    Route::get('/payouts', [OrganizerPayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts', [OrganizerPayoutController::class, 'store'])->name('payouts.store');
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('events', AdminEventController::class)->only(['index', 'show']);
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject',  [AdminEventController::class, 'reject'])->name('events.reject');
    Route::post('/events/{event}/feature', [AdminEventController::class, 'feature'])->name('events.feature');

    // Revenue & commission
    Route::get('/revenue', [AdminRevenueController::class, 'index'])->name('revenue.index');
    Route::get('/revenue/payouts', [AdminRevenueController::class, 'payouts'])->name('revenue.payouts');
    Route::get('/commission', [AdminRevenueController::class, 'commissionSettings'])->name('commission.edit');
    Route::patch('/commission', [AdminRevenueController::class, 'updateCommission'])->name('commission.update');

    // Payout requests
    Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{payout}/approve', [AdminPayoutController::class, 'approve'])->name('payouts.approve');
    Route::post('/payouts/{payout}/paid', [AdminPayoutController::class, 'markPaid'])->name('payouts.paid');
    Route::post('/payouts/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');
    // Organizer management
    Route::get('/organizers', [AdminOrganizerController::class, 'index'])->name('organizers.index');
    Route::get('/organizers/{organizer}', [AdminOrganizerController::class, 'show'])->name('organizers.show');
    Route::post('/organizers/{organizer}/suspend', [AdminOrganizerController::class, 'suspend'])->name('organizers.suspend');
    Route::post('/organizers/{organizer}/reactivate', [AdminOrganizerController::class, 'reactivate'])->name('organizers.reactivate');
    Route::delete('/organizers/{organizer}', [AdminOrganizerController::class, 'destroy'])->name('organizers.destroy');
    Route::post('/organizers/{organizer}/impersonate', [AdminOrganizerController::class, 'impersonate'])->name('organizers.impersonate');
});

// Stop impersonation — only requires auth (current user is the organizer during impersonation)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/impersonate/stop', [AdminOrganizerController::class, 'stopImpersonation'])->name('impersonate.stop');
});

require __DIR__.'/auth.php';
