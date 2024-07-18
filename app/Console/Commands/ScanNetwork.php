<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScanNetwork extends Command
{
    protected $signature = 'network:scan {ip}';
    protected $description = 'Scan the local network for a given IP address';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $ipAddress = $this->argument('ip');
        $output = shell_exec("nmap -sP " . escapeshellarg($ipAddress));
        $this->info($output);
    }
}
