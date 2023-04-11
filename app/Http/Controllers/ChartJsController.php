<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartJsController extends Controller
{
    public function prep($query, $daysBack, array $secondLevel = null)
    {
        $dates = $stats = [];

        foreach (range(-6, 0) as $i => $days) {
            $ts = strtotime("{$days} days");
            $date = date("Y-m-d", $ts);
            $dates[$i] = date("M j (D)", $ts);

            foreach ($types as $typeId) {
                $name = config('constants.eventTypesDetails')[$typeId]['name'];

                if (! array_key_exists($name, $stats)) {
                    $stats[$name] = [];
                }

                if (! array_key_exists($i, $stats[$name])) {
                    $stats[$name][$i] = 0;
                }
            }

            foreach ($query->get()->toArray() as $row) {
                $name = config('constants.eventTypesDetails')[$row['type_id']]['name'];
                $stats[$name][$i] += $row['total'];
            }
        }

        return [
            'labels' => $dates,
            'data' => array_values($stats),
        ];
    }
}
