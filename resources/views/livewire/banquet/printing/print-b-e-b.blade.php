<div>
   <div class="head mb-4">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                <img src="{{ asset('images/' . auth()->user()->branch->company->company_logo) }}" alt="Branch Logo" style="max-height: 50px;">
                <h3 style="margin: 0;">{{ auth()->user()->branch->branch_name }}</h3>
            </div>
            <span class="address h5">{{ auth()->user()->branch->branch_address }}</span>
            <span class="address ">{{ auth()->user()->branch->branch_type }}</span>
    </div>
    <strong class="mb-4 mt-6">Banquet Event Budget - {{ $banquetEventBudget->reference_number }}</strong>


        <table class="w-full table-bordered mt-2">
        <tbody>
            <tr>
                <td><strong class="m-1">Event Name:</strong> {{ $banquetEventBudget->event->event_name }}</td>
                <td><strong class="m-1">Banquet Event Order (BEO) Number:</strong> {{ $banquetEventBudget->event->reference}}</td>
            </tr>
            <tr>
                <td><strong class="m-1">Start Date:</strong> {{ (\Carbon\Carbon::parse($banquetEventBudget->start_date)->format('M. d-'))}}{{ (\Carbon\Carbon::parse($banquetEventBudget->end_date)->format('d, Y')) }}</td>
                <td><strong class="m-1">Print Date :</strong> {{ \Carbon\Carbon::now()->format('M. d, Y') }}</td>
            </tr>
            <tr>
                <td><strong class="m-1">Start Time:</strong> {{ \Carbon\Carbon::parse($banquetEventBudget->event->arrival_time)->format('h:i A') }} ( {{ \Carbon\Carbon::parse($banquetEventBudget->event->start_date)->format('M. d, Y') }} )</td>
                <td><strong class="m-1">End Time:</strong> {{ \Carbon\Carbon::parse($banquetEventBudget->event->departure_time)->format('h:i A') }} ( {{ \Carbon\Carbon::parse($banquetEventBudget->event->end_date)->format('M. d, Y') }} )</td>
            </tr>
        </tbody>
    </table>
    <div class="col-md-12 mt-4 mb-2">
        <h6>The estimated budget for the event is as follows:</h6>
    </div>
    <table class="w-full table-bordered mt-2">
            <thead>
                    <th class="thead">Catering</th>
            </thead>
            <tbody>
                @foreach($banquetEventBudget->event->eventMenus ?? [] as $eventMenu)
                    <tr>
                        <td>
                                <span class="ml-1 t-sm break-all">{!! ($eventMenu->menu->menu_name) ? $eventMenu->qty . 'x '.$eventMenu->menu->menu_name . ' - ₱' . number_format($eventMenu->price->amount * $eventMenu->qty, 2) : '' !!}</span>
                        </td>
                    </tr>
                 @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        @php
                            $total =  isset($banquetEventBudget) && $banquetEventBudget->event->eventMenus
                                                            ? $banquetEventBudget->event->eventMenus->sum(function($menu) {
                                                                return $menu->price->amount * ($menu->qty ? $menu->qty : 1);
                                                            })
                                                            : 0;
                        @endphp
                        <strong>Total: ₱{{ number_format($total, 2) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        @if($banquetEventBudget->services_included)
            <table class="w-full mt-2 table-bordered">
                <thead>
                    <tr>
                        <th class="thead">OTHERS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @foreach($banquetEventBudget->event->eventServices ?? [] as $eventService)
                                <p class="m-1 t-sm">{!! ($eventService->service->service_name) ? $eventService->qty . 'x '.$eventService->service->service_name . ' - ₱' . number_format($eventService->price->amount * $eventService->qty, 2) : '' !!}</p>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            @php
                                $totalServices =  isset($banquetEventBudget) && $banquetEventBudget->event->eventServices
                                                                ? $banquetEventBudget->event->eventServices->sum(function($service) {
                                                                    return $service->price->amount * ($service->qty ? $service->qty : 1);
                                                                })
                                                                : 0;
                            @endphp
                            <strong>Total: ₱{{ number_format($totalServices, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif

         <div class="mt-4">
            <table>
               <tbody>
                   <tr>
                       <td style="border: none; padding: 0;">
                           <p class="m-1 t-sm"><strong>Gross Order Amount :  </strong>₱ {{ number_format($totalGrossOrder, 2) }}</p>
                           <p class="m-1 t-sm"><strong>Estimated Budget :  </strong>₱ {{ number_format($banquetEventBudget->suggested_amount, 2) }} (<strong>{{ $totalGrossOrder > 0 ? number_format(($banquetEventBudget->suggested_amount / $totalGrossOrder) * 100, 0) : 0 }}%</strong>)</p>
                           <p class="m-1 t-sm"><strong>Actual Expense :  </strong>₱ {{ $actualExpense > 0 ? number_format($actualExpense, 2) : '' }}</p>
                           <hr>
                           <p class="m-1 t-sm"><strong>Variance :  </strong>₱ {{ $variance > 0 ? number_format($variance, 2) : '' }}</p>
                           <hr>
                           <p class="m-1 t-sm"><strong>Event Gross income: </strong>₱ {{ $grossIncome > 0 ? number_format($grossIncome, 2) : '' }}</p><br><br>
                       </td>
                   </tr>
               </tbody>
            </table>
         </div>



            <footer style="width: 100%; margin-top: 2rem;">
        <div class="mt-4 row" style="display: flex; width: 100%;">
            <div class="text-center col-md-4" style="flex: 1;">
                <p class="mb-0">Prepared By:</p>
                <p class="mb-0 signaroty-name">  {{ $banquetEventBudget->createdBy->name ?? '' }} {{ $banquetEventBudget->createdBy->last_name ?? '' }}  </p>
                <p class="mb-0">{{ $banquetEventBudget->createdBy->position->position_name ?? '' }}</p>
            </div>
            <div class="text-center col-md-4" style="flex: 1;">
                <p class="mb-0">Approved By:</p>
                <p class="mb-0 signaroty-name">  {{ $banquetEventBudget->approver->name ?? '' }} {{ $banquetEventBudget->approver->last_name ?? '' }}  </p>
                <p class="mb-0">{{ $banquetEventBudget->approver->position->position_name ?? '' }}</p>
            </div>
        </div>
    </footer>
</div>
