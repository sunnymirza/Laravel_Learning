<?php

namespace App;

use function foo\func;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];
    protected $touches = ['project'];
    protected  $casts = [
        'completed' => 'boolean'
    ];
    protected static function boot()
    {
        parent::boot();
        static ::created(function ($task)
        {
            $task->project->recordActivity('created_task');
        });
        static ::updated(function ($task) {
            if (!$task->completed) return;
            $task->project->recordActivity('completed_task');
        });
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }
    public function complete()
    {
        $this->update(['completed' => true]);
    }
}
