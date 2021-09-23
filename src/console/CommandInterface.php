<?php

namespace Bermuda\App\Console;

interface CommandInterface
{
    public const success = 1;
    public const failure = 0;

    public function getName(): string ;
    public function getDescription(): string;

    public function execute(InputInterface $input, OutputInterface $output): int ;
}
