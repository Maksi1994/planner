<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    public function tasks() {
      return $this->hasMany(Task::class);
    }

    public function user() {
      return $this->belongsTo(User::class);
    }
}
