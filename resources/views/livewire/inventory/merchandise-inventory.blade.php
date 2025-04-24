<div class="content-fluid">
    @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card mt-3 mb-3">
        <div class="card-header p-2">
            <div class="row">
                <div class=" row col-md-12">
                    {{-- <div class="col-md-6">
                        <x-primary-button style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                           + Print Inventory
                        </x-primary-button>
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div> --}}
                    <div class="col-md-6">
                        <h5>Merchandise Inventory</h5>
                    </div>
                </div>

                <div class="col-md-6">
                
                </div>
            </div>
        </div>


        <div class="card-body">
            <div class="table-responsive-sm">
                <div class="d-flex justify-content-between mb-3">
                    <table class="table table-striped table-hover table-sm table-responsive">
                                <thead class="table-light me-3">
                                    <tr style="font-size: x-small;">
                                        @if ($avlBal)
                                            <th>BAL.</th>
                                        @endif
                                        @if ($avlQty)
                                            <th>AVAIL.</th>
                                        @endif
                                        @if ($totalReserved)
                                            <th>Reserved</th>
                                        @endif
                                        @if ($code)
                                            <th>SKU</th>
                                        @endif
                                        <th>NAME</th>
                                        @if ($uom)
                                            <th>UNIT</th>
                                        @endif
                                        @if ($category)
                                            <th>CATEGORY</th>
                                        @endif
                                        @if ($location)
                                            <th>LOCATION</th>
                                        @endif
                                        @if ($brand)
                                            <th>BRAND</th>
                                        @endif
                                        @if ($status)
                                            <th>STATUS</th>
                                        @endif
                                        @if ($classification)
                                            <th>CLASSIFICATION</th>
                                        @endif
                                        @if ($barcode)
                                            <th>BARCODE</th>
                                        @endif
                                       
                                        <th>COST</th>
                                      
                                        <th>
                                            ACTION
                                            <button type="button"
                                            class="btn btn-sm float-end"
                                            style="background: transparent; border: none; font-size: 1.25rem; padding: 0; line-height: 1;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#customCol"
                                            title="Add or remove column">
                                            +
                                        </button>
                                        </th> 
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                    </table>
                </div>
            </div>
        </div>


     <!-- Custom  Columns Modal -->
     <div class="modal fade" id="customCol" tabindex="-1" aria-lablledby="CustomModalLabel" aria-hidden="true"  wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CustomModalLabel">Custom Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
                        <input wire:model.live="avlBal" class="form-check-input" type="checkbox" value="" id="checkAvlBal">
                        <label class="form-check-label" for="checkAvlBal">
                            Inventory Balance (BAL)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="avlQty" class="form-check-input" type="checkbox" value="" id="checkAvlQty">
                        <label class="form-check-label" for="checkAvlQty">
                            Available Quantity (AVAIL.)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="totalReserved" class="form-check-input" type="checkbox" value="" id="totalReserved">
                        <label class="form-check-label" for="totalReserved">
                            Total Reserved (RESERVED)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="code" class="form-check-input" type="checkbox" value="" id="checkCode">
                        <label class="form-check-label" for="checkCode">
                            SKU/CODE (SKU)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="uom" class="form-check-input" type="checkbox" value="" id="checkUom">
                        <label class="form-check-label" for="checkUom">
                            Unit of Measure (UNIT)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="category" class="form-check-input" type="checkbox" value="" id="checkCategory">
                        <label class="form-check-label" for="checkCategory">
                            Category (CATEGORY)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="location" class="form-check-input" type="checkbox" value="" id="checkLocation">
                        <label class="form-check-label" for="checkLocation">
                            Location (LOCATION)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="brand" class="form-check-input" type="checkbox" value="" id="checkBrand">
                        <label class="form-check-label" for="checkBrand">
                            Brand (BRAND)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="status" class="form-check-input" type="checkbox" value="" id="checkStatus">
                        <label class="form-check-label" for="checkStatus">
                            Status (STATUS)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="classification" class="form-check-input" type="checkbox" value="" id="checkClassification">
                        <label class="form-check-label" for="checkClassification">
                            Classification (CLASSIFICATION)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="barcode" class="form-check-input" type="checkbox" value="" id="checkBarcode">
                        <label class="form-check-label" for="checkBarcode">
                            Barcode (BARCODE)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>



