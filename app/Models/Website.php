<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Website extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "uuid",
        "name",
        "domain",
        "icon",
        "image",
        "dedupe_subscribers",
        "webhook_url",
        "webhook_method",
        "webhook_event_types",
        "vapid_public",
        "vapid_private",
        "dialog_id",
        // "user_id",
    ];

    protected $hidden = [
        'id',
        'user_id',
    ];

    protected $appends = [
        'created',
        'createdHuman',
        'updated',
        'updatedHuman',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->uuid = (string)\Uuid::generate(4);
        });
    }

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

    public function getWebhookEventTypesAttribute($value)
    {
        if (strlen($value) == 0) {
            return [];
        }
        return array_map("intval", explode(",", $value));
    }

    // TODO: move WebhookController::validateTypes() here
    public function setWebhookEventTypesAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['webhook_event_types'] = "";
        } else {
            $this->attributes['webhook_event_types'] = join(",", array_map("intval", $value));
        }
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User'); // ->withTrashed();
    }

    public function subscribers()
    {
        return $this->hasMany('App\Models\Subscriber')->withTrashed();
    }

    public function subs()
    {
        return $this->hasMany('App\Models\Subscriber');
    }

    public function unsubs()
    {
        return $this->hasMany('App\Models\Subscriber')->onlyTrashed();
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }

    public function pushes()
    {
        return $this->hasMany('App\Models\Push');
    }

    public function webhooks()
    {
        return $this->hasMany('App\Models\Webhook');
    }

    public function dialog()
    {
        return $this->belongsTo('App\Models\Dialog');
    }

    public function campaigns()
    {
        return $this->belongsToMany('App\Models\Campaign');
    }

    public function schedules()
    {
        return $this->belongsToMany('App\Models\Schedule');
    }

    public function variables()
    {
        return $this->hasMany('App\Models\Variable', 'target_id')->where('scope', 'website');
    }
}
