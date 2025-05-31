@extends('layouts.master')
@section('content')

<div class="dashboard">
    <h2 class="mb-4 text-center">Create Banquet Event</h2>
    <div class="container my-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" required>
                        </div>
                         <div class="col-md-6">
                            <label for="event_name" class="form-label">Customer Name</label>
                            <div class="input-group">
                                <select class="form-control" id="sub_classification_id-update"
                                    wire:model="sub_classification_id">
                                    <option value="">Select</option>
                                    <option value="1">Customer sample 1</option>
                                    <option value="2">Customer sample 2</option>
                                    <option value="3">Customer sample 3</option>
                                </select>
                                <button class="input-group-text" type="button"
                                    style="background-color: rgb(190, 243, 217);">+</button>
                            </div>  
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="event_date" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" required>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-6">
                                <label for="event_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="event_time" name="event_time" required>
                            </div>
                            <div class="col-md-6">
                                <label for="event_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="event_time" name="event_time" required>
                            </div>

                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="venue" class="form-label">Venue</label>
                        <select name="" id="" class="form-select" required>
                            <option value="">Select Venue</option>
                            <option value="Venue 1">Venue 1</option>
                            <option value="Venue 2">Venue 2</option>
                            <option value="Venue 3">Venue 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="guest_count" class="form-label">Expected Guest Count</label>
                        <input type="number" class="form-control" id="guest_count" name="guest_count" required>
                    </div>
                    <div class="mb-3">
                        <label for="event-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="event-notes" name="event-notes" rows="3"></textarea>
                    </div>


                    <button type="submit" class="btn btn-primary">Create Event</button>
                    <a href="{{ route('banquet_events.summary') }}" class="btn btn-secondary">Back to Events</a>
                </form>
            </div>
        </div>    
</div>


@endsection