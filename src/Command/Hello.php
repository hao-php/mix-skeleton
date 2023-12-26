<?php

namespace App\Command;

use App\Common\Cli\BaseRun;
use Mix\Cli\Flag;
use Mix\Cli\Option;
use Swoole\Coroutine;

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

    /**
     * @example php bin/cli.php -n=name
     */
    public function handle(): void
    {
        var_dump(1 / 3);
        $name = Flag::match('n', 'name')->string('default');
        $cid = Coroutine::getCid();
        print "hello, {$name}, cid:{$cid} \n";
    }

}
