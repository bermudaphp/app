<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

final class BootableCallback implements Bootable
{
    /**
     * @var callable
     */
    private $bootable;

    public function __construct(
        callable $bootable
    ) {
        $this->bootable = $bootable;
    }

    public function boot(AppInterface $app): void
    {
        ($this->bootable)($app);
    }
}