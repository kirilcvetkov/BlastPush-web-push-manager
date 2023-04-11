<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\WebhookCreated;
use App\Listeners\ProcessWebhook;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        "website_id",
        "event_id",
        "event_type_id",
        "tries",
        "request_url",
        "request_method",
        "request_body",
        "response_status",
        "response_headers",
        "response_body",
        "status",
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => WebhookCreated::class,
    ];

    protected $appends = [
        'statusName',
    ];

    public function getEventTypeIdAttribute($value)
    {
        return ucfirst(config('constants.eventTypes')[$value] ?? $value);
    }

    public function getRequestMethodAttribute($value)
    {
        $methods = array_flip(config('constants.webhookMethod'));

        return ucfirst($methods[$value]) ?: $value;
    }

    public function getStatusNameAttribute()
    {
        $names = config('constants.webhookResponseStatusNames');

        $after = null;
        if (config('constants.webhookResponseStatus')['queue-retry'] == $this->status) {
            $delays = (new ProcessWebhook())->delays;
            $after = " after " . ($delays[$this->tries] / 60) . " min";
        }

        return ($names[$this->status] ?? $this->status) . $after;
    }

    public function getResponseStatusAttribute($value)
    {
        $text = null;

        switch ($value) {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 200: $text = 'OK'; break;
            case 201: $text = 'Created'; break;
            case 202: $text = 'Accepted'; break;
            case 203: $text = 'Non-Authoritative Information'; break;
            case 204: $text = 'No Content'; break;
            case 205: $text = 'Reset Content'; break;
            case 206: $text = 'Partial Content'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Moved Temporarily'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Time-out'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Request Entity Too Large'; break;
            case 414: $text = 'Request-URI Too Large'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Time-out'; break;
            case 505: $text = 'HTTP Version not supported'; break;
        }

        return $text ? $text . " (" . $value . ")" : $value;
    }

    public function getResponseBodyAttribute($value)
    {
        return preg_match("/<[^<]+>/", $value) // is HTML?
            ? htmlspecialchars($value)
            : $value;
    }

    public function setResponseBodyAttribute(&$value)
    {
        $this->attributes['response_body'] = $value = substr(stripslashes($value), 0, 65535);
    }

    /**
     * Get all of the events for the website.
     */
    public function website()
    {
        return $this->belongsTo('App\Models\Website');
    }

    /**
     * Get all of the events for the website.
     */
    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
}
