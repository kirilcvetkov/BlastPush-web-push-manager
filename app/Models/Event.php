<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\EventCreated;

class Event extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    protected $fillable = [
        'type_id',
        'type',
        'url',
        'location',
        'ip',
        'country',
        'state',
        'city',
        'postal',
        'timezone',
        'user_agent',
        'device',
        'device_version',
        'platform',
        'platform_version',
        'browser',
        'browser_version',
        'browser_version_short',
        'is_mobile',
        'is_tablet',
        'is_desktop',
        'is_robot',
        'referer',
        'body',
        'uuid',
        'website_id',
        'user_id',
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $appends = [
        'created',
        'createdHuman',
    ];

    protected $dispatchesEvents = [
        'created' => EventCreated::class,
    ];

    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format("m/d/y h:i a") : null;
    }

    public function getCreatedHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber')->withTrashed();
    }

    public function website()
    {
        return $this->belongsTo('App\Models\Website');
    }

    public function webhook()
    {
        return $this->hasOne('App\Models\Webhook');
    }
}

