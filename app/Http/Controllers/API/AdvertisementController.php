<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    public function advertisements()
    {
        $ads = Advertisement::where('status','1' )->get();
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Advertisements",
            "data" => $ads
        ], 200);
    }

    public function advertisement($id)
    {
        $ad = Advertisement::where('id',$id )->first();
        return response()->json([
            "ReturnCode" => 1,
            "ReturnMessage" => "Advertisement fetched sucessfully",
            "data" => $ad
        ], 200);
    }
}
