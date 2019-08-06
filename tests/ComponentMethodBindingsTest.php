<?php

namespace Tests;

use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Livewire;

class ComponentMethodBindingsTest extends TestCase
{
    /** @test */
    public function mount_method_receives_route_model_bindings()
    {
        Livewire::component('foo', ComponentWithBindings::class);

        Route::bind('foo', function ($value) {
            return new ModelToBeBoundStub($value);
        });

        Route::livewire('/test/{foo}', 'foo')->middleware('web');

        $this->get('/test/from-injection')->assertSee('from-injection');
    }

    /** @test */
    public function mount_method_receives_bindings()
    {
        Livewire::test(ComponentWithBindings::class)
            ->assertSee('from-injection');
    }
}

class ModelToBeBoundStub
{
    public function __construct($value = 'from-injection')
    {
        $this->value = $value;
    }
}

class ComponentWithBindings extends Component
{
    public function mount(ModelToBeBoundStub $stub)
    {
        $this->value = $stub->value;
    }

    public function render()
    {
        return app('view')->make('show-name')->with('name', $this->value);
    }
}