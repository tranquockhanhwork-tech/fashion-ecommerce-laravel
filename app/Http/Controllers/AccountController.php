<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user = Auth::user();
        
        if ($user->customer) {
            $user->customer->update([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'gender' => $request->gender,
            ]);
        }

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }
}
