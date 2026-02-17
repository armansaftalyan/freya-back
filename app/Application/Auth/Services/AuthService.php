<?php

declare(strict_types=1);

namespace App\Application\Auth\Services;

use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /** @param array{name:string,email:string,phone?:string,password:string} $data */
    public function registerClient(array $data): User
    {
        /** @var User $user */
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        $user->assignRole('client');

        return $user;
    }

    /** @param array{email:string,password:string} $credentials */
    public function attemptLogin(array $credentials): User
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        return $user;
    }
}
