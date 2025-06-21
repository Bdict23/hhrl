
@extends('layouts.master')
@section('content')
  <style>
    .table-card {
      border-radius: 15px;
      transition: transform 0.2s ease;
    }
    .table-card:hover {
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
  
  <div>
    @livewire('restaurant.table-selection')
  </div>
@endsection