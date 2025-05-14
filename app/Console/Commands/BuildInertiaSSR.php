<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class BuildInertiaSSR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inertia:build-ssr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build SSR bundle';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Building SSR bundle...');
        
        $process = Process::path(base_path())->run('npm run build:ssr');
        
        if ($process->successful()) {
            $this->info('SSR bundle built successfully.');
            return 0;
        }
        
        $this->error('Failed to build SSR bundle.');
        $this->error($process->errorOutput());
        return 1;
    }
}