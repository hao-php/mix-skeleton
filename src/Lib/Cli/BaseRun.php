<?php

namespace App\Lib\Cli;

use Mix\Cli\RunInterface;

abstract class BaseRun implements RunInterface
{
    public array $options = [];

}