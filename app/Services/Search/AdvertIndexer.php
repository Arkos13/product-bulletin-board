<?php

namespace App\Services\Search;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Adverts\Advert\Value;
use App\Entity\Region;
use Elasticsearch\Client;

class AdvertIndexer
{
    /**
     * @var Client
    */
    private $client;

    /**
     * AdvertIndexer constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function clear()
    {
        $this->client->deleteByQuery([
            'index' => 'app',
            'type' => 'advert',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ]);
    }

    /**
     * @param Advert $advert
     */
    public function index(Advert $advert)
    {
        $regions = [];
        if ($region = $advert->region) {
            /** @var Region $region */
            do {
                $regions[] = $region->id;
            } while ($region =  $region->parent);
        }

        $this->client->index([
            'index' => 'app',
            'type' => 'advert',
            'id' => $advert->id,
            'body' => [
                'id' => $advert->id,
                'published_at' => $advert->published_at ? $advert->published_at->format(DATE_ATOM) : null,
                'title' => $advert->title,
                'content' => $advert->content,
                'price' => $advert->price,
                'status' => $advert->status,
                'categories' => array_merge(
                    [$advert->category->id],
                    $advert->category->ancestors()->pluck('id')->toArray()
                ),
                'regions' => $regions,
                'values' => array_map(function (Value $value) {
                    return [
                        'attribute' => $value->attribute_id,
                        'value_string' => (string)$value->value,
                        'value_int' => (int)$value->value,
                    ];
                }, $advert->values()->getModels()),
            ]
        ]);
    }

    /**
     * @param Advert $advert
     */
    public function remove(Advert $advert)
    {
        $this->client->delete([
            'index' => 'app',
            'type' => 'advert',
            'id' => $advert->id,
        ]);
    }
}