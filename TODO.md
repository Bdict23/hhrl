# Task: Display Total Receiving Amount column for each Purchase Order in Banquet Liquidation Create

## Steps to Complete (Approved Plan)
- [x] **Step 1**: Update `app/Livewire/Banquet/LiquidationCreate.php`
  - In `getEventInformation()`: Compute `total_received_amount` for each PO by summing `receivings->receive_amount`.
  - Expose via property or accessor. ✅

- [x] **Step 2**: Update `resources/views/livewire/banquet/liquidation-create.blade.php`
  - Fill vacant Amount `<td>` with formatted total (₱{{ number_format(...) }} ).
  - Optional: Add tooltip/header clarity. ✅

- [ ] **Step 3**: Test changes
  - Navigate to Liquidation Create, select event with POs.
  - Verify Amount column shows correct sums (0 if no receiving, sum if multiple).
  - Check footer total if updated.

- [x] **Step 4**: Complete task
  - Run `attempt_completion`.

**Status**: Steps 1-2 complete. Ready for testing/completion. ✅

