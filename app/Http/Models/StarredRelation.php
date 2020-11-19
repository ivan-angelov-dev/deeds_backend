<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class StarredRelation extends Model
{
    protected $table = 't_starred_relation';
    public $timestamps = false;

    public function starred_user() {
        return $this->hasOne(User::class, 'id', 'starred_user_id');
    }

}
