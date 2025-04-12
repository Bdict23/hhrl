<!-- resources/views/components/add-cost-modal.blade.php -->
<div>
    <!-- Modal -->
    <div wire:ignore.self 
         class="modal fade {{ $showModal ? 'show d-block' : '' }}" 
         tabindex="-1" 
         role="dialog" 
         aria-hidden="{{ $showModal ? 'false' : 'true' }}"
         style="{{ $showModal ? 'background: rgba(0,0,0,0.5); display: block;' : 'display: none;' }}">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Add Cost for {{ $itemName ?? 'Item' }}
                    </h5>
                    <button type="button" 
                            class="btn-close" 
                            wire:click="closeModal"
                            aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveCost">
                        <div class="mb-3">
                            <label for="cost" class="form-label">Cost Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="cost" 
                                       wire:model="cost"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                            @error('cost') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supplierId" class="form-label">Supplier</label>
                            <select class="form-select" 
                                    id="supplierId" 
                                    wire:model="supplierId"
                                    required>
                                <option value="">Select Supplier</option>
                                @foreach(\App\Models\Supplier::all() as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->supp_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplierId') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="costDate" class="form-label">Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="costDate" 
                                   wire:model="costDate"
                                   max="{{ now()->format('Y-m-d') }}"
                                   required>
                            @error('costDate') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" 
                                    class="btn btn-secondary"
                                    wire:click="closeModal">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled">
                                <span wire:loading wire:target="saveCost">
                                    Saving...
                                </span>
                                <span wire:loading.remove wire:target="saveCost">
                                    Save Cost
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>