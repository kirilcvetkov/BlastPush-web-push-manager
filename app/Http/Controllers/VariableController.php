<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Faker\Factory as Faker;
use App\Models\Variable;
use Inertia\Inertia;

class VariableController extends Controller
{
    use \App\Traits\ResponseTrait;

    public function index(Request $request)
    {
        $list = $payload = [];

        foreach (config('constants.variableScopes') as $scope) {
            $list[$scope]['objects'] = $this->objects($scope);
            $list[$scope]['cols'] = $this->cols($scope, $list[$scope]['objects']);
        }

        foreach (config('constants.variableColumns') as $scope => $cols) {
            $objects = isset($list[$scope]) ? ($list[$scope]['cols'] ?: $list[$scope]['objects']) : $cols;

            foreach ($objects as $object) {
                if ($object instanceof Variable && $object->noVars) {
                  continue;
                }

                $name = $object->name ?? $object;
                $type = $object->type ?? (! is_object($object) ? 'standard' : $scope);

                $payload[] = [
                    'name' => $name,
                    'scope' => $scope,
                    'type' => $type,
                    'color' => $type == 'standard' ? 'text-success' : ($type == 'global' ? 'text-info' : 'text-danger'),
                    'btn' => "btn-inverse-" . config('constants.variableScopesColors')[$scope],
                    'var' => '{$' . $scope . '_' . ($type == 'custom' ? $type . "_" : null) . $name . '}',
                ];
            }
        }

        return Inertia::render('Variables/Index', [
            'payload' => $payload,
            'example' => $this->howtoExample($list),
            'route' => url('/') . '/api/website/<span class="text-primary">----WEBSITE--KEY----</span>/javascript',
        ]);
    }

    public function objects($scope)
    {
        $objects = null;

        switch ($scope) {
            case 'global':
            case 'subscriber':
                $objects = Variable::where('user_id', Auth::id())->where('scope', $scope)->get();

                if ($objects->isEmpty()) {
                    $objects = $this->noVariables($scope);
                }
                break;

            case 'website':
                $objects = Auth::user()->websites()->with('variables')->get();
                break;

            case 'campaign':
                $objects = Auth::user()->campaigns()->with('variables')->get();
                break;

            case 'schedule':
                $objects = Auth::user()->schedules()->with('variables')->get();
                break;
        }

        return $objects;
    }

    public function cols($scope, $objects)
    {
        $cols = [];

        foreach (config('constants.variableColumns')[$scope] as $column) {
            $var = (new Variable())->fill([
                'name' => $column,
                'scope' => $scope,
                'value' => null,
                'target_id' => null,
                'user_id' => Auth::id(),
            ]);

            $var->type = 'standard';
            $var->class = $column == 'id' ? 'col-id' : 'col-standard d-none';

            $cols[] = $var;
        }

        foreach ($objects->pluck('variables') as $vars) {
            if (is_null($vars) && $objects->first() instanceof Variable) {
                $vars = $objects;
            }

            if ($vars && $vars->isNotEmpty()) {
                foreach ($vars as $var) {
                    if (! array_key_exists($var->name, $cols) && ! $var->noVars) {
                        $new = (new Variable())->fill([
                            'name' => $var->name,
                            'scope' => $var->scope,
                            'value' => null,
                            'target_id' => null,
                            'user_id' => Auth::id(),
                        ]);

                        $new->type = 'custom';
                        $new->class = 'col-custom';

                        $cols[$new->name] = $new;
                    }
                }
            }
        }

        return $cols;
    }

    protected function noVariables($scope)
    {
        $new = (new Variable())->fill([
            'name' => "No {$scope} variables.",
            'scope' => $scope,
            'value' => null,
            'target_id' => null,
            'user_id' => Auth::id(),
        ]);

        $new->noVars = true;

        return collect([$new]);
    }

