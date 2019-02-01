<?php
namespace App\Services\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\User;

class FavoriteService
{
    public function add(int $userId, int $advertId)
    {
        $user = $this->getUser($userId);
        $advert = $this->getAdvert($advertId);
        $user->addToFavorites($advert->id);
    }

    public function remove($userId, $advertId): void
    {
        $user = $this->getUser($userId);
        $advert = $this->getAdvert($advertId);
        $user->removeFromFavorites($advert->id);
    }

    /**
     * @param $userId
     * @return User
     */
    private function getUser($userId): User
    {
        return User::query()->findOrFail($userId);
    }

    /**
     * @param $advertId
     * @return Advert
     */
    private function getAdvert($advertId): Advert
    {
        return Advert::query()->findOrFail($advertId);
    }
}