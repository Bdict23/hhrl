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
  @livewire('banquet.banquet-event-summary')


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