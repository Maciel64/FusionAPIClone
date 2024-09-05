<?php

namespace App\Providers;


use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      $this->registerFacades();
    }

    private function registerFacades()
    {
      foreach (config('facades') as $nameFacade => $class) {
        App::bind($nameFacade, fn() => new $class());
      }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
