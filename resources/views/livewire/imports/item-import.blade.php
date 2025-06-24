<div id="item-import"  style="display: none;" wire:ignore.self>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="import">
        <input type="file" class="form-control" wire:model="file">
        @error('file') <span class="text-danger">{{ $message }}</span> @enderror

        <button type="submit" class="btn btn-primary mt-3">Import Items</button>
    </form>
</div>

