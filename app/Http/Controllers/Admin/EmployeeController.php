<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $employees = Employee::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('full_name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('position', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalEmployees = Employee::count();
        $hiredThisYear = Employee::whereYear('hired_at', now()->year)->count();
        $positionsCount = Employee::query()->whereNotNull('position')->distinct('position')->count('position');

        return view('admin.employees.index', compact(
            'employees',
            'search',
            'totalEmployees',
            'hiredThisYear',
            'positionsCount'
        ));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateEmployee($request);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'employee',
            ]);

            Employee::create([
                'user_id' => $user->id,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null,
                'salary' => $data['salary'] ?? null,
                'hired_at' => $data['hired_at'] ?? null,
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Đã thêm nhân viên mới.');
    }

    public function edit(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $data = $this->validateEmployee($request, $employee);

        DB::transaction(function () use ($employee, $data) {
            $userData = [
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $employee->user->update($userData);

            $employee->update([
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null,
                'salary' => $data['salary'] ?? null,
                'hired_at' => $data['hired_at'] ?? null,
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function destroy(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $employee->user?->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Đã xóa nhân viên.');
    }

    protected function validateEmployee(Request $request, ?Employee $employee = null): array
    {
        $userId = $employee?->user_id;

        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [$employee ? 'nullable' : 'required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hired_at' => ['nullable', 'date'],
        ]);
    }
}
