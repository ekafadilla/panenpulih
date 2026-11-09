<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['petaniProfile', 'pembeliProfile']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(12);

        return view('admin.users.index', compact('users'));
    }

    public function verifikasi(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_verified' => ! $user->is_verified]);

        $status = $user->is_verified ? 'terverifikasi' : 'non-aktif';

        return back()->with('success', "Status pengguna {$user->name} berhasil diubah menjadi {$status}.");
    }
}
