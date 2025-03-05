<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Employee;
use App\Models\Branch;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        try {
            //code...
            $employees = Employee::all();
            $branches = Branch::all();
           return view('auth.register', compact('employees', 'branches'));

        } catch (\Throwable $th) {
            //throw $th;
            return dd($th);
        }

    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

         try {
            //code...
             // dd($request->emp_id);
             $employee = Employee::where('id', $request->emp_id)->first();
             $validatedData =   $request->validate([
                'emp_id' => ['required', 'integer'],
                'name' => ['required', 'string', 'max:255'],
                 'branch_id' => ['required', 'integer'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $emp_name = $employee->name . ' ' . $employee->last_name;
            $user = User::create([
                'emp_id' => $request->emp_id,
                'name' => $emp_name,
                'branch_id' => $request->branch_id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);


            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        } catch (\Throwable $th) {
           return dd($th);
        }

    }
}
