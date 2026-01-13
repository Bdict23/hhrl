<div class="container my-5">
        <h3 class="mb-3 text-center">Choose your table</h3>
    
    <!-- Events Card View -->
    <div class="row" id="eventsContainer">
        <!-- TAKE OUT -->
        <div class="col-md-4 mb-4 event-item"  wire:click="gotoMenuSelection('takeout')">
                    <div class="card event-card shadow-sm bg-primary text-white">
                        <div class="card-body text-center border border-primary">
                           <div class="d-flex justify-content-between">
                             <h5 class="card-title">&nbsp;</h5>
                           </div>
                           <h5>Take Out Order &nbsp; <i class="bi bi-person-walking"></i></h5>
                            <div class="mt-2">&nbsp;</div>
                        </div>
                    </div>
                </div>
            
                {{-- dine in --}}
        @foreach ($availableTables as $table)
                <div class="col-md-4 mb-4 event-item" data-date="{{ $table->event_date }}" wire:click="gotoMenuSelection({{ $table->id }})">
                    <div class="card event-card shadow-sm">
                        <div class="card-body">
                           <div class="d-flex justify-content-between">
                             <h5 class="card-title">{{ $table->table_name }}</h5>
                           </div>
                            <p class="card-text">
                                {{ $table->seating_capacity }} Capacity
                            </p>
                            <span class="badge bg-success date-badge" wire:ignore>{{ $table->availability}}</span>

                        </div>
                    </div>
                </div>
            @endforeach
           
            @if ($availableTables->isEmpty())
                <div class="col-12 text-center">
                    <p class="text-muted">No tables available.</p>
                </div>
            @endif
        </div>
</div>