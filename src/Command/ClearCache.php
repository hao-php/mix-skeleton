<?php

namespace App\Command;

use App\Container\RDS;
use Mix\Cli\Flag;

/**
 * Class ClearCache
 * @package App\Command
 */
class ClearCache extends BaseCommand
{

    public function handle(): void
    {
        $key = Flag::match('k', 'key')->string();
        RDS::instance()->del($key);
        print 'ok';
    }

}
