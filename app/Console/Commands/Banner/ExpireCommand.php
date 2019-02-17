<?php

namespace App\Console\Commands\Banner;

use App\Entity\Banner\Banner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Predis\Client;

class ExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banner:expire';

    /**
     * @var Client
    */
    private $client;

    /**
     * Create a new command instance.
     * @param Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $success = true;
        foreach (Banner::active()->whereRaw("`limit` - views < 100")->with("user")->cursor() as $banner) {
            /** @var Banner $banner */
            $key = 'banner_notify_' . $banner->id;
            if ($this->client->get($key)) {
                continue;
            }
            Mail::to($banner->user->email)->queue(new BannerExpiresSoonMail($banner));
            $this->client->set($key, true);
        }
        return $success;
    }
}
