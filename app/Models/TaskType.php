<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{

    public $table = 'tasks_types';

    public $timestamps = true;
    protected $guarded = [];

    public function tasks() {
      $this->hasMany(Tasks::class);
    }

    public static function getTypeId($name) {
      switch (variable) {
        case 'value':
          // code...
          break;

        default:
          // code...
          break;
      }
    }
}
