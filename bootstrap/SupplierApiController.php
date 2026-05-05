<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierApiController extends Controller
{
    /**
     * Display a listing of suppliers formatted for TallStackUI Select.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Filter by the company associated with the user's branch
        $suppliers = Supplier::where('company_id', $user->branch->company_id)
            ->where('supplier_status', 'active')
            ->get(['id', 'supp_name'])
            ->map(fn ($supplier) => [
                'label' => $supplier->supp_name,
                'value' => $supplier->id,
            ]);

        return response()->json($suppliers);
    }
}
