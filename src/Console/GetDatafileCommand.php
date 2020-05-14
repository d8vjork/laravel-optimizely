<?php

namespace D8vjork\LaravelOptimizely\Console;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GetDatafileCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'optimizely:datafile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get your features datafile from Optimizely\'s CDN.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = config('optimizely.key');

        if (! $key) {
            return $this->error('You must set a SDK key for get your datafile from Optimizely.');
        }

        $response = (new Client())->get("https://cdn.optimizely.com/datafiles/${key}.json");

        if (file_put_contents(config('optimizely.datafile_path'), $response->getBody()->getContents()) !== false) {
            return $this->info('Success! Features file created!');
        }
    }
}
