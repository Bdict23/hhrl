<div class="card">
    <div class="row">
        <div class="col-md-4">
            <label for="itemCode" class="form-label">Item Code</label>
            <div class="input-group ">
                <input wire:model="itemCode" type="text" wire:loading.class.remove="bg-blue" class="form-control bg-blue" id="itemCode" name="item_code">
                <button class="btn btn-primary" wire:click="getData()">
                   Search
                </button>
            </div>
            @error('itemCode')
                <div class="alert alert-danger mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="col-md-8">
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" 
                    readonly value="{{ $itemDescription }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" readonly value="{{ $location }}">
        </div>

        <div class="col-md-2">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" id="price" name="price" readonly value="{{ $price }}">
        </div>

        <div class="col-md-3">
            <label for="totalBalance" class="form-label">Total Balance</label>
            <input type="text" class="form-control" id="totalBalance" name="total_balance"
                readonly value="{{ $totalBalance }}">
        </div>
    </div>
    <table class="table table-striped card-body">
        <thead>
            <tr>
                <th>Date</th>
                <th>In</th>
                <th>Out</th>
                <th>Balance</th>
                <th>Transaction</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody id="cardexTableBody">
            @foreach ($cardexData as $index => $cardexItem)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($cardexItem['created_at'])->format('Y-m-d') }}</td>
                    <td>{{ $cardexItem['in'] }}</td>
                    <td>{{ $cardexItem['out'] }}</td>
                    <td>{{ $cardexItem['balance'] }}</td>
                    <td>{{ $cardexItem['transaction_type'] }}</td>
                    <td>{{ $cardexItem['reference']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>


