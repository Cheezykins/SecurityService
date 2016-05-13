<?php

namespace App\Console\Commands;

use App\Helpers\IncrementalString;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class GenerateHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:generate {amount=1000000 : The number of additional hashes to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates additional hashes for use as keys';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hashes = Redis::get('hash_max');

        if ($hashes === null) {
            $start = '';
        } else {
            $start = $hashes;
        }

        $hasher = new IncrementalString($start);

        $hashCount = $this->argument('amount');

        $bar = $this->output->createProgressBar($hashCount);

        $this->info("Generating {$hashCount} hashes starting from {$hasher}".PHP_EOL);

        for ($i = 0; $i < $hashCount; $i++) {
            Redis::sadd('hashes', $hasher);
            $hasher->increment();
            if ($i % 1000 == 0) {
                $bar->setProgress($i);
            }
        }
        $bar->setProgress($hashCount);
        $this->line(PHP_EOL);

        $this->info("Hashes generated, new highest hash is {$hasher}".PHP_EOL);

        Redis::set('hash_max', $hasher);
    }
}
