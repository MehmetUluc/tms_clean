<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StartInertiaSSR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inertia:start-ssr {--port=13714}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Inertia SSR server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Building SSR bundle...');
        $this->call('inertia:build-ssr');
        
        $port = $this->option('port');
        $this->info("Starting SSR server on port {$port}...");
        
        $serverFile = base_path('bootstrap/ssr/ssr.mjs');
        $process = Process::forever()->env(['PORT' => $port])->start("node {$serverFile}");
        
        $this->info("SSR server started successfully.");
        $this->info("Use APP_SSR_PORT={$port} in your .env file.");
        
        while ($process->running()) {
            if ($output = $process->latestOutput()) {
                $this->line($output);
            }
            
            sleep(1);
        }
        
        $this->error('SSR server stopped unexpectedly!');
        
        return 0;
    }
}