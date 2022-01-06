<?php

namespace Bermuda\App\Boot;

use Bermuda\Clock;
use Bermuda\App\AppInterface;
use Bermuda\App\Boot\BootstrapperInterface;

final class DateTimeBootstrapper implements BootstrapperInterface
{
    public function boot(AppInterface $app): void
    {
        Clock::timeZone($app->config->timeZone);
        Clock::locale($app->config->locale);

        $app->registerCallback('createDate', '\Bermuda\Clock\Clock::create');
    }
}
