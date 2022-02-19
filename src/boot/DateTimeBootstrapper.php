<?php

namespace Bermuda\App\Boot;

use Bermuda\Clock\Clock;
use Bermuda\App\AppInterface;

final class DateTimeBootstrapper implements BootstrapperInterface
{
    public function boot(AppInterface $app): void
    {
        Clock::timeZone($app->config->timeZone);
        Clock::locale($app->config->locale);

        $app->registerCallback('createDate', '\Bermuda\Clock\Clock::create');
        return $app;
    }
}
