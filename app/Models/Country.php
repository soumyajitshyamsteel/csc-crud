<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'name',
        'slug',
        'iso3',
        'numeric_code',
        'iso2',
        'phonecode',
        'capital',
        'language',
        'native',
        'currency',
        'emoji',
        'emojiU',
        'status',
        'wikiDataId',
        'nationality'
    ];

    public function states(){
        return $this->hasMany(State::class);
    }
    public function cities(){
        return $this->hasMany(City::class);
    }
}
