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

    public function getCountries(){
        return response()->json(array_keys($this->data));
    }
    public function getCities($country)
    {
        return response()->json($this->data[$country] ?? []);
    }
}
