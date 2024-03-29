<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to initialize a module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->call('make:model', [
            "name" => "{$name}",
            "-m" => true,
            "-s" => true,
            "-f" => true,
        ]);

        $this->call('make:request', [
            "name" => "Api/{$name}/Store{$name}Request",
        ]);

        $this->call('make:request', [
            "name" => "Api/{$name}/Update{$name}Request",
        ]);

        $this->call('make:modulecontrollers', [
            "name" => $name,
        ]);

        $this->call('make:route', [
            "name" => "{$name}",
        ]);

        $this->call('make:resource', [
            "name" => "{$name}Resource",
        ]);

        $this->call('make:policy', [
            "name" => "{$name}Policy",
            "-m" => $name,
        ]);

        $this->call('make:moduletests', [
            "name" => $name,
        ]);
    }
}
