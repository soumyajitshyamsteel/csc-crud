<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'iso2',
        'country_id',
    ];
    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function cities(){
        return $this->hasMany(City::class);
    }
}
