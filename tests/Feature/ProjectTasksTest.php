<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->signIn();
        $project = auth()->user()->projects()->create(factory(Project::class)->raw());
        $this->post($project->path(). '/tasks', ['body' => 'Lorem Ipsum']);
        $this-> get($project->path())
            ->assertSee('Lorem Ipsum');
    }
    /** @test */
    public function a_task_requires_a_body()
    {
        $this->signIn();
        $project = auth()->user()->projects()->create(factory(Project::class)->raw());
        $attributes =  factory('App\Task')->raw(['body' => '']);
        $this->post($project->path().'/tasks', $attributes)->assertSessionHasErrors('body');
    }
    /** @test */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();
        $this->post($project->path().'/tasks')->assertRedirect('login');
    }
    /** @test */
    public function only_the_owner_of_project_may_add_tasks()
    {
        $this->signIn();
        $project = factory('App\Project')->create();
        $this->post($project->path(). '/tasks', ['body' => 'Lorem Ipsum'])
            ->assertStatus(403);
        $this->assertDatabaseMissing('tasks', ['body' => 'Lorem Ipsum']);

    }
    /** @test */
    public function only_the_owner_of_project_may_update_a_task()
    {
        $this->signIn();
        $project = factory('App\Project')->create();
        $task = $project->addTask('test task');
        $this->patch($task->path(), ['body' => 'Lorem Ipsum'])
            ->assertStatus(403);
        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);

    }
    /** @test */
    public function a_task_can_be_updated()
    {
        $this->signIn();
        $project = auth()->user()->projects()->create(factory(Project::class)->raw());
        $task = $project->addTask('test task');
        $this->patch($project->path(). '/tasks/' .$task->id, $task->complete());
        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
             'completed' => true
        ]);
    }
}
