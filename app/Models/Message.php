<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "title",
        "url",
        "body",
        "button",
        "icon",
        "image",
        "badge",
        "sound",
        "direction",
        "actions",
        "silent",
        "tag",
        "renotify",
        "require_interaction",
        "ttl",
        "user_id",
    ];

    protected $appends = [
        'created',
        'createdHuman',
        'updated',
        'updatedHuman',
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

    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    public function pushes()
    {
        return $this->hasMany('App\Models\Push');
    }

    public function variables()
    {
        return $this->hasMany('App\Models\Variable', 'target_id')->where('scope', 'message');
    }
}
