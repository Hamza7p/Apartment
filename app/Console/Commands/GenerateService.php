<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new Service class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int    
    {
        $name = $this->argument('name');
        $servicePath = app_path('Http/Services/' . $name . 'Service.php');

        if (file_exists($servicePath)) {
            $this->error('Service already exists!');
            return 0;
        }
        $stub = file_get_contents(base_path().'/stubs/service.stub');
        $serviceContent = str_replace('{{name}}', $name, $stub);
        file_put_contents($servicePath, $serviceContent);

        $this->info('Service created successfully.');
        return Command::SUCCESS;
    }
}
