<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;


    protected $dates = ['deleted_at'];

    protected $appends = ['avatar_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'name', 'email', 'password', 'active', 'activation_token', 'avatar'
    ];


    protected $hidden = ['password', 'active', 'activation_token', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($name)
    {
        return $this->role()->where('name', '=', $name)->exists();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getAvatar() {
        return asset('storage/avatars/'.$this->avatar);
    }

    public function getAvatarUrlAttribute() {
         return  Storage::disk('public')->url("avatars/{$this->avatar}");
    }
}
