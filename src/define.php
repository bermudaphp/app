<?php

namespace Bermuda\App;

define('is_cli', PHP_SAPI == 'cli');

function is_cli(): bool
{
  return is_cli;
}

