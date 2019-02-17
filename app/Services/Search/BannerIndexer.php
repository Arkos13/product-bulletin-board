<?php

namespace App\Services\Search;

use App\Entity\Banner\Banner;
use App\Entity\Region;
use Elasticsearch\Client;

class BannerIndexer
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function clear()
    {
        $this->client->deleteByQuery([
            'index' => 'banners',
            'type' => 'banner',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ]);
    }

    /**
     * @param Banner $banner
     */
    public function index(Banner $banner)
    {
        $regionIds = [0];
        if ($banner->region) {
            $regionIds = [$banner->region->id];
            $childrenIds = $regionIds;
            while ($childrenIds = Region::whereIn('parent_id', $childrenIds)->pluck('id')->toArray()) {
                $regionIds = array_merge($regionIds, $childrenIds);
            }
        }
        $this->client->index([
            'index' => 'banners',
            'type' => 'banner',
            'id' => $banner->id,
            'body' => [
                'id' => $banner->id,
                'status' => $banner->status,
                'format' => $banner->format,
                'categories' => array_merge(
                    [$banner->category->id],
                    $banner->category->descendants()->pluck('id')->toArray()
                ),
                'regions' => $regionIds,
            ],
        ]);
    }

    /**
     * @param Banner $banner
     */
    public function remove(Banner $banner)
    {
        $this->client->delete([
            'index' => 'banners',
            'type' => 'banner',
            'id' => $banner->id,
        ]);
    }
}