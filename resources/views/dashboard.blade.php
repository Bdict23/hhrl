@extends('layouts.master')
@section('content')

    <div class="container-fluid px-4">
        <h1 class="mt-4 text-muted">Welcome !</h1>
        <ol class="breadcrumb mb-4">
            <h6 class="breadcrumb-item active">{{ auth()->user()->name }}</h6>
        </ol>
        

    </div>
    
@endsection
