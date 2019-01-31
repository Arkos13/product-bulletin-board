<?php

namespace App\Services\Adverts;


use App\Entity\Adverts\Advert\Advert;
use App\Entity\Adverts\Attribute;
use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Entity\User;
use App\Http\Requests\Adverts\AttributesRequest;
use App\Http\Requests\Adverts\CreateRequest;
use App\Http\Requests\Adverts\PhotoRequest;
use App\Http\Requests\Adverts\RejectRequest;
use App\Http\Requests\Adverts\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvertService
{
    /**
     * @param int $userId
     * @param int $categoryId
     * @param int|null $regionId
     * @param CreateRequest $request
     * @return Advert
     */
    public function create(int $userId, int $categoryId, ?int $regionId, CreateRequest $request): Advert
    {
        /** @var User $user */
        $user = User::query()->findOrFail($userId);

        /** @var Category $category */
        $category = Category::query()->findOrFail($categoryId);

        /** @var Region $region */
        $region = $regionId ? Region::query()->findOrFail($regionId) : null;

        return DB::transaction(function () use ($request, $user, $category, $region) {
            /** @var Advert $advert */
            $advert = Advert::query()->make([
                'title' => $request['title'],
                'content' => $request['content'],
                'price' => $request['price'],
                'address' => $request['address'],
                'status' => Advert::STATUS_DRAFT
            ]);

            $advert->user()->associate($user);
            $advert->category()->associate($category);
            $advert->region()->associate($region);

            $advert->saveOrFail();

            foreach ($category->allAttributes() as $attribute) {
                /** @var Attribute $attribute */
                $value = $request['attributes'][$attribute->id] ?? null;
                if (!empty($value)) {
                    $advert->values()->create([
                        'attribute_id' => $attribute->id,
                        'value' => $value,
                    ]);
                }
            }
            return $advert;
        });
    }

    /**
     * @param int $id
     * @param PhotoRequest $request
     */
    public function addPhotos(int $id, PhotoRequest $request)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);

        DB::transaction(function () use ($request, $advert) {
            foreach ($request['files'] as $file) {
                $advert->photos()->create([
                    'file' => $file->store('adverts')
                ]);
            }
            $advert->update();
        });
    }

    /**
     * @param $id
     * @param UpdateRequest $request
     */
    public function edit($id, UpdateRequest $request): void
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);

        $advert->update($request->only([
            'title',
            'content',
            'price',
            'address',
        ]));
    }

    /**
     * @param int $id
     */
    public function sendToModeration(int $id)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);
        $advert->sendToModeration();
    }

    /**
     * @param int $id
     */
    public function moderate(int $id)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);
        $advert->moderate(Carbon::now());
    }

    /**
     * @param int $id
     * @param RejectRequest $request
     */
    public function reject(int $id, RejectRequest $request)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);
        $advert->reject($request['reason']);
    }

    /**
     * @param Advert $advert
     */
    public function expire(Advert $advert)
    {
        $advert->expire();
    }

    /**
     * @param int $id
     */
    public function close(int $id)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);
        $advert->close();
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function remove(int $id)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);
        $advert->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    private function getAdvert(int $id): Advert
    {
        return Advert::query()->findOrFail($id);
    }

    public function editAttributes(int $id, AttributesRequest $request)
    {
        /** @var Advert $advert */
        $advert = $this->getAdvert($id);
        DB::transaction(function() use ($request, $advert) {
            $advert->values()->delete();
            foreach ($advert->category->allAttributes() as $attribute) {
                $value = $request['attributes'][$attribute->id] ?? null;
                if (!empty($value)) {
                    $advert->values()->create([
                        'attribute_id' => $attribute->id,
                        'value' => $value,
                    ]);
                }
            }
            $advert->update();
        });
    }
}