<?php

namespace Bermuda\App;

use Psr\Container\ContainerInterface;

interface ContainerCollectorInterface
{
    public function addContainer(ContainerInterface $container): void ;
}
