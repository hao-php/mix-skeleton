<?php

namespace App\Command;

use Mix\Cli\Flag;
use Mix\Cli\Option;
use Swoole\Coroutine;

/**
 * @package App\Command
 */
class Hello extends BaseCommand
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

    /**
     * @example php bin/cli.php -n=name
     */
    public function handle(): void
    {
        $name = Flag::match('n', 'name')->string('default');
        $cid = Coroutine::getCid();
        print "hello, {$name}, cid:{$cid} \n";
    }

}
