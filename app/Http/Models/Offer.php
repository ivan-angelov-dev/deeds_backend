<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 't_offer';
    public $timestamps = false;

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');

    }

    public function interested_users()
    {

        return $this->hasManyThrough(
            User::class,
            InterestedUserRelation::class,
            'offer_id',
            'id',
            'id',
            'user_id');

    }
}
