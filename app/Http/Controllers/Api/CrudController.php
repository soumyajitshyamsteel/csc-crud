<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\CountryResource;
use Illuminate\Support\Facades\Validator;

class CrudController extends Controller
{
    public function index(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'location' => 'string|required|in:countries,states,cities',
            "limit" => 'sometimes|integer|min:10|max:1000',
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false, 422, $validateData->errors()->all(), []);
        }
        $message = $request->location . ' found successfully';
        switch ($request->location) {
            case 'countries':
                $data = CountryResource::collection(Country::all());
                break;
            case 'states':
                $data = StateResource::collection(State::all());
                break;
            case 'cities':
                if ($request->limit) {
                    $cities = City::limit($request->limit)->get();
                } else {
                    $cities = City::inRandomOrder()->limit(1000)->get();
                }
                $data = CityResource::collection($cities);
                break;
            default:
                $data = [];
                $message = 'No data found';
                break;
        }
        return $this->responseJson(true, 200, $message, $data);
    }

    public function getCitiesByCountry(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'country_id' => 'required|integer|exists:countries,id',
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false, 422, $validateData->errors()->all(), []);
        }

        $cities = City::where('country_id', $request->country_id)->get();
        if ($cities->isNotEmpty()) {
            $message = 'Cities data found successfully';
        }
        return $this->responseJson(true, 200, $message ?? 'No Data found', CityResource::collection($cities));

    }

    public function findType(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'type' => 'required|string|in:country,state,city',
            'country_id' => 'required_if:type,country|integer|exists:countries,id',
            'state_id' => 'required_if:type,state|integer|exists:states,id',
            'city_id' => 'required_if:type,city|integer|exists:cities,id',
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false, 422, $validateData->errors()->all(), []);
        }
        $message = $request->type . ' data found';
        switch ($request->type) {
            case 'country':
                $data = new CountryResource(Country::find($request->country_id));
                break;
            case 'state':
                $data = new StateResource(State::find($request->state_id));
                break;
            case 'city':
                $data = new CityResource(City::find($request->city_id));
                break;
            default:
                $data = [];
                break;
        }
        if (empty($data)) {
            $message = 'No Data found';
        }
        return $this->responseJson(true, 200, $message, $data);
    }
    public function addType(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'type' => 'required|string|in:country,state,city',
            'country_name' => 'required_if:type,country|string|min:3|unique:countries,name',
            'state_name' => 'required_if:type,state|string|min:3|unique:states,name',
            'city_name' => 'required_if:type,city|string|min:3|unique:cities,name',
            'country_id' => Rule::requiredIf($request->get('type') != "country").'|integer|exists:countries,id',
            'state_id' => 'required_if:type,city|integer|exists:states,id',
            'phonecode' => 'required_if:type,country|alpha_num',
            'capital' => 'required_if:type,country|string|min:3|unique:countries,capital',
            'iso3' => 'sometimes|string',
            'iso2' => 'sometimes|string',
            'currency' => 'sometimes|string',
            'nationality' => 'sometimes|string',
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false, 422, $validateData->errors()->all(), []);
        }
        DB::beginTransaction();
        try {
            $request->merge(['status' => true]);
            switch ($request->type) {
                case 'country':
                    $request->merge(['name' => $request->country_name]);
                    $isCountryCreated = Country::create($request->only(['name', 'phonecode', 'capital', 'iso3', 'currency', 'nationality', 'status']));
                    if ($isCountryCreated) {
                        DB::commit();
                        $data = new CountryResource($isCountryCreated);
                    }
                    break;
                case 'state':
                    $request->merge(['name' => $request->state_name]);
                    $isStateCreated = State::create($request->only(['name','country_id','country_code','iso2']));
                    if ($isStateCreated) {
                        DB::commit();
                        $data = new StateResource($isStateCreated);
                    }
                    break;
                case 'city':
                    $request->merge(['name' => $request->city_name]);
                    $isCityCreated =City::create($request->only(['name','country_id','state_id', 'iso2']));
                    if ($isCityCreated) {
                        DB::commit();
                        $data = new CityResource($isCityCreated);
                    }
                    break;
                default:
                    $data = [];
                    break;
            }
            return $this->responseJson(true, 200, $request->type.' created successfully', $data);

        } catch (\Exception$e) {
            DB::rollBack();
            return $this->responseJson(false, 500, $e->getMessage(), []);
        }
    }

    public function editType(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'type' => 'required|string|in:country,state,city',
            'country_id' => 'required_if:type,country|integer|exists:countries,id',
            'state_id' => 'required_if:type,state|integer|exists:states,id',
            'city_id' => 'required_if:type,city|integer|exists:cities,id',
            'country_name' => 'required_if:type,country|string|min:3|unique:countries,name,' . $request->country_id,
            'state_name' => 'required_if:type,state|string|min:3|unique:states,name,' . $request->state_id,
            'city_name' => 'required_if:type,city|string|min:3|unique:cities,name,' . $request->city_id,
            'phonecode' => 'sometimes|alpha_num',
            'capital' => 'sometimes|string|min:3|unique:countries,capital,' . $request->country_id,
            'iso3' => 'sometimes|string',
            'iso2' => 'sometimes|string',
            'currency' => 'sometimes_if:type,country',
            'nationality' => 'sometimes|string',
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false, 422, $validateData->errors()->all(), []);
        }
        DB::beginTransaction();
        try {
            switch ($request->type) {
                case 'country':
                    $request->merge(['name' => $request->country_name]);
                    $isCountry = Country::find($request->country_id);
                    $isCountryUpdated = $isCountry->update($request->only(['name', 'phonecode', 'capital', 'iso3', 'currency', 'nationality']));
                    if ($isCountryUpdated) {
                        DB::commit();
                        $data = new CountryResource($isCountry);
                    }
                    break;
                case 'state':
                    $request->merge(['name' => $request->state_name]);
                    $isState = State::find($request->state_id);
                    $isStateUpdated = $isState->update($request->only(['name', 'iso2']));
                    if ($isStateUpdated) {
                        DB::commit();
                        $data = new StateResource($isState);
                    }
                    break;
                case 'city':
                    $request->merge(['name' => $request->city_name]);
                    $isCity = City::find($request->city_id);
                    $isCityUpdated = $isCity->update($request->only(['name', 'iso2']));
                    if ($isCityUpdated) {
                        DB::commit();
                        $data = new CityResource($isCity);
                    }
                    break;
                default:
                    $data = [];
                    break;
            }

            return $this->responseJson(true, 200, 'Country updated successfully', $data);

        } catch (\Exception$e) {
            DB::rollBack();
            return $this->responseJson(false, 500, $e->getMessage(), []);
        }

    }

    public function deleteType(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'type' => 'required|string|in:country,state,city',
            'country_id' => 'required_if:type,country|integer|exists:countries,id',
            'state_id' => 'required_if:type,state|integer|exists:states,id',
            'city_id' => 'required_if:type,city|integer|exists:cities,id',
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false, 422, $validateData->errors()->all(), []);
        }
        try {
            $message = $request->type . ' deleted successfully';
            switch ($request->type) {
                case 'country':
                    $isCountry = Country::find($request->country_id);
                    if ($isCountry) {
                        $isCountry->states()->delete();
                        $isCountry->cities()->delete();
                        $isCountry->delete();
                    } else {
                        $message = 'No data found for deleting';
                    }
                    break;
                case 'state':
                    $isState = State::find($request->state_id);
                    if ($isState) {
                        $isState->cities()->delete();
                        $isState->delete();
                    } else {
                        $message = 'No data found for deleting';
                    }
                    break;
                case 'city':
                    $isCityDeleted = City::find($request->city_id)->delete();
                    if (!$isCityDeleted) {
                        $message = 'No data found for deleting';
                    }
                    break;
                default:
                    $message = 'No data found for deleting';
                    break;
            }
        } catch (\Exception$e) {
            return $this->responseJson(false, 500, $e->getMessage(), []);
        }
        return $this->responseJson(true, 200, $message, []);
    }
}
