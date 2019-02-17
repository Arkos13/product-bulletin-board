<?php

namespace App\Console\Commands\Search;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Banner\Banner;
use App\Services\Search\AdvertIndexer;
use App\Services\Search\BannerIndexer;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    private $adverts;

    private $banners;

    /**
     * Create a new command instance.
     * @param AdvertIndexer $adverts
     * @param BannerIndexer $bannerIndexer
     * @return void
     */
    public function __construct(AdvertIndexer $adverts, BannerIndexer $bannerIndexer)
    {
        parent::__construct();
        $this->adverts = $adverts;
        $this->banners = $bannerIndexer;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $this->adverts->clear();
        foreach (Advert::active()->orderBy('id')->cursor() as $advert) {
            /** @var Advert $advert */
            $this->adverts->index($advert);
        }

        $this->banners->clear();
        foreach (Banner::active()->orderBy('id')->cursor() as $banner) {
            $this->banners->index($banner);
        }

        return true;
    }
}
