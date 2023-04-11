<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "order",
        "delay",
        "scheduled_at",
        "reoccurring_frequency",
        "hour_minute",
        "day",
        "is_trigger",
        "trigger_schedule_id",
        "message_id",
        "campaign_id",
        "user_id",
    ];

    protected $dates = [
        "scheduled_at",
    ];

    protected $casts = [
        'hour_minute' => 'datetime:H:i',
    ];

    protected $appends = [
        'scheduled',
        'scheduledHuman',
        'reoccurringFrequencyColor',
        'dayLabel',
        'hourMinuteLabel',
    ];

    public function getScheduledAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->format("m/d/y h:i a") : null;
    }

    public function getScheduledHumanAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->diffForHumans() : null;
    }

    public function campaign()
    {
        return $this->belongsTo('App\Models\Campaign');
    }

    public function message()
    {
        return $this->belongsTo('App\Models\Message');
    }

    public function pushes()
    {
        return $this->hasMany('App\Models\Push');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function variables()
    {
        return $this->hasMany('App\Models\Variable', 'target_id')->where('scope', 'schedule');
    }

    public function hasTrigger()
    {
        if (! is_null($this->is_trigger) || ! is_int($this->trigger_schedule_id)) {
            return false;
        }

        return true;
    }

    public function trigger()
    {
        if (! $this->hasTrigger()) {
            return null;
        }

        return $this->where('id', $this->trigger_schedule_id)->where('is_trigger', 1);
    }

    public function triggerOrigin()
    {
        if (! $this->is_trigger) {
            return null;
        }

        return $this->where('trigger_schedule_id', $this->id)->whereNull('is_trigger')->withTrashed();
    }

    public function setHourMinuteAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['hour_minute'] = $value;
        } elseif ($this->reoccurring_frequency == "hourly") {
            $this->attributes['hour_minute'] = "00:" . sprintf("%02d", $value) . ":00";
        } else {
            $this->attributes['hour_minute'] = date("H:i:s", strtotime($value));
        }
    }

    public function getHourMinuteLabelAttribute()
    {
        if (! isset($this->hour_minute) || is_null($this->hour_minute) || ! $this->hour_minute instanceof Carbon) {
            return $this->hour_minute ?? null;
        }

        if ($this->reoccurring_frequency == "hourly") {
            return ":" . sprintf("%02d", $this->hour_minute->minute) . " minute";
        } else {
            return $this->hour_minute->format('g:i A');
        }
    }

    public function getDayLabelAttribute()
    {
        if (! isset($this->day) || is_null($this->day)) {
            return;
        }

        if ($this->reoccurring_frequency == "weekly") {
            return config("constants.campaignWeekdays")[$this->day];
        } elseif ($this->reoccurring_frequency == "monthly") {
            return (new \NumberFormatter('en_US', \NumberFormatter::ORDINAL))->format($this->day);
        }
    }

    public function getReoccurringFrequencyColorAttribute()
    {
        if (is_null($this->reoccurring_frequency)) {
            return;
        }
        return config("constants.campaignReoccurringFrequencyColors")[$this->reoccurring_frequency];
    }
}
