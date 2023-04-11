<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Minishlink\WebPush\VAPID;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'country',
        'website',
        'company',
        'timezone',
        'api_token',
        'plan_id',
        'uuid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'name',
        'created',
        'createdHuman',
        'updated',
        'updatedHuman',
    ];

    public static function boot()
    {
        parent::boot();

        $keys = VAPID::createVapidKeys();

        self::creating(function($model) use ($keys) {
            $model->uuid = \Uuid::generate(4);
            $model->vapid_public = $keys['publicKey'];
            $model->vapid_private = $keys['privateKey'];
            $model->api_token = $model->api_token ?? \Str::random(60);
        });
    }

    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=DA22FF&background=F6ECFD';
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
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
        return $this->hasMany('App\Models\Website'); // ->withTrashed();
    }

    public function dialogs()
    {
        return $this->hasMany('App\Models\Dialog');
    }

    public function subscribers()
    {
        return $this->hasMany('App\Models\Subscriber')->withTrashed();
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message'); // ->withTrashed();
    }

    public function pushes()
    {
        return $this->hasMany('App\Models\Push');
    }

    public function campaigns()
    {
        return $this->hasMany('App\Models\Campaign');
    }

    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }

    public function variables()
    {
        return $this->hasMany('App\Models\Variable');
    }
}
