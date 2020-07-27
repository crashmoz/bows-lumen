<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    public $incrementing = false;

    public $timestamp = false;

    protected $hidden = ['category_id'];

    public function category()
	{
		return $this->belongsTo('App\Category');
	}
}