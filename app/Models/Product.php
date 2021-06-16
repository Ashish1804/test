<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category(){

        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function cart(){

        return $this->hasMany('App\Models\Cart', 'product_id', 'id');
    }
}
