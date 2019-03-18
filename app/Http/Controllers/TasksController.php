<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Task\TaskWeekCollection;
use App\Http\Resources\Task\TaskMonthCollection;
use App\Models\Task;

class TasksController extends Controller
{

  public function getTasks(Request $request) {
      $tasks = Task::getTasks($request);

      switch ($request->type) {
        case 'month':
          return new TaskMonthCollection($tasks);
          break;

        case 'week':
          return new TaskWeekCollection($tasks);
          break;
      }
  }

  public function getTasksList(Request $request) {

  }

  public function saveDailyTasks(Request $request) {
    $validation = Validator($request->all(), [
      'tasks' => 'required|array|min:1',
      'tasks.*.name' => 'required',
      'tasks.*.description' => 'max:600',
      'date_name' => 'max:100',
      'date_id' => 'exists:dates,id',
      'date' => 'required|numeric',
    ]);
    $success = false;

    if (!$validation->fails()) {
      Task::saveTasks($request, 'daily');
      $success = true;
    }

    return response()->json(compact('success'));
  }

  public function deleteDailyTasks(Request $request) {
    $success = (boolean)Date::destroy($request->id);

    return response()->json(compact('success'));
  }
}
