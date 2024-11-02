<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Kernel extends Command
{
    protected $commands = [
        \App\Console\Commands\CleanStorage::class,
    ];
}
