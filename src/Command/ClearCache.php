<?php

namespace App\Command;

use App\Common\Cli\BaseRun;
use Mix\Cli\Flag;

/**
 * Class ClearCache
 * @package App\Command
 */
class ClearCache extends BaseRun
{

    public function main(): void
    {
        $key = Flag::match('k', 'key')->string();
        RDS::instance()->del($key);
        print 'ok';
    }

}
