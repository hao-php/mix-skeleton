<?php

namespace App\Common\Cli;

use Mix\Cli\RunInterface;

abstract class BaseRun implements RunInterface
{
    public array $options = [];

}