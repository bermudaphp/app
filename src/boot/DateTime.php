<?php

namespace Bermuda\App\Boot;

use Bermuda\Clock\Clock;
use Bermuda\App\AppInterface;
use Bermuda\Config\Config;

final class DateTime implements Bootable
{
    public function boot(AppInterface $app): void
    {
        //Clock::timeZone($app->config->timeZone);
        //Clock::locale($app->config->locale);

        $app->registerCallback('createDate', '\Bermuda\Clock\Clock::create');



        $timezone = $app->config[Config::app_timezone]
            ?? date_default_timezone_get();

        Clock::timeZone(new \DateTimeZone($timezone));
        date_default_timezone_set($timezone);
    }
}
