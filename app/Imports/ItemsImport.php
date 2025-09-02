<?php

namespace App\Imports;
use App\Models\Item;
use App\Models\Category;
use App\Models\Classification;
use App\Models\PriceLevel;
use App\Models\Brand;
use App\Models\UOM;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // dd($row);
        $company_id = auth()->user()->branch->company_id;
        $category = Category::firstOrCreate(
            [
                'category_name' => $row['category'],
                'category_type' => 'ITEM',
                'company_id' => $company_id
            ]
        );

        $classification = Classification::where([
            ['classification_name', '=', $row['classification']],
            ['company_id', '=', $company_id]
        ])->first();

        if (!$classification) {
            $classification = Classification::create([
            'classification_name' => $row['classification'],
            'company_id' => $company_id
            ]);
        }
        $parentId = $classification->id;
        $subClassification = null;
        if (!empty($row['sub_classification'])) {
            $subClassification = Classification::firstOrCreate(
                [
                    'classification_name' => $row['sub_classification'],
                    'class_parent' => $parentId,
                    'company_id' => $company_id
                ]
            );
        }
       

        $unit = null;
        if (!empty($row['unit'])) {
            $unit = UOM::firstOrCreate(
            [
                'unit_symbol' => $row['unit'],
                'company_id' => $company_id
            ]
            );
        }

        $brand = null;
        if (!empty($row['brand'])) {
            $brand = Brand::firstOrCreate(
                [
                    'brand_name' => $row['brand'],
                    'company_id' => $company_id
                ]
            );
        }

        

        // Check if item already exists by item_code or item_description
        $existingItem = Item::where('item_description', '' . $row['item'] . '' . $row['description'])
            ->where('company_id', $company_id)
            ->first();

        if ($existingItem) {
            return null; // Ignore insertion if item exists
        }

        $item = new Item([
            'item_code'         => strtoupper(substr($row['item'], 0, 3)) . rand(100, 999),
            'item_description'  => '' . $row['item'] . '' . $row['description'],
            'brand_id'          => $brand->id ?? null,
            'item_barcode'      => $row['barcode'],
            'uom_id'            => $unit->id ?? null,
            'classification_id' => $classification->id ?? null,
            'sub_class_id'      => $subClassification->id ?? null,
            'category_id'       => $category->id ?? null,
            'item_status'       => 'ACTIVE',
            'created_by'        => auth()->user()->emp_id ?? 1,
            'company_id'        => auth()->user()->branch->company_id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        $item->save();

        PriceLevel::create([
            'item_id'     => $item->id,
            'price_type'  => 'COST',
            'amount'      => '1.00',
            'branch_id'   => auth()->user()->branch->id,
            'company_id'  => $company_id
        ]);

        return $item;

    }
}