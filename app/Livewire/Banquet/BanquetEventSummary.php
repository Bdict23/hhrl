<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use App\Models\BanquetEvent;
use App\Models\Customer; 
use App\Models\EventMenu;
use App\Models\EventVenue;
use Barryvdh\DomPDF\Facade\Pdf;

class BanquetEventSummary extends Component
{
    public $eventLists = [];
    public $selectedEventId = null;
    public $selectedEventStatus = null;
    public $eventDetails = [];
    public $venueDetails = [];
    public $fromDate;
    public $toDate;
    public $customerDetails;

    
    public function render()
    {
        return view('livewire.banquet.banquet-event-summary');
    }
    public function mount()
    {
        $this->fetchData();
    }

       public function openEvent()
    {
      return redirect()->to('/banquet-events-create?event-id=' .  $this->selectedEventId);
    }

     public function viewCustomer($customerId = null)
    {
         $this->customerDetails = $customerId ? Customer::find($customerId) : null;

    }

    public function fetchData()
    {
        $this->eventLists = BanquetEvent::where('branch_id', auth()->user()->branch_id)
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function viewEventDetails($eventId)
    {
        $this->selectedEventId = $eventId;
        $this->selectedEventStatus = BanquetEvent::find($eventId)->status;
        $this->eventDetails = BanquetEvent::with('customer', 'eventServices', 'eventMenus', 'equipmentRequests', 'withdrawals', 'withdrawals.cardex.priceLevel', 'eventServices.service', 'eventVenues', 'eventVenues.venue', 'eventVenues.ratePrice', 'eventServices.price', 'eventMenus.menu', 'eventMenus.menu.category', 'eventMenus.price', 'createdBy', 'createdBy.position', 'reviewer', 'reviewer.position', 'approver', 'approver.position')->find($eventId);
        $this->venueDetails = EventVenue::with('venue', 'ratePrice')->where('event_id', $eventId)->get();
        $this->viewCustomer($this->eventDetails->customer_id);
        $this->dispatch('showEventDetailsModal');
        $this->dispatch('modalOpened');
    }

    // public function filterEvents()
    // {
    //     $this->eventLists = BanquetEvent::where('branch_id', auth()->user()->branch_id)
    //         ->whereBetween('event_date', [$this->fromDate, $this->toDate])
    //         ->orderBy('created_at', 'asc')
    //         ->get();
    // }
    public function resetFilters()
    {
        $this->fromDate = null;
        $this->toDate = null;
        $this->fetchData();
        $this->dispatch('modalOpened');
    }

     public function openToEdit($eventId)
    {
        
      //redirect to receiving page with the selected receing id request
      return redirect()->to('/banquet-event-edit?event-id=' . $eventId );
    }

  
    public function confirmEvent()
    {
      $event = BanquetEvent::find($this->selectedEventId);
      if ($event) {
          $event->status = 'CONFIRMED';
          $event->save();
          $this->selectedEventStatus = 'CONFIRMED';
          session()->flash('success', 'Event has been confirmed successfully.');
      }
    }

    public function filterEvents(){
    
         $query = BanquetEvent::where('branch_id', auth()->user()->branch_id);

        if (!$this->fromDate) {
             $query->whereDate('created_at', '<=', $this->toDate);
        }
        if (!$this->toDate) {
             $query->whereDate('created_at', '>=', $this->fromDate);
        }

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('created_at', '>=', $this->fromDate)
                  ->whereDate('created_at', '<=', $this->toDate);
        }

        $this->eventLists = $query->get();
    }

    public function exportEventToPdf()
    {
        // Load event with all relationships
        $eventDetails = BanquetEvent::with(
            'customer',
            'eventServices.service',
            'eventServices.price',
            'eventMenus.menu',
            'eventMenus.menu.category',
            'eventMenus.price',
            'eventVenues.venue',
            'eventVenues.ratePrice',
            'createdBy.position',
            'reviewer.position',
            'approver.position'
        )->find($this->selectedEventId);

        if (!$eventDetails) {
            session()->flash('error', 'Event not found.');
            return;
        }

        // Calculate totals
        $totalAmountMenu = $eventDetails->eventMenus->sum('total_amount');
        $totalAmountLocation = $eventDetails->eventVenues->sum('total_amount');
        $totalAmountService = $eventDetails->eventServices->sum('total_amount');

        // Load the PDF view
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
        $pdf->setOption('dpi', 150);
        $pdf->setOption('defaultFont', 'Arial');
        $pdf->setOption('isHtml5ParserEnabled', true);

        // Return the PDF as download
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'BEO-' . $eventDetails->reference . '.pdf');
    }

}
