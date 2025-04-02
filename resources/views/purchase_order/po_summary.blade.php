@extends('layouts.master')
@section('content')
    @livewire('purchase-order-summary')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif


@endsection
