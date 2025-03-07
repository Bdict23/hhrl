<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Employee;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $branches = Branch::all();
        $employees = Employee::with('branch')->where('status', 'ACTIVE')->get();
        return view('master_data.departments', compact('departments', 'branches', 'employees'));
    }

    public function show($id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            return response()->json(['message' => 'Department not found'], 404);
        }
        return response()->json($department);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'department_name' => 'required|string|max:255',
            'branch' => 'required|exists:branches,id',
            'description' => 'nullable|string',
            'employees' => 'array',
            'employees.*' => 'exists:employees,id',
        ]);

        $department = Department::create([
            'department_name' => $validatedData['department_name'],
            'branch_id' => $validatedData['branch'],
            'department_description' => $validatedData['description'],
        ]);

        if (!empty($validatedData['employees'])) {
            Employee::whereIn('id', $validatedData['employees'])->update(['department_id' => $department->id]);
        }

        return redirect()->route('departments.index')->with('status', 'Department created successfully!');
    }

    public function update(Request $request, $id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            return response()->json(['message' => 'Department not found'], 404);
        }
        $department->update($request->all());
        return response()->json($department);
    }

    public function destroy($id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            return response()->json(['message' => 'Department not found'], 404);
        }
        $department->delete();
        return response()->json(null, 204);
    }
}
