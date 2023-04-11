<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Variable;

class VariableFilterController extends Controller
{
    public $variables = null;

    public function __construct($variables, int $userId)
    {
        $variables = is_string($variables) ? json_decode($variables, true) : $variables;

        if (! is_array($variables)) {
            return;
        }

        $variables = $this->validation($variables);

        $existing = Variable::select('name')
            ->whereIn('name', array_keys($variables))
            ->where('user_id', $userId)
            ->get()->pluck('name')->all();

        $this->variables = array_filter(
            $variables,
            function ($k) use ($existing) { return in_array($k, $existing); },
            ARRAY_FILTER_USE_KEY
        );
    }

    public function validation($variables)
    {
        return array_filter(
            $variables,
            function ($k) { return ! preg_match('/[^a-z_\-0-9]/', $k); },
            ARRAY_FILTER_USE_KEY
        );
    }

    public function get()
    {
        return $this->variables;
    }
}
