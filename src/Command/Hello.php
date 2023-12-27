<?php

namespace App\Command;

use Haoa\Cli\Option;
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
     * @example php bin/cli.php hello arg1 arg2 -n=name
     */
    public function handle(): void
    {
        $name = $this->optMatch('n', 'name')->string('default');
        $options = $this->arg()->array();
        var_dump($options);

        $cid = Coroutine::getCid();
        print "hello, {$name}, cid:{$cid} \n";
    }

}
