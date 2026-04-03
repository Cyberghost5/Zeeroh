<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'organizer_id', 'category_id', 'title', 'slug', 'description', 'banner',
        'venue_name', 'venue_address', 'city', 'state', 'is_virtual', 'virtual_link',
        'start_date', 'end_date', 'start_time', 'end_time',
        'status', 'rejection_reason', 'is_featured', 'is_promoted', 'views_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_virtual' => 'boolean',
        'is_featured' => 'boolean',
        'is_promoted' => 'boolean',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function featuredEvent()
    {
        return $this->hasOne(FeaturedEvent::class);
    }

    public function reviews()
    {
        return $this->hasMany(EventReview::class);
    }

    public function waitlist()
    {
        return $this->hasMany(WaitlistEntry::class);
    }

    public function getMinPriceAttribute()
    {
        return $this->ticketTypes()->where('is_active', true)->min('price');
    }

    public function getTotalTicketsSoldAttribute()
    {
        return $this->ticketTypes()->sum('quantity_sold');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title) . '-' . Str::random(6);
            }
        });
    }
}
