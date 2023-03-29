<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            'name'=> $this->name,
            'iso3'=> $this->iso3,
            'capital'=> $this->capital,
            'currency'=> $this->currency,
            'nationality'=> $this->nationality,
        ];
    }
}
