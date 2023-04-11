<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'details',
        'website_limit',
        'message_limit',
        'subscriber_limit',
        'push_limit',
        'push_limit_timeframe',
        'cost',
        'can_renew',
        'available',
        'color',
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
