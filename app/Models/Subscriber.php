<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\SubscriberCreated;

class Subscriber extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "subscribed",
        "endpoint",
        "hash",
        "expiration",
        "public",
        "auth",
        "encoding",
        "body",
        "variables",
        "created_at",
        "deleted_at",
        "user_id",
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'variables' => 'json',
    ];

    protected $appends = [
        'created',
        'createdHuman',
        'updated',
        'updatedHuman',
        'deleted',
        'deletedHuman',
    ];

    protected $dispatchesEvents = [
        'created' => SubscriberCreated::class,
    ];

    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format("m/d/y h:i a") : null;
    }

    public function getCreatedHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function getUpdatedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format("m/d/y h:i a") : null;
    }

    public function getUpdatedHumanAttribute()
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : null;
    }

    public function getDeletedAttribute()
    {
        return $this->deleted_at ? $this->deleted_at->format("m/d/y h:i a") : null;
    }

    public function getDeletedHumanAttribute()
    {
        return $this->deleted_at ? $this->deleted_at->diffForHumans() : null;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function website()
    {
        return $this->belongsTo('App\Models\Website');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }

    public function event() // last event
    {
        return $this->hasOne('App\Models\Event')->orderBy('id', 'desc');
    }

    public function pushes()
    {
        return $this->hasMany('App\Models\Push');
    }
}
