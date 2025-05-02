
@extends('layouts.master')
@section('content')
    <div class="container min-h-screen bg-gray-100">
    <main>
    {{ $slot }}
    </main>
    @livewireScripts
    </div>
@endsection
