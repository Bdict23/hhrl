<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\requisitionInfos;
use App\Models\requisitionDetails;
use App\Models\supplier;
use App\Models\employees;
use App\Models\Branch;
use App\Models\requisitionTypes;
use App\Models\items;
use App\Models\priceLevel;
use App\Models\statuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\signatories;
use App\Models\receiving;
use App\Models\cardex;
use  App\Models\locations;


class CardexController extends Controller
{
    //

    public function viewItem()
    {
        dd($request);
        $cardex = cardex::with('item', 'branch', 'receiving')->where('item_id', 1)->get();
        return view('layouts.cardex', compact('cardex'));
    }

    public function getCardexData($itemCode)
    {
        $item = items::where('item_code', $itemCode)->first();
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $totalIn = cardex::where('item_id', $item->id)
            ->where('status', 'final')->where('source_branch_id', Auth::user()->branch_id)
            ->sum('qty_in');

        $totalOut = cardex::where('item_id', $item->id)
            ->where('status', 'final')->where('source_branch_id', Auth::user()->branch_id)
            ->sum('qty_out');

        $totalIn = $totalIn ?: 0;
        $totalOut = $totalOut ?: 0;
        $totalBalance = $totalIn - $totalOut;
        $location = locations::where('item_id', $item->id)->where('branch_id', Auth::user()->branch_id)->first(); 
        $price = priceLevel::where('items_id', $item->id)->where('branch_id', Auth::user()->branch_id)->where('price_type', 'SRP')->first();
        if (!$price) {
            $price = priceLevel::where('items_id', $item->id)->where('price_type', 'SRP')->first();
        }
        $cardexData = cardex::where('item_id', $item->id)->where('status', 'final')->where('source_branch_id', Auth::user()->branch_id)->get();
        $response = [
            'description' => $item->item_description,
            'location' => $location ? $location->location_name : 'N/A',
            'price' => $price ? $price->amount : '0.00',
            'total_balance' => $totalBalance,
            'cardex' => $cardexData
        ];
      
         return response()->json($response);
    }

}
