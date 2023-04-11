<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Variable extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        "name",
        "scope",
        "value",
        "target_id",
        "user_id",
    ];

    public function websites()
    {
        return \DB::table('websites')
            ->select(
                'variables.*',
                'websites.id as target_id',
                'websites.name as website_name',
                'websites.domain as website_domain',
            )
            ->leftJoin('variables', function ($join) {
                $join->on('websites.id', '=', 'variables.target_id')
                    ->where('variables.scope', 'website')
                    ->where('variables.user_id', Auth::id());
            })
            ->where('websites.user_id', Auth::id());

        return $this->select(
                'websites.id as target_id',
                'websites.name as website_name',
                'websites.domain as website_domain',
                'variables.*'
            )
            ->where('scope', 'website')
            ->where('variables.user_id', Auth::id())
            ->join('websites', 'websites.id', '=', 'variables.target_id');
    }
}
