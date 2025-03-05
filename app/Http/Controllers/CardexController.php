<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\RequisitionType;
use App\Models\Item;
use App\Models\PriceLevel;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Signatories;
use App\Models\Receiving;
use App\Models\cardex;
use  App\Models\Location;


class CardexController extends Controller
{
    //

    public function viewItem()
    {
        dd($request);
        $cardex = Cardex::with('item', 'branch', 'receiving')->where('item_id', 1)->get();
        return view('layouts.cardex', compact('cardex'));
    }

    public function getCardexData($itemCode)
    {
        $item = Item::where('item_code', $itemCode)->first();
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $totalIn = Cardex::where('item_id', $item->id)
            ->where('status', 'final')->where('source_branch_id', Auth::user()->branch_id)
            ->sum('qty_in');

        $totalOut = Cardex::where('item_id', $item->id)
            ->where('status', 'final')->where('source_branch_id', Auth::user()->branch_id)
            ->sum('qty_out');

        $totalIn = $totalIn ?: 0;
        $totalOut = $totalOut ?: 0;
        $totalBalance = $totalIn - $totalOut;
        $location = Location::where('item_id', $item->id)->where('branch_id', Auth::user()->branch_id)->first();
        $price = PriceLevel::where('items_id', $item->id)->where('branch_id', Auth::user()->branch_id)->where('price_type', 'SRP')->first();
        if (!$price) {
            $price = PriceLevel::where('items_id', $item->id)->where('price_type', 'SRP')->first();
        }
        $cardexData = Cardex::where('item_id', $item->id)->where('status', 'final')->where('source_branch_id', Auth::user()->branch_id)->get();
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
