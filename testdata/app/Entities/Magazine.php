<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Magazine extends Model
{
    protected $table = 'magazines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany('App\Entities\Customer', 'magazine_customer');
    }
}
