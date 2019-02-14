<?php

namespace App\Console\Commands\Search;

use App\Entity\Adverts\Advert\Advert;
use App\Services\Search\AdvertIndexer;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    private $indexer;

    /**
     * Create a new command instance.
     * @param AdvertIndexer $indexer
     * @return void
     */
    public function __construct(AdvertIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $this->indexer->clear();
        foreach (Advert::active()->orderBy('id')->cursor() as $advert) {
            /** @var Advert $advert */
            $this->indexer->index($advert);
        }
        return true;
    }
}
