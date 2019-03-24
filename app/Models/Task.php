<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Task extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    public function date() {
      return $this->belongsTo(Date::class);
    }

    public function type() {
      return $this->belongsTo(TaskType::classs);
    }

    public static function getTasks(Request $request) {
      switch ($request->type) {
        case 'month':
          return Task::selectRaw('
            SUM(tasks.id) as tasks_count,
            dates.date as date,
            dates.name as date_title
          ')
          ->join('dates', 'dates.id', '=', 'tasks.date_id')
          ->where('dates.user_id', 1)
          ->groupBy(DB::raw('YEAR(dates.date), MONTH(dates.date)'))
          ->orderBy('dates.date')
          ->get();
          break;

          case 'week':
          return Task::selectRaw("
            DATE_ADD(date, INTERVAL - WEEKDAY(date) DAY) as begin_of_week,
            DATE_ADD(date, INTERVAL + WEEKDAY(date) DAY) as end_of_week,
            dates.date as date,
            dates.name as date_title,
            tasks.name,
            tasks.description
          ")
          ->join('dates', 'dates.id', '=', 'tasks.date_id')
          ->where('dates.user_id', 1)
          ->whereRaw('dates.date >= DATE_ADD(date, INTERVAL - WEEKDAY(date) DAY)')
          ->orderBy('dates.date')
          ->get();
            break;
          case 'general':
           return Task::selectRaw("
            DATE_ADD(date, INTERVAL - WEEKDAY(date) DAY) as begin_of_week,
            DATE_ADD(date, INTERVAL + WEEKDAY(date) DAY) as end_of_week,
            dates.date as date,
            dates.name as date_title,
            tasks.name,
            tasks.description
          ")
          ->join('dates', 'dates.id', '=', 'tasks.date_id')
          ->where('dates.user_id', 1)
          ->whereRaw('dates.date >= DATE_ADD(date, INTERVAL - WEEKDAY(date) DAY)')
          ->orderBy('dates.date')
          ->get();

      }
    }

    public function scopeGetAllTasks(Request $request) {
      return Task::all();
    }

    public static function saveTasks(Request $request, $type) {
      $date = Carbon::createFromTimeStampUTC((int)$request->date)->toDateString();
      $dateModel = Date::where('date', $date)->first();
      $dailyTaskId = TaskType::getTypeId($type);
      $tasks = array_map(function($task) use($dailyTaskId, $request) {
        $task['type_id'] = $dailyTaskId;
        $task['execute_time'] = Carbon::createFromTimeStampUTC((int)$task['execute_time'])->toDateTimeString();
        return $task;
      }, $request->tasks);

      if (empty($dateModel)) {
          $dateModel = Date::create([
            'name' => $request->date_name,
            'user_id' => 1,
            'date' => $date
          ]);
      }

      $dateModel->tasks()->delete();
      $dateModel->tasks()->createMany($tasks);
    }


}
