<?php

namespace App\Providers;

use App\Models\EmailModel;
use App\Repositories\EmailRepository;
use App\Services\EmailServices;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\Interfaces\IMail', 'App\Services\Mail');

        $this->app->bind('App\Models\EmailModel', function () {
            return new EmailModel();
        });


        $this->app->bind('App\Repositories\EmailRepository', function ($app) {
            return new EmailRepository($app['App\Models\EmailModel']);
        });

        $this->app->bind('IMailerArray', function ():array {
            $config               = config('mail.mailers');
            $mailProvider         = [];
            $mailerClassNameSpace = 'App\Services';

            //Sort config array based on the priority set in the config file.
            array_multisort(array_column($config, "priority"), SORT_ASC, $config);

            foreach ($config as $key => $value) {
                $mailerClass    = $mailerClassNameSpace . '\\' . ucfirst($key . 'EmailService');
                $mailProvider[] = new $mailerClass();

            }

            return $mailProvider;
        });


        $this->app->bind('App\Services\Interfaces\IEmailServices', function ($app) {
            return new EmailServices($app['App\Repositories\EmailRepository'], $app['App\Services\Interfaces\IMail'],
                $app['IMailerArray']);
        });

    }
}
