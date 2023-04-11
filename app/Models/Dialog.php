<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dialog extends Model
{
    use HasFactory;

    protected $fillable = [
        "message",
        "image",
        "delay",
        "button_allow",
        "button_block",
        "show_percentage",
        "user_id",
    ];

    protected $appends = [
        'image_default',
        'created',
        'createdHuman',
        'updated',
        'updatedHuman',
    ];

    public function getImageDefaultAttribute()
    {
        return 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' xmlns:xlink=\'http://www.w3.org/1999/xlink\' version=\'1.1\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'rgb(41, 121, 255)\'><path d=\'M21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29C10,4.19 10,4.1 10,4C10,2.9 10.9,2 12,2C13.1,2 14,2.9 14,4C14,4.1 14,4.19 14,4.29C16.97,5.17 19,7.9 19,11V17L21,19M14,21C14,22.1 13.1,23 12,23C10.9,23 10,22.1 10,21\' /></svg>';
    }

    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->format("m/d/y h:i a") : null;
    }

    public function getCreatedHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function getUpdatedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format("m/d/y h:i a") : null;
    }

    public function getUpdatedHumanAttribute()
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : null;
    }

    public function websites()
    {
        return $this->hasMany('App\Models\Website');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