    public function store(Request $request)
    {
        $created = null;

        foreach ($request->variable as $k => $variable) {
            $variable = json_decode($variable, true);

            $id = $variable['id'] ?? null;
            $name = null;
            $value = $request->value[$k] ?? '';

            if ($variable["scope"] == 'subscriber') {
                $name = $request->value[$k] ?? null;
                $value = strtolower($value);
            }

            $name = strtolower($name ?? $request->name[$k] ?? $variable["name"]);
            $targetIds = json_decode($request->targetid[$k], true) ?: null;

            if (! is_array($targetIds)) {
                $targetIds = [json_decode($request->targetid[$k], true) ?: $request->targetid[$k]];
            }

            $fields = [
                "name" => $name,
                "scope" => $variable["scope"],
                "value" => $value,
                "user_id" => Auth::id(),
            ];

            $validation = $this->validation($fields, false);

            if ($validation->fails()) {
                return $this->fail($validation->errors(), 400);
            }

            if ($request->new) {
                $exists = Variable::where('name', $name)
                    ->where('scope', $variable['scope'])
                    ->where('user_id', Auth::id())
                    ->first();

                if ($exists instanceof Variable) {
                    return $this->fail("Variable '{$name}' exists already.", 409);
                }
            }

            foreach ($targetIds as $targetId) {
                if (in_array($variable["scope"], ['global', 'subscriber'])) {
                    $targetId = null;
                }

                if (strlen($request->value[$k]) == 0) {
                    // $this->destroy($variable["scope"], $name, $targetId, true);
                    continue;
                }

                $fields['value'] = $value;
                $fields['target_id'] = $targetId;

                $validation = $this->validation($fields);

                if ($validation->fails()) {
                    return $this->fail($validation->errors(), 400);
                }

                $object = $id > 0 ? Variable::where('id', $id)->first() : null;

                if (is_null($object) && $fields['name']) {
                    $object = Variable::where('name', $fields['name'])
                        ->where('scope', $fields['scope'])
                        ->where('target_id', $targetId)
                        ->where('user_id', Auth::id())
                        ->first();
                }

                if (! $object instanceof Variable) {
                    $object = new Variable();
                }

                $object->fill($fields)->save();

                $created = $object->wasRecentlyCreated;
            }
        }

        return $this->success(
            true,
            ($created ? 201 : 200),
            null,
            "Variables " . (is_null($created) ? "not changed." : ($created === true ? " added." : " updated.")),
            "variables.index"
        );
    }

    public function validation(array $fields, bool $validateTarget = true)
    {
        $rules = [
            "name" => ['required', 'alpha_dash'],
            "scope" => [Rule::in(config('constants.variableScopes'))],
            "value" => ['required', 'alpha_dash'],
        ];

        if ($validateTarget) {
            $rules["target_id"] = ['nullable', 'integer'];
        }

        if ($fields['scope'] == 'subscriber') {
            unset($rules['value']);
        }

        $messages = [
            'name.alpha_dash' => 'The variable name may only contain letters and numbers.',
            'value.alpha_dash' => 'The variable value may only contain letters and numbers.'
        ];

        return Validator::make($fields, $rules, $messages);
    }

    public function howtoExample($payload)
    {
        $objects = isset($payload['subscriber']) ? $payload['subscriber']['objects'] : [];

        $json = [];
        $faker = Faker::create();

        foreach ($objects as $object) {
            $name = $object->noVars ? 'test' : $object->name;
            $json[$name] = $faker->word();
        }

        return json_encode($json);
    }

    public function destroy($scope, $name, int $targetId = null, bool $internal = false)
    {
        if (! in_array($scope, config('constants.variableScopes'))) {
            if ($internal) {
                return false;
            }
            return $this->fail("Scope is invalid.", 400);
        }

        $count = Auth::user()->variables()
            ->where('scope', $scope)
            ->where('name', $name)
            ->when($targetId > 0, function ($query) use ($targetId) {
                return $query->where('target_id', $targetId);
            })
            ->delete();

        if ($internal) {
            return true;
        }

        $message = $targetId > 0
            ? "Variable '{$name}' for {$scope} id {$targetId} deleted."
            : "Variables '{$name}' deleted ({$count} total).";

        return $this->success(true, 200, null, $message, "variables.index");
    }
}
