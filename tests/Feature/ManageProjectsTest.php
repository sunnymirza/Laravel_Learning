<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{
    use withfaker, RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_project()
    {
        //If I make a post request to this endpoint
        //I want to see the data in database table
        //and want to see it in browser
        $this->withoutExceptionHandling();
        $this->actingAs(factory('App\User')->create());
        $this->signIn();
        $this->get('/projects/create')->assertStatus(200);
        $attributes =
            [
               'title' =>$this->faker->name,
               'description'=>$this->faker->sentence,
                'notes'=>$this->faker->sentence
            ];
        $response = $this->post('/projects', $attributes);
        $project = Project::where($attributes)->first();
        $response-> assertRedirect($project->path());
        $this->assertDatabaseHas('projects',$attributes);
        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes'])
        ;

    }
    /** @test */
    public function a_user_can_update_a_project()
    {
        $this->signIn();
       $this->withoutExceptionHandling();
       $project = factory('App\Project')->create(['owner_id' => auth() -> id()]);
       $this->patch($project->path(), [
            'notes' => 'Changed'
       ])->assertRedirect($project->path());
       $this->assertDatabaseHas('projects', ['notes' => 'Changed']);

    }
    /** @test */
    public function a_project_require_a_title()
    {
        $this->actingAs(factory('App\User')->create());
        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('title');
    }
    /** @test */
    public function a_project_require_a_description()
    {
        $this->actingAs(factory('App\User')->create());
        $attributes =  factory('App\Project')->raw(['description' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }
    /** @test */
    public function a_user_can_view_their_project()
    {
        $this->be(factory('App\User')->create());
        $this->withoutExceptionHandling();
        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);
        $this->get($project->path())
                ->assertSee($project->title)
                ->assertSee($project->description);
    }
    /** @test */
    public function guests_cannot_create_projects()
    {
        //$this->withoutExceptionHandling();
        $attributes = factory('App\Project')->raw();
        $this->post('/projects', $attributes)->assertRedirect('login');

    }
    /** @test */
    public function guests_may_not_view_projects()
    {
        //$this->withoutExceptionHandling();
        $this->get('/projects')->assertRedirect('login');
    }
    /** @test */
    public function guests_cannot_view_a_single_project()
    {
        $project = factory('App\Project')->create();
        $this->get($project->path())->assertRedirect('login');
    }
    /** @test */
    public function an_authenticated_user_cannot_view_projects_of_others()
    {
        $this->be(factory('App\User')->create());
        //this->withoutExceptionHandling();
        $project = factory('App\Project')->create();
        $this->get($project->path())->assertStatus(403);
    }
}
