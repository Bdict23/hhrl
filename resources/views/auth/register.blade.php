@extends('layouts.master')
@section('content')

    <h5 class="text-start d:ml-3">User Registration Form</h5>
    <div class="dashboard">
        <form method="POST" 
        action="{{ route('register') }}" >
            @csrf
            <!-- Employee Dropdown -->
            <input type="hidden" id="empid" name="emp_id" value="">

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const employeeSelect = document.getElementById('name');
                    const employeeIdField = document.getElementById('empid');

                    employeeSelect.addEventListener('change', function() {
                        employeeIdField.value = employeeSelect.value;
                    });

                    // Trigger change event to set initial value
                    employeeSelect.dispatchEvent(new Event('change'));
                });
            </script>

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <select id="name" name="name" class="block mt-1 w-full form-control" required>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}
                            {{ $employee->last_name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Branch Dropdown -->
            <div class="mt-4">
                <x-input-label for="branch_id" :value="__('Assigned Branch')" />
                <select id="branch_id" name="branch_id" class="block mt-1 w-full form-control" required>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- <!-- Terms -->
            <div class="mt-4">
                <label for="terms">
                    <input id="terms" type="checkbox" name="terms" required>
                    <span class="ms-2 text-sm text-gray-600">{{ __('I agree to the terms and conditions') }}</span>
                </label>
            </div> --}}

            <div class="flex items-center justify-end mt-4">
                {{-- <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a> --}}

                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>

@endsection
