<?php

namespace App\Livewire\Imports;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsImport;

class ItemImport extends Component
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new ItemsImport, $this->file->getRealPath());

        session()->flash('success', 'Items imported successfully.');
        $this->reset('file');
    }

    public function render()
    {
        return view('livewire.imports.item-import');
    }
}
