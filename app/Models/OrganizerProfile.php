<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerProfile extends Model
{
    protected $fillable = [
        'user_id', 'organization_name', 'slug', 'bio', 'logo',
        'website', 'facebook', 'twitter', 'instagram', 'is_verified',
        'bank_name', 'account_number', 'account_name',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
