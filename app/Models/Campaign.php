<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "name",
        "enabled",
        "type",
        "user_id",
    ];

    protected $appends = [
        'created',
        'createdHuman',
        'updated',
        'updatedHuman',
        'campaignColor'
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

    public function getCampaignColorAttribute()
    {
        return $this->type == 'waterfall'
            ? "text-info"
            : ($this->type == 'scheduled'
                ? "text-success"
                : ($this->type == 'reoccurring' ? "text-primary" : null)
            );
    }

    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    public function triggers()
    {
        return $this->hasMany('App\Models\Schedule')->where('is_trigger', 1);
    }

    public function schedulesWithTrashed()
    {
        return $this->hasMany('App\Models\Schedule')->withTrashed();
    }

    public function websites()
    {
        return $this->belongsToMany('App\Models\Website');
    }

    public function pushes()
    {
        return $this->hasMany('App\Models\Push');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function variables()
    {
        return $this->hasMany('App\Models\Variable', 'target_id')->where('scope', 'campaign');
    }
}
