<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTest extends TestCase
{
    use withfaker, RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_project()
    {

        //If I make a post request to this endpoint
        //I want to see the data in database table
        //and want to see it in browser

        $this->withoutExceptionHandling();

        $attributes =
            [
               'title' =>$this->faker->name,
               'description'=>$this->faker->sentence
            ];

        $this->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects',$attributes);

        $this->get('/projects')->assertSee($attributes['title']);

    }

    /** @test */
    public function a_project_require_a_title()
    {
        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_require_a_description()
    {
        $attributes =  factory('App\Project')->raw(['description' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }
}
