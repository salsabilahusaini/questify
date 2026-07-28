<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
{
    $users = User::query()
        ->orderBy('role')
        ->orderBy('name')
        ->paginate(10);

    return view('admin.users.index', [
        'users' => $users,
        'totalUsers' => User::count(),
        'totalAdmins' => User::where('role', User::ROLE_ADMIN)->count(),
        'totalTeachers' => User::where('role', User::ROLE_TEACHER)->count(),
        'totalStudents' => User::where('role', User::ROLE_STUDENT)->count(),
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
{
    return view('admin.users.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'role' => [
            'required',
            Rule::in([
                User::ROLE_ADMIN,
                User::ROLE_TEACHER,
                User::ROLE_STUDENT,
            ]),
        ],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ], [
        'name.required' => 'Nama pengguna wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email tersebut sudah digunakan.',
        'role.required' => 'Role pengguna wajib dipilih.',
        'role.in' => 'Role pengguna tidak valid.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal terdiri atas 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak sesuai.',
    ]);

    $user=user::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'role' => $validated['role'],
        'password' => Hash::make($validated['password']),

    ]);
    $user->forceFill([
    'email_verified_at' => now(),
    ])->save();
    return redirect()
        ->route('admin.users.index')
        ->with('success', 'Pengguna berhasil ditambahkan.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
{
    return view('admin.users.edit', [
        'user' => $user,
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user),
        ],
        'role' => [
            'required',
            Rule::in([
                User::ROLE_ADMIN,
                User::ROLE_TEACHER,
                User::ROLE_STUDENT,
            ]),
        ],
        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
        ],
    ], [
        'name.required' => 'Nama pengguna wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email tersebut sudah digunakan.',
        'role.required' => 'Role pengguna wajib dipilih.',
        'role.in' => 'Role pengguna tidak valid.',
        'password.min' => 'Password minimal terdiri atas 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak sesuai.',
    ]);

    /*
     * Admin yang sedang digunakan tidak boleh diubah
     * menjadi Guru atau Peserta Didik.
     */
    if (
        $request->user()->is($user)
        && $validated['role'] !== User::ROLE_ADMIN
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'role' => 'Role akun Admin yang sedang digunakan tidak dapat diubah.',
            ]);
    }

    /*
     * Questify harus tetap memiliki minimal satu Admin.
     */
    if (
        $user->role === User::ROLE_ADMIN
        && $validated['role'] !== User::ROLE_ADMIN
        && User::where('role', User::ROLE_ADMIN)->count() <= 1
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'role' => 'Sistem harus memiliki minimal satu akun Admin.',
            ]);
    }

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];

    /*
     * Password hanya diubah apabila kolom password baru diisi.
     */
    if (! empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'Data pengguna berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
    Request $request,
    User $user
): RedirectResponse {
    /*
     * Admin tidak dapat menghapus akun yang sedang digunakan.
     */
    if ($request->user()->is($user)) {
        return redirect()
            ->route('admin.users.index')
            ->with(
                'error',
                'Akun yang sedang digunakan tidak dapat dihapus.'
            );
    }

    /*
     * Questify harus tetap memiliki minimal satu Admin.
     */
    if (
        $user->role === User::ROLE_ADMIN
        && User::where('role', User::ROLE_ADMIN)->count() <= 1
    ) {
        return redirect()
            ->route('admin.users.index')
            ->with(
                'error',
                'Sistem harus memiliki minimal satu akun Admin.'
            );
    }

    $user->delete();

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'Pengguna berhasil dihapus.');
}
}
