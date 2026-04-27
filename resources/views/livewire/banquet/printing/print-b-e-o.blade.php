<div>
    <div class="mb-4 head">
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
            <img src="{{ asset('images/' . auth()->user()->branch->company->company_logo) }}" alt="Branch Logo"
                style="max-height: 50px;">
            <h3 style="margin: 0;">{{ auth()->user()->branch->branch_name }}</h3>
        </div>
        <span class="address h5">{{ auth()->user()->branch->branch_address }}</span>
        <span class="address ">{{ auth()->user()->branch->branch_type }}</span>
    </div>


    <strong class="mt-6 mb-4">Banquet Event Order - {{ $eventDetails->reference }}</strong>
    <table class="w-full mt-2 table-bordered">
        <tbody>
            <tr>
                <td><strong class="m-1">Event Name:</strong> {{ $eventDetails->event_name }}</td>
                <td><strong class="m-1">Customer Name:</strong> {{ $eventDetails->customer->customer_fname }}
                    {{ $eventDetails->customer->customer_lname }} {{ $eventDetails->customer->suffix ?? '' }}</td>
            </tr>
            <tr>
                <td><strong class="m-1">Event Date:</strong>
                    {{ \Carbon\Carbon::parse($eventDetails->start_date)->format('M. d-') }}{{ \Carbon\Carbon::parse($eventDetails->end_date)->format('d, Y') }}
                </td>
                <td><strong class="m-1">Address:</strong> {{ $eventDetails->customer->customer_address }}</td>
            </tr>
            <tr>
                <td><strong class="m-1">Location :</strong> {{ $eventDetails->event_address }}</td>
                <td><strong class="m-1">Contact #:</strong>
                    {{ $eventDetails->customer->contact_no_1 }}{{ $eventDetails->customer->contact_no_2 ? '/' . $eventDetails->customer->contact_no_2 : '' }}
                </td>
            </tr>
            <tr>
                <td><strong class="m-1">Guests Count:</strong> {{ $eventDetails->guest_count }}</td>
                <td><strong class="m-1">Email:</strong> {{ $eventDetails->customer->email }}</td>
            </tr>
            <tr>
                <td><strong class="m-1">Start Time:</strong>
                    {{ \Carbon\Carbon::parse($eventDetails->arrival_time)->format('h:i A') }} (
                    {{ \Carbon\Carbon::parse($eventDetails->start_date)->format('M. d, Y') }} )</td>
                <td><strong class="m-1">End Time:</strong>
                    {{ \Carbon\Carbon::parse($eventDetails->departure_time)->format('h:i A') }} (
                    {{ \Carbon\Carbon::parse($eventDetails->end_date)->format('M. d, Y') }} )</td>
            </tr>
        </tbody>
    </table>
    <div class="w-full justify-content-start" style=" padding: 10px; border: 1px">
        <span style="white-space: normal;"><strong>Note : </strong> {{ $eventDetails->notes ?? '' }}</span>
    </div>
    <table class="w-full mt-2 table-bordered">
        <thead>
            <th class="thead">FOOD</th>
            <th class="thead">DESCRIPTION</th>
            <th class="thead">NOTE</th>

        </thead>
        <tbody>
            @foreach ($eventDetails->eventMenus ?? [] as $eventMenu)
                <tr>
                    <td colspan="1">
                        <span class="ml-1 break-all t-sm">{!! $eventMenu->menu->menu_name
                            ? $eventMenu->qty .
                                'x ' .
                                $eventMenu->menu->menu_name .
                                ' - ₱' .
                                number_format($eventMenu->price->amount * $eventMenu->qty, 2)
                            : '' !!}</span>
                    </td>
                    <td colspan="1">
                        <span class="ml-1 break-all t-sm" style="white-space: normal;">{!! $eventMenu->menu->menu_description ? $eventMenu->menu->menu_description : '' !!}</span>
                    </td>
                    <td colspan="1">
                        <span class="ml-1 break-all t-sm" style="white-space: pre-wrap;">{!! $eventMenu->note ? $eventMenu->note : '' !!}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="col-md-12">
        <table class="w-full mt-4 table-bordered">
            <thead>
                <tr>
                    <th class="thead">LOCATIONS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @foreach ($eventDetails->eventVenues ?? [] as $eventVenue)
                            <p class="m-1 t-sm">{!! $eventVenue->venue->venue_name
                                ? $eventVenue->qty .
                                    'x ' .
                                    $eventVenue->venue->venue_name .
                                    ' (Good for ' .
                                    $eventVenue->venue->capacity .
                                    ' Guests) - ₱' .
                                    number_format($eventVenue->ratePrice->amount * $eventVenue->qty, 2)
                                : '' !!}</p>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="w-full mt-2 table-bordered">
            <thead>
                <tr>
                    <th class="thead">OTHERS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @foreach ($eventDetails->eventServices ?? [] as $eventService)
                            <p class="m-1 t-sm">{!! $eventService->service->service_name
                                ? $eventService->qty .
                                    'x ' .
                                    $eventService->service->service_name .
                                    ' - ₱' .
                                    number_format($eventService->price->amount ?? 0 * $eventService->qty, 2)
                                : '' !!}</p>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-start">
            <table>
                <tbody>
                    <tr>
                        <td style="border: none; padding: 0;">
                            <p class="m-1 t-sm"><strong>Food : </strong>₱ {{ number_format($totalAmountMenu, 2) }}</p>
                            <p class="m-1 t-sm"><strong>Location : </strong>₱
                                {{ number_format($totalAmountLocation, 2) }}</p>
                            <p class="m-1 t-sm"><strong>Others : </strong>₱ {{ number_format($totalAmountService, 2) }}
                            </p>
                            <hr>
                            <p class="m-1 t-sm"><strong>Total Amount: </strong>₱
                                {{ number_format($eventDetails->total_amount, 2) }}</p><br><br>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>

    <footer style="width: 100%; margin-top: 2rem;">
        <div class="mt-4 row" style="display: flex; width: 100%;">
            <div class="text-center col-md-4" style="flex: 1;">
                <p class="mb-0">Prepared By:</p>
                <p class="mb-0 signaroty-name"> {{ $eventDetails->createdBy->name ?? '' }}
                    {{ $eventDetails->createdBy->last_name ?? '' }} </p>
                <p class="mb-0">{{ $eventDetails->createdBy->position->position_name ?? '' }}</p>
            </div>
            <div class="text-center col-md-4" style="flex: 1;">
                <p class="mb-0">Reviewed By:</p>
                <p class="mb-0 signaroty-name"> {{ $eventDetails->reviewer->name ?? '' }}
                    {{ $eventDetails->reviewer->last_name ?? '' }} </p>
                <p class="mb-0">{{ $eventDetails->reviewer->position->position_name ?? '' }}</p>
            </div>
            <div class="text-center col-md-4" style="flex: 1;">
                <p class="mb-0">Approved By:</p>
                <p class="mb-0 signaroty-name"> {{ $eventDetails->approver->name ?? '' }}
                    {{ $eventDetails->approver->last_name ?? '' }} </p>
                <p class="mb-0">{{ $eventDetails->approver->position->position_name ?? '' }}</p>
            </div>
        </div>
    </footer>


    <!-- Additional sections for services, menus, equipment, etc. can be added here -->
</div>
