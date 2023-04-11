<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use App\Http\Requests\CsvImportRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Website;
use App\Models\Subscriber;
use App\Models\Event;
use App\Models\Variable;

class ImportSubscribersController extends Controller
{
    protected $fields = [
        'subscribers.created_at' => false,
        'subscribers.endpoint' => true,
        'subscribers.public' => true,
        'subscribers.auth' => true,
        'subscribers.encoding' => false,
        'events.location' => false,
        'events.ip' => false,
        'events.country' => false,
        'events.state' => false,
        'events.city' => false,
        'events.postal' => false,
        'events.timezone' => false,
        'events.user_agent' => false,
    ];

    protected $subscriber;
    protected $event;
    protected $variables;

    public function show()
    {
        return view("subscribers.import");
    }

    public function preview(CsvImportRequest $request)
    {
        $fields = $this->fields;
        $variables = Variable::select('name')
            ->where('user_id', Auth::id())
            ->where('scope', 'subscriber')
            ->get()
            ->pluck('name')
            ->map(function ($item) use (&$fields) {
                $fields["variables.{$item}"] = false;
            });

        $websites = Auth::user()->websites()->get();
        $path = $request->file('import')->move(storage_path('tmp'))->getRealPath();

        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);
        $data = array_slice($data, 0, 20);

        return view(
            "subscribers.import-preview",
            compact('path', 'websites', 'header', 'data', 'fields')
        );
    }

    public function store(Request $request)
    {
        $website = Auth::user()->websites()->where('id', $request->website_id)->first();

        if (! $website instanceof Website) {
            return redirect()->route("import.show")->withErrors(["Website not found."]);
        }

        try {
            $data = array_map('str_getcsv', file($request->path));
            $header = array_shift($data);
        } catch (\Exception $e) {
            return redirect()->route("import.show")->withErrors([$e->getMessage()]);
        }

        $stats = [0 => 0, 1 => 0];
        $errors = [];

        foreach ($data as $i => $line) {
            $this->init();

            foreach (array_filter($request->fields) as $id => $field) {
                if ($this->add($field, $line[$id]) === false) {
                    if (empty($errors[$i])) {
                        $errors[$i] = "Line " . ($i + 2) . " has empty column ";
                    }
                    $errors[$i] .= $field;
                    $stats[0]++;
                    continue 2;
                }
            }

            $stats[$this->persist($website)]++;
        }

        unlink($request->path);

        return view("subscribers.import")
            ->with('success', "Imported {$stats[1]} records." . ($stats[0] ? " Failed {$stats[0]}." : null))
            ->withErrors($errors);
    }

    public function init()
    {
        $this->subscriber = [];
        $this->event = [];
        $this->variables = [];
    }

    public function breakdown($field)
    {
        list($scope, $col) = explode(".", $field);
        $required = $this->fields[$field] ?? false;

        return [$scope, $col, $required];
    }

    public function add($field, $value): bool
    {
        [$scope, $col, $required] = $this->breakdown($field);

        switch ($scope) {
            case 'subscribers':
                if ($required && empty($value)) {
                    return false;
                }
                $this->subscriber[$col] = $value;
                break;

            case 'events':
                $this->event[$col] = $value;
                break;

            case 'variables':
                $this->variables[$col] = $value;
                break;
        }

        return true;
    }

    public function persist(Website $website): int
    {
        $fields = [
            'type' => 'subscribe',
            'created_at' => $this->subscriber['created_at'] ?? null,
            'subscription' => [
                'endpoint' => $this->subscriber['endpoint'] ?? null,
                'keys' => [
                    'p256dh' => $this->subscriber['public'] ?? null,
                    'auth' => $this->subscriber['auth'] ?? null,
                ],
                'expirationTime' => null,
            ],
            'encoding' => $this->subscriber['encoding'] ?? null,
            'variables' => $this->variables ?? null,
            'ip' => $this->event['ip'] ?? null,
            'user_agent' => $this->event['user_agent'] ?? null,
        ];

        $request = (new Request())->setJson(new ParameterBag($fields));

        $subscriber = app('App\Http\Controllers\SubscriberController')->store($request, $website, true);
        $event = null;

        if ($subscriber instanceof Subscriber) {
            $event = app('App\Http\Controllers\EventController')->store($request, $website, $subscriber, true);
        }

        if (! $subscriber instanceof Subscriber || ! $event instanceof Event) {
            dd($this->subscriber, $this->event, $fields, $subscriber, $event);
        }

        return $subscriber instanceof Subscriber && $event instanceof Event ? 1 : 0;
    }
}
