<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class PassportRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ClientRepository $clients)
    {
        ;
        $this->createPasswordClient($clients);

    }

    protected function createPasswordClient(ClientRepository $clients)
    {
        $provider = ["users", "admins"];
        $name = ["Users", "Admins"];

        for ($i = 0; $i < 2; $i++) {
            $client = $clients->createPasswordGrantClient(
                null, $name[$i], 'http://localhost', $provider[$i]
            );

            $this->outputClientDetails($client);
        }

        $this->info('Password grant client created successfully.');

    }

    protected function outputClientDetails(Client $client)
    {
        if (Passport::$hashesClientSecrets) {
            $this->line('<comment>Here is your new client secret. This is the only time it will be shown so don\'t lose it!</comment>');
            $this->line('');
        }

        $this->line('<comment>Client ID:</comment> ' . $client->getKey());
        $this->line('<comment>Client secret:</comment> ' . $client->plainSecret);
    }
}
