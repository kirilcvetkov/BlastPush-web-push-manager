<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Subscriber;
use App\Models\Variable;
use App\Models\Campaign;
use App\Models\Schedule;
use App\Models\Message;
use App\Models\Website;
use App\Models\Push;
use App\Models\User;
use App\Models\Event;

class MessageLinkRenderController extends Controller
{
    protected $vars = [];
    protected $subscriber;
    protected $campaign;
    protected $schedule;
    protected $message;
    protected $website;
    protected $push;
    protected $user;
    protected $event;
    protected $link;
    protected $eventColumns = [
        'ip',
        'user_agent',
        'device',
        'platform',
        'browser',
        'is_mobile',
        'is_tablet',
        'is_desktop',
        'is_robot',
        'referer',
    ];

    public function __construct(
        Subscriber $subscriber,
        Campaign $campaign,
        Schedule $schedule,
        Message $message,
        Website $website,
        User $user,
        Push $push
    ) {
        $this->subscriber = $subscriber;
        $this->campaign = $campaign;
        $this->schedule = $schedule;
        $this->message = $message;
        $this->website = $website;
        $this->user = $user;
        $this->push = $push;
    }

    public function get()
    {
        $this->event = $this->subscriber->events()->orderBy('id', 'desc')->first() ?? new Event();

        // 1. Pull all vars from link
        // 2. Break down the vars by scope/name
        // 3. Gather necessary data
        $this->breakDown($this->message->url);

        // 4. Replace
        $this->link = $this->replace($this->message->url);

        return $this->link;
    }

    public function getVars($link)
    {
        preg_match_all('/{\$[a-z\d\_\|]+}/', $link, $matchedVars);

        return $matchedVars[0];
    }

    public function breakDown($link)
    {
        foreach ($this->getVars($link) as $var) {
            $bare = str_replace(['{$', '}'], '', $var);

            [$scope, $name] = explode("_", $bare, 2);

            if (! array_key_exists($scope, config('constants.variableColumns'))) {
                $this->vars[$var] = null; // unknown scope
                continue;
            }

            if ($scope == 'global' || strpos($name, "custom_") !== false) {
                $this->vars[$var] = $this->getCustomData($scope, $name);
                continue;
            }

            if (! in_array($name, config('constants.variableColumns')[$scope])) {
                $this->vars[$var] = null;
                continue;
            }

            $this->vars[$var] = $this->getStandardData($scope, $name);
        }
    }

    public function getStandardData($scope, $name)
    {
        if (in_array($name, $this->eventColumns)) {
            $scope = 'event';
        }

        return is_object($this->{$scope})
            ? urlencode($this->{$scope}->{$name} ?? null)
            : null;
    }

    public function getNameAndDefault($name)
    {
        $name = str_replace("custom_", "", $name);
        $default = null;

        if (strpos($name, "|") !== false) {
            list($name, $default) = explode("|", $name);
        }

        return [$name, $default];
    }

    public function getCustomData($scope, $name)
    {
        [$name, $default] = $this->getNameAndDefault($name);

        if ($scope == 'subscriber') {
            return $this->subscriber->variables[$name] ?? $default;
        }

        $object = Variable::where('user_id', $this->user->id)
            ->when($scope == 'global', function ($query) {
                return $query->whereNull('target_id');
            })
            ->when($scope == 'website', function ($query) {
                return $query->where('target_id', $this->website->id);
            })
            ->when($scope == 'campaign', function ($query) {
                return $query->where('target_id', $this->campaign->id);
            })
            ->when($scope == 'schedule', function ($query) {
                return $query->where('target_id', $this->schedule->id);
            })
            ->when($scope == 'message', function ($query) {
                return $query->where('target_id', $this->message->id);
            })
            ->where('scope', $scope)
            ->where('name', $name)
            ->first();

        return $object instanceof Variable ? urlencode($object->value) : $default;
    }

    public function replace($link)
    {
        return strtr(str_replace(" ", "", $link), $this->vars);
    }
}
