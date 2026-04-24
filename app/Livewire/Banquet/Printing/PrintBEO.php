<?php

namespace App\Livewire\Banquet\Printing;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\BanquetEvent;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintBEO extends Component
{
    public $eventDetails;
    public $totalAmountLocation = 0;
    public $totalAmountService = 0;
    public $totalAmountMenu = 0;

    public function render()
    {
        return view('livewire.banquet.printing.print-b-e-o');
    }
    public function mount(Request $request)
    {
       if($request->has('event-id')) {
           $eventId = $request->query('event-id');
           $this->eventDetails = BanquetEvent::with('customer', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel', 'eventServices.service', 'eventVenues', 'eventVenues.venue', 'eventVenues.ratePrice', 'eventServices.price', 'eventMenus.menu', 'eventMenus.menu.category', 'eventMenus.price', 'createdBy', 'createdBy.position', 'reviewer', 'reviewer.position', 'approver', 'approver.position')->find($eventId);
           $this->calculateTotalAmount();
       } else {
           abort(404, 'Event ID is required');
       }

    }

    public function calculateTotalAmount()
    {

        // Calculate total from event services
        foreach ($this->eventDetails->eventServices as $service) {
            $this->totalAmountService += $service->total_amount;
        }

        // Calculate total from event menus
        foreach ($this->eventDetails->eventMenus as $menu) {
            $this->totalAmountMenu += $menu->total_amount;
        }

        // Calculate total from location rentals
        foreach ($this->eventDetails->eventVenues as $venue) {
            $this->totalAmountLocation += $venue->total_amount;
        }
    }

    public function exportToPdf()
    {
        // Prepare data for PDF
        $eventDetails = $this->eventDetails;
        $totalAmountLocation = $this->totalAmountLocation;
        $totalAmountService = $this->totalAmountService;
        $totalAmountMenu = $this->totalAmountMenu;

        // Load the PDF view without buttons
        $pdf = Pdf::loadView('export.pdf.banquet-event-order', [
            'eventDetails' => $eventDetails,
            'totalAmountMenu' => $totalAmountMenu,
            'totalAmountLocation' => $totalAmountLocation,
            'totalAmountService' => $totalAmountService,
        ]);

        // Set PDF options for better formatting
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);

        // Return the PDF as download
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'BEO-' . $eventDetails->reference . '.pdf');
    }
}
