<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierApiController extends Controller
{
    /**
     * Display a listing of suppliers filtered by company ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer',
        ]);

        $suppliers = Supplier::where('company_id', $request->company_id)
            ->where('supplier_status', 'ACTIVE')
            ->get();

        return response()->json($suppliers);
    }
}
