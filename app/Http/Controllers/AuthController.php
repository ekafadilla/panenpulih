<?php

namespace App\Http\Controllers;

use App\Models\PembeliProfile;
use App\Models\PetaniProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:petani,pedagang,umkm'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'institution' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'institution' => $validated['institution'] ?? null,
            'latitude' => $validated['latitude'] ?? -6.2088,
            'longitude' => $validated['longitude'] ?? 106.8456,
            'is_verified' => true,
        ]);

        if ($user->role === 'petani') {
            PetaniProfile::create([
                'user_id' => $user->id,
                'nama_poktan' => $user->institution ?? 'Kelompok Tani Swadaya',
                'luas_lahan_ha' => 1.0,
                'alamat_lahan' => $user->address,
                'fasilitas_penyimpanan' => 'shaded_shed',
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
            ]);
        } elseif (in_array($user->role, ['pedagang', 'umkm'], true)) {
            PembeliProfile::create([
                'user_id' => $user->id,
                'nama_usaha' => $user->institution ?? $user->name,
                'jenis_usaha' => $user->role === 'umkm' ? 'umkm_olahan' : 'pedagang_pasar',
                'kapasitas_serap_mingguan_kg' => 1000,
                'preferensi_grade' => 'semua',
                'alamat_gudang' => $user->address,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
            ]);
        }

        Auth::login($user);

        return $this->redirectBasedOnRole($user)->with('success', 'Selamat datang di PANENPULIH! Akun Anda berhasil terdaftar.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Anda telah berhasil keluar.');
    }

    /**
     * Demo Fast Login untuk kemudahan evaluasi & pengujian (1-click switch)
     */
    public function demoLogin(string $role): RedirectResponse
    {
        $email = match ($role) {
            'admin' => 'admin@panenpulih.id',
            'petani' => 'petani@panenpulih.id',
            'pedagang' => 'pedagang@panenpulih.id',
            'umkm' => 'umkm@panenpulih.id',
            default => 'petani@panenpulih.id',
        };

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect('/login')->with('error', 'Akun demo belum dibuat. Jalankan seeder database terlebih dahulu.');
        }

        Auth::login($user);

        return $this->redirectBasedOnRole($user)->with('success', "Beralih peran sebagai [{$user->name} - " . strtoupper($user->role) . ']');
    }

    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petani' => redirect()->route('petani.dashboard'),
            'pedagang', 'umkm' => redirect()->route('pembeli.dashboard'),
            default => redirect('/'),
        };
    }
}
