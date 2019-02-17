<?php

namespace App\Console\Commands\Advert;

use App\Entity\Adverts\Advert\Advert;
use App\Services\Adverts\AdvertService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'advert:expire';

    /**
     * @var AdvertService
    */
   private $advertService;

    /**
     * Create a new command instance.
     *
     * @param AdvertService $service
     */
    public function __construct(AdvertService $service)
    {
        parent::__construct();
        $this->advertService = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $success = true;

        foreach (Advert::active()->where('expired_at', '<', Carbon::now())->cursor() as $advert) {
            /** @var Advert $advert */
            try {
                $this->advertService->expire($advert);
            } catch (\DomainException $e) {
                $this->error($e->getMessage());
                $success = false;
            }
        }
        return $success;
    }
}
