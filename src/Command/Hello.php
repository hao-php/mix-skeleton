<?php

namespace App\Command;

use App\Lib\Cli\BaseRun;
use Mix\Cli\Flag;
use Mix\Cli\Option;

/**
 * @package App\Command
 */
class Hello extends BaseRun
{

    public function __construct()
    {
        $this->options = [
            new Option([
                'names' => ['name', 'n'],
                'usage' => 'name',
            ]),
        ];
    }

    public function main(): void
    {
        $name = Flag::match('n', 'name')->string();
        print "hello, {$name}\n";
    }

}
