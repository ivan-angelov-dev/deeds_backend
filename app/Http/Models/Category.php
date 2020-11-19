<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 't_category';
    public $timestamps = false;

    public function offers() {
        return $this->hasMany(Offer::class, 'category_id', 'id');
    }
}
