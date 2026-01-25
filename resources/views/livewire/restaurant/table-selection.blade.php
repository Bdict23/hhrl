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
                <div class="col-md-4 mb-4 event-item" data-date="{{ $table->event_date }}">
                    <div class="card event-card shadow-sm">
                        <div class="card-body">
                           <div class="row">
                                <div class="col-md-11"  wire:click="gotoMenuSelection({{ $table->id }})" id="test" style="cursor: pointer">
                                    <div class="d-flex justify-content-between">
                                    <h5 class="card-title " >{{ $table->table_name }}</h5>
                                    
                                    </div>
                                    <p class="card-text">
                                        {{ $table->seating_capacity }} Capacity
                                    </p>
                                    <i class="bi bi-circle-fill text-muted"><i class="bi bi-fork-knife text-muted"></i></i>
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                           </div>
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


        <div class="modal fade modal-sm " id="tableStatusChange" tabindex="-1" aria-labelledby="ViewPaymentsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ViewPaymentsLabel">Set table status &nbsp;<i class="bi bi-gear"></i></h5>
                        <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#supplierViewModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="">
                        <div class="d-flex justify">
                            <button class="btn btn-secondary m-2">Reserve</button>
                            <button class="btn btn-info m-2">Available</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <script>

           function check(){
            // Swal.fire({
            // template: "#my-template"
            // });
            
            //  const Toast = Swal.mixin({
            // toast: true,
            // position: "top-end",
            // showConfirmButton: false,
            // timer: 3000,
            // timerProgressBar: true,
            // didOpen: (toast) => {
            //     toast.onmouseenter = Swal.stopTimer;
            //     toast.onmouseleave = Swal.resumeTimer;
            // }
            // });
            // Toast.fire({
            // icon: "success",
            // title: "Signed in successfully"
            // });

           } 
  
   

        // Listen for Livewire alert event
        window.addEventListener('alert', event => {
            const data = event.detail[0];
                Swal.fire({
                title: "Pending order for this table.",
                text: "Do you want to add more items to the current order?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, add more items!",
                }).then((result) => {
                if (result.isConfirmed) {
                    @this.additionalOrder(data.tableId);
                }
                });
                if(data.error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                    });
                }
        });

       
    </script>
</div>