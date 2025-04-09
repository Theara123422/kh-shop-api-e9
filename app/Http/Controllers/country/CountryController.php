<?php

namespace App\Http\Controllers\country;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    protected $data;

    public function __construct(){
        $this->data = json_decode(Storage::get('./public/countries.json'), true);
    }

    /**
     * Summary of getCountries
     * @return mixed|\Illuminate\Http\JsonResponse
     * @group Country
     */
    public function getCountries(){
        return response()->json(array_keys($this->data));
    }

    /**
     * Summary of getCities
     * @param mixed $country
     * @return mixed|\Illuminate\Http\JsonResponse
     * @group city
     */
    public function getCities($country)
    {
        return response()->json($this->data[$country] ?? []);
    }
}
