<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;

class VariableMergeController extends Controller
{
    public $variables = null;

    public function __construct(Subscriber $subscriber, $variables)
    {
        $variables = array_merge($subscriber->variables ?? [], $variables ?? []);

        ksort($variables);

        if (empty($variables)) {
            $variables = null;
        }

        $this->variables = $variables;
    }

    public function get()
    {
        return $this->variables;
    }
}
