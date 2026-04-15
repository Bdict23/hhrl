@extends('layouts.master')
@section('content')

@if (session('error'))
    <div class="alert alert-danger" role="alert" >
        {{ session('error') }}
    </div>
@endif
    <div class="container-fluid px-4">
        <h1 class="mt-4 text-muted">Welcome !</h1>
        <ol class="breadcrumb mb-4">
            <h6 class="breadcrumb-item active">{{ auth()->user()->name }}</h6>
        </ol>
        

    </div>
    
@endsection
