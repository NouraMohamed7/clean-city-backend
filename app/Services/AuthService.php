<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register new user
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
            'city_id' => $data['city_id'] ?? null,
            'total_points' => 0,
            'is_active' => true,
        ]);

        // If company role, create company record
        if ($user->role === 'company' && !empty($data['company'])) {
            Company::create([
                'user_id' => $user->id,
                'name' => $data['company']['name'],
                'city_id' => $data['company']['city_id'],
                'coverage_areas' => json_encode($data['company']['coverage_areas'] ?? []),
                'phone' => $data['company']['phone'] ?? null,
                'email' => $data['email'],
                'is_active' => true,
                'latitude' => $data['company']['latitude'] ?? null,
            'longitude' => $data['company']['longitude'] ?? null,
            ]);
        }

if ($user->role === 'company') {
    if (
        empty($data['company']['latitude']) ||
        empty($data['company']['longitude'])
    ) {
        throw ValidationException::withMessages([
            'company.location' => 'Company location is required.',
        ]);
    }
}



        $token = $user->createToken('cleancity')->plainTextToken;

        return [
            'user' => $user->load('company'),
            'token' => $token,
        ];
    }

    /**
     * Login user
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('cleancity')->plainTextToken;

        return [
            'user' => $user->load('company'),
            'token' => $token,
        ];
    }

    /**
     * Logout user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Get authenticated user
     */
    public function me(User $user): User
    {
        return $user->load(['city', 'company', 'reports' => fn($q) => $q->latest()->limit(5)]);
    }
}
