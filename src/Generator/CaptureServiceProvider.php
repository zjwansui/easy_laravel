<?php


namespace Zjwansui\EasyLaravel\Generator;


use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Illuminate\Support\ServiceProvider;
use Zjwansui\EasyLaravel\Generator\Command\CreateRequestCommand;
use Zjwansui\EasyLaravel\Generator\Command\CreateResponseCommand;

class CaptureServiceProvider extends ArtisanServiceProvider
{

    protected $selfCommands = [
        'CreateResponse'=>'command.api_response.make',
        'CreateRequest'=>'command.api_request.make',
    ];

    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands, $this->devCommands,$this->selfCommands
        ));
    }

    protected function registerCreateRequestCommand()
    {
        $this->app->singleton('command.api_request.make', function ($app) {
            return new CreateRequestCommand($app['files']);
        });
    }

    protected function registerCreateResponseCommand()
    {
        $this->app->singleton('command.api_response.make', function ($app) {
            return new CreateResponseCommand($app['files']);
        });
    }
}
