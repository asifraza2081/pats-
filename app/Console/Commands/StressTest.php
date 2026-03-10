<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class StressTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stress {--requests=100} {--concurrency=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a heavy asynchronous load test on the local application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $totalRequests = (int) $this->option('requests');
        $concurrency = (int) $this->option('concurrency');
        
        $url = env('APP_URL', 'http://pats.test');
        $this->info("Starting Stress Test against {$url} ...");
        $this->info("Total Requests target: {$totalRequests} | Concurrency: {$concurrency}");

        $successes = 0;
        $failures = 0;
        $latencies = [];

        $startTime = microtime(true);
        $bar = $this->output->createProgressBar($totalRequests);
        $bar->start();

        // Calculate how many batches we need
        $batches = ceil($totalRequests / $concurrency);

        for ($b = 0; $b < $batches; $b++) {
            $batchSize = min($concurrency, $totalRequests - ($b * $concurrency));
            
            $startBatchTime = microtime(true);
            
            $responses = Http::pool(function (Pool $pool) use ($batchSize, $url) {
                for ($i = 0; $i < $batchSize; $i++) {
                    // Randomly select endpoints to hammer
                    $endpoints = ['/', '/projects', '/login', '/results'];
                    $randEndpoint = $endpoints[array_rand($endpoints)];
                    
                    $pool->as("req_{$i}")->timeout(10)->get($url . $randEndpoint);
                }
            });
            
            $endBatchTime = microtime(true);

            foreach ($responses as $response) {
                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $successes++;
                } else {
                    $failures++;
                }
            }
            
            // Approximate latency per batch to give a sense of load
            $latencies[] = ($endBatchTime - $startBatchTime) / $batchSize * 1000;
            $bar->advance($batchSize);
        }

        $bar->finish();
        $totalTime = microtime(true) - $startTime;

        $this->newLine(2);
        
        $avgLatency = count($latencies) > 0 ? array_sum($latencies) / count($latencies) : 0;
        
        $this->info("========================================");
        $this->info("STRESS TEST RESULTS");
        $this->info("========================================");
        $this->line("Total Time: " . number_format($totalTime, 2) . " seconds");
        $this->line("Requests/sec: " . number_format($totalRequests / $totalTime, 2));
        $this->line("Avg Batch Delay/Req: " . number_format($avgLatency, 2) . " ms");
        
        if ($failures === 0) {
            $this->info("✅ SUCCESSES: {$successes} | FAILURES: {$failures}");
            $this->info("Application handled the load perfectly without throwing 500/Timeout errors.");
        } else {
            $this->error("❌ SUCCESSES: {$successes} | FAILURES: {$failures}");
            $this->error("Application started dropping requests or timing out under load.");
        }
        
        return 0;
    }
}
