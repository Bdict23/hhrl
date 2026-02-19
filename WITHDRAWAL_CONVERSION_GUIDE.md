# Withdrawal Unit Conversion - Clean Implementation Guide

## Overview
This implementation provides automatic unit conversion for withdrawal quantities while keeping the original request as a reference.

## How It Works

### When Production Order is Selected:

**Example Scenario:**
- Recipe requires: **1250 grams** of Rice
- Item base unit: **Kilogram (kg)**
- Available conversions: kg ↔ grams (1 kg = 1000 g)

**What Happens:**

1. **Initial Display (Base Unit = kg):**
   - REQ. QTY: `1250 (grams)` ← **STAYS FIXED as reference**
   - WIT. QTY: `1.25` ← converted to base unit (kg)
   - AVAIL: `10` kg
   - BAL: `15` kg

2. **User Changes Unit to Grams:**
   - REQ. QTY: `1250 (grams)` ← **UNCHANGED**
   - WIT. QTY: `1250` ← auto-converted to grams
   - AVAIL: `10000` ← auto-converted to grams
   - BAL: `15000` ← auto-converted to grams

3. **User Changes Unit Back to kg:**
   - REQ. QTY: `1250 (grams)` ← **UNCHANGED**
   - WIT. QTY: `1.25` ← auto-reverts to kg
   - AVAIL: `10` ← auto-reverts to kg
   - BAL: `15` ← auto-reverts to kg

## Data Structure

### Stored in `selectedItems` Array:

```php
[
    // Base quantities (never change, always in item's base UOM)
    'base_total_balance' => 15,        // 15 kg
    'base_total_available' => 10,      // 10 kg
    'requested_qty_base' => 1.25,      // 1.25 kg
    
    // Display quantities (auto-convert based on selected unit)
    'total_balance' => 15000,          // shows as grams when unit = grams
    'total_available' => 10000,        // shows as grams when unit = grams
    'requested_qty' => 1250,           // shows as grams when unit = grams
    
    // Reference (never changes)
    'request_qty' => '1250 (grams)',   // original request from recipe
    
    // Selected unit
    'uom' => 2,                        // current selected unit ID
    'base_uom_id' => 1,                // item's base unit ID (kg)
]
```

## Key Functions

### 1. `updateItemUnit()`
Called when user changes the unit dropdown.
- Converts `balance`, `available`, and `requested_qty` from base to selected unit
- **Never touches** `request_qty` (stays as reference)
- Recalculates `total` based on base unit

### 2. `updateRequestedQty()`
Called when user types in WIT. QTY field.
- Validates against available balance (in current unit)
- Converts entered qty to base unit for storage
- Updates total cost based on base unit qty

### 3. `store()`
Saves withdrawal to database.
- Uses `requested_qty_base` for cardex (always in base unit)
- Ensures consistent database storage regardless of display unit

## Conversion Logic

### Formula:
```
Display Value = Base Value × Conversion Factor
Base Value = Display Value ÷ Conversion Factor
```

### Example:
- Base unit: kg (factor = 1)
- Target unit: grams (factor = 1000)
- Base qty: 1.25 kg
- Display qty: 1.25 × 1000 = 1250 grams

## Benefits

✅ **User-Friendly:** Change units anytime without data loss
✅ **Accurate:** All calculations use base unit (no rounding errors)
✅ **Reference Maintained:** Original request qty stays visible
✅ **Database Consistency:** Cardex always stores base unit qty
✅ **Clean Code:** Separated concerns, clear function names

## Testing Checklist

- [ ] Select production order → verify items display in base unit
- [ ] Change unit to grams → verify WIT/AVAIL/BAL auto-convert
- [ ] REQ. QTY stays unchanged (shows original 1250 grams)
- [ ] Type withdrawal qty → verify validation against available
- [ ] Change unit again → verify WIT qty converts correctly
- [ ] Save withdrawal → verify cardex stores base unit qty
- [ ] Cost total remains accurate regardless of unit changes

## Important Notes

1. **Base Unit = Item's Default UOM** (defined in item settings)
2. **Conversion Factor** comes from UnitConversion table
3. **REQ. QTY** is read-only display (from production order recipe)
4. **WIT. QTY** is editable and auto-converts with unit changes
5. **All Database Storage** uses base unit quantities
