@extends('layouts.master')
@section('content')
 <style>
    .event-card {
      border-radius: 15px;
      transition: transform 0.2s ease;
    }
    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .circle {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      margin-right: 6px;
    }
    .circle.red {
      background-color: #dc3545;
    }
    .circle.yellow {
      background-color: #ffc107;
    }
    .circle.green {
      background-color: #28a745;
    }
  </style>
<div class="container my-5">
        <h3 class="mb-3 text-center">Banquet Events Overview</h3>
<div class="container my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body d-flex flex-wrap align-items-end gap-2">

      <!-- Date range filter group -->
        <div class="d-flex flex-wrap gap-2">
            <div>
                <label for="startDate" class="form-label mb-1 fw-semibold">From</label>
                <input type="date" id="startDate" class="form-control" style="min-width: 160px;">
            </div>
            <div>
                <label for="endDate" class="form-label mb-1 fw-semibold">To</label>
                <input type="date" id="endDate" class="form-control" style="min-width: 160px;">
            </div>
            <div class="d-flex align-items-end gap-2">
                <button class="btn btn-primary" onclick="filterEvents()">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <button class="btn btn-outline-secondary" onclick="resetFilter()">
                <i class="bi bi-x-circle"></i> Reset
            </button>
            </div>
        </div>

        <!-- Spacer -->
        @if (auth()->user()->employee->getModulePermission('Banquet Events') == 1)
            <div class="ms-auto">
               <x-primary-button style="text-decoration: none;">
                                <a href="{{ route('banquet_events.create') }}" style="text-decoration: none; color: inherit;">+ Create New Event</a>
                </x-primary-button>
            </div>
        @endif
        
    </div>
  </div>
</div>




    <!-- Events Card View -->
        <div class="row" id="eventsContainer">

            <!-- Events -->
            <div class="col-md-4 mb-4 event-item" data-date="2025-06-10">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Wedding Reception</h5>
                <p class="card-text">Grand Hall, 6:00 PM</p>
                <span class="badge bg-success date-badge">June 10, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-12">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Corporate Gala Dinner</h5>
                <p class="card-text">Banquet Room B, 7:00 PM</p>
                <span class="badge bg-primary date-badge">June 12, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-15">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Birthday Celebration</h5>
                <p class="card-text">Garden Venue, 5:00 PM</p>
                <span class="badge bg-warning text-dark date-badge">June 15, 2025</span>
                </div>
            </div>
            </div>

            <!-- 8 More Events -->
            <div class="col-md-4 mb-4 event-item" data-date="2025-06-05">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Charity Banquet</h5>
                <p class="card-text">Main Hall, 5:30 PM</p>
                <span class="badge bg-info date-badge">June 5, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-09">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Engagement Party</h5>
                <p class="card-text">Rooftop Lounge, 7:00 PM</p>
                <span class="badge bg-secondary date-badge">June 9, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-18">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Product Launch</h5>
                <p class="card-text">Conference Hall, 1:00 PM</p>
                <span class="badge bg-dark date-badge">June 18, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-20">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Family Reunion</h5>
                <p class="card-text">Garden Venue, 12:00 NN</p>
                <span class="badge bg-warning text-dark date-badge">June 20, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-25">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Awards Night</h5>
                <p class="card-text">Banquet Hall, 8:00 PM</p>
                <span class="badge bg-success date-badge">June 25, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-06-28">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Annual Sports Night</h5>
                <p class="card-text">Ballroom, 7:00 PM</p>
                <span class="badge bg-primary date-badge">June 28, 2025</span>
                </div>
            </div>
            </div>

            <div class="col-md-4 mb-4 event-item" data-date="2025-07-01">
            <div class="card event-card shadow-sm">
                <div class="card-body">
                <h5 class="card-title">Graduation Party</h5>
                <p class="card-text">Poolside, 6:00 PM</p>
                <span class="badge bg-info date-badge">July 1, 2025</span>
                </div>
            </div>
            </div>

        </div>
    </div>


<script>
  function filterEvents() {
    let start = document.getElementById('startDate').value;
    let end = document.getElementById('endDate').value;
    let items = document.querySelectorAll('.event-item');

    items.forEach(item => {
      let eventDate = item.getAttribute('data-date');
      if ((start && eventDate < start) || (end && eventDate > end)) {
        item.style.display = 'none';
      } else {
        item.style.display = 'block';
      }
    });
  }

  function resetFilter() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.querySelectorAll('.event-item').forEach(item => {
      item.style.display = 'block';
    });
  }

  function updateDaysLeft() {
    let today = new Date();
    let items = document.querySelectorAll('.event-item');

    items.forEach(item => {
      let dateStr = item.getAttribute('data-date');
      let eventDate = new Date(dateStr);
      let diffTime = eventDate - today;
      let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      let circle = document.createElement('span');
      circle.classList.add('circle');

      if (diffDays <= 3) {
        circle.classList.add('red');
      } else if (diffDays <= 10) {
        circle.classList.add('yellow');
      } else {
        circle.classList.add('green');
      }

      let badge = item.querySelector('.date-badge');
      badge.innerHTML = `${circle.outerHTML} ${badge.innerHTML} <small>(${diffDays} day(s) left)</small>`;
    });
  }

  function sortEventsByDate() {
    let container = document.getElementById('eventsContainer');
    let items = Array.from(container.querySelectorAll('.event-item'));

    items.sort((a, b) => {
      let dateA = new Date(a.getAttribute('data-date'));
      let dateB = new Date(b.getAttribute('data-date'));
      return dateA - dateB;
    });

    items.forEach(item => {
      container.appendChild(item);
    });
  }

  // Sort events and update days left indicators on load
  sortEventsByDate();
  updateDaysLeft();
</script>

@endsection