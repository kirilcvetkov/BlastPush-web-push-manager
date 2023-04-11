<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Push extends Model
{
    use HasFactory;

    protected $fillable = [
        "subscriber_id",
        "campaign_id",
        "schedule_id",
        "message_id",
        "uuid",
        "scheduled_to_send_at",
        "sent_at",
        "is_success",
        "response",
        "http_code",
        "website_id",
        "user_id",
    ];

    protected $dates = [
        "scheduled_to_send_at",
        "sent_at",
    ];

    protected $appends = [
        'created',
        'createdHuman',
        'sent',
        'sentHuman',
        'scheduledToSend',
        'scheduledToSendHuman',
    ];

    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format("m/d/y h:i a") : null;
    }

    public function getCreatedHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function getSentAttribute()
    {
        return $this->sent_at ? $this->sent_at->format("m/d/y h:i a") : null;
    }

    public function getSentHumanAttribute()
    {
        return $this->sent_at ? $this->sent_at->diffForHumans() : null;
    }

    public function getScheduledToSendAttribute()
    {
        return $this->scheduled_to_send_at ? $this->scheduled_to_send_at->format("m/d/y h:i a") : null;
    }

    public function getScheduledToSendHumanAttribute()
    {
        return $this->scheduled_to_send_at ? $this->scheduled_to_send_at->diffForHumans() : null;
    }

    public function subscriber()
    {
        return $this->belongsTo("App\Models\Subscriber")->withTrashed();
    }

    public function message()
    {
        return $this->belongsTo("App\Models\Message");
    }

    public function campaign()
    {
        return $this->belongsTo("App\Models\Campaign");
    }

    public function schedule()
    {
        return $this->belongsTo("App\Models\Schedule")->withTrashed();
    }

    public function status()
    {
        $status = $this->getAttributes()['is_success'];

        return config('constants.queueResponseStatusNames')[$status] ?: $status;
    }

    public function statusIcon()
    {
        $status = $this->getAttributes()['is_success'];

        return config('constants.queueResponseStatusIcon')[$status] ?: $status;
    }

    public function statusColor($pre = 'text')
    {
        $status = $this->getAttributes()['is_success'];

        return $pre . "-" . config('constants.queueResponseStatusColor')[$status] ?: $status;
    }
}
