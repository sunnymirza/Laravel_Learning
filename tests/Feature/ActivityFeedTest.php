<?php

namespace Tests\Feature;

use Facade\Reflection\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
   public function creating_a_project_generates_activity()
   {
       $project = factory('App\Project')->create();
       $this->assertCount(1, $project->activity);
       $this->assertEquals ('created', $project->activity[0]->description);
   }
    /** @test */
   public function updating_a_project_generated_activity()
   {
       $project = factory('App\Project')->create();
       $project->update(['title' => 'changed']);
       $this->assertCount(2, $project->activity);
       $this->assertEquals ('updated', $project->activity[1]->description);
   }
   /** @test */
   public function creating_a_new_task_records_project_activity()
   {
       $project = factory('App\Project')->create();
       $project->addTask('Some Task');
       $this->actingAs($project->owner)->patch($project->tasks[0]->path(),[
           'body' => 'foobar',
               'completed' => true
           ]);
       $this->assertCount(2, $project->activity);
//       $this->assertEquals('completed_task', $project->activity->last()->description);
   }
}
