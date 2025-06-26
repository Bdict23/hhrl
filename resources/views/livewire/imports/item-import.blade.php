<div id="item-import"  style="display: none;" class="tab-content" wire:ignore.self>
      @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session()->has('error'))
    <div class="alert alert-danger" id="success-message">
        {{ session('error') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div wire:loading wire:target="import" class="text-center my-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Importing...</span>
        </div>
        <div>Importing, please wait...</div>
    </div>

    <form wire:submit.prevent="import">
        <input type="file" class="form-control" wire:model="file">
        @error('file') <span class="text-danger">{{ $message }}</span> @enderror
        <button type="submit" class="btn btn-primary mt-3">Import Items</button>  
    </form>
    <div class="mt-4 alert alert-info">
        <p class="mt-3">Please ensure your file is in .xlsx format and follows the required structure.</p>
        <p>Download the <a wire:click="downloadSample" href="#" >sample file</a> for reference.</p>
    </div>
</div>

