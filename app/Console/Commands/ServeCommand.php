<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

class ServeCommand extends BaseServeCommand
{
    /**
     * Use a differently named router file: the default "server.php" filename is
     * blocked by some antivirus software (e.g. AVG Web Shield) on some machines.
     */
    protected function serverCommand()
    {
        $command = parent::serverCommand();

        $command[3] = base_path('server-artisan.php');

        return $command;
    }
}
