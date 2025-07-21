<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'display_name',
        'email',
        'password',
        'phone_number',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $name = $this->display_name ?: $this->name;

        return Str::of($name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name'] ?: $this->name;
    }

    /**
     * Check if phone number is verified
     */
    public function hasVerifiedPhoneNumber(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * Generate a unique username based on email or name
     */
    public static function generateUsername(string $baseString, ?string $existingEmail = null): string
    {
        // Extract base from email or use provided string
        if (str_contains($baseString, '@')) {
            $base = explode('@', $baseString)[0];
        } else {
            $base = $baseString;
        }

        // Clean the base string to match username regex: /^([a-zA-Z0-9][a-zA-Z0-9_\-.]{0,47})$/
        $base = Str::lower($base);
        $base = preg_replace('/[^a-z0-9_.-]/', '', $base);

        // Ensure it starts with alphanumeric
        if (! preg_match('/^[a-z0-9]/', $base)) {
            $base = 'user'.$base;
        }

        // Minimum length check
        if (strlen($base) < 3) {
            $base = 'user'.$base;
        }

        // Check if base username is available
        $query = static::where('username', $base);

        // If updating existing user, exclude their current record
        if ($existingEmail) {
            $query->where('email', '!=', $existingEmail);
        }

        if (! $query->exists()) {
            return $base;
        }

        // Try with numbers
        for ($i = 1; $i <= 999; $i++) {
            $candidate = $base.$i;
            $query = static::where('username', $candidate);

            if ($existingEmail) {
                $query->where('email', '!=', $existingEmail);
            }

            if (! $query->exists()) {
                return $candidate;
            }
        }

        // Fallback with timestamp
        return $base.time();
    }

    /**
     * Suggest alternative usernames if the requested one is taken
     */
    public static function suggestUsernames(string $requestedUsername, ?string $existingEmail = null): array
    {
        $suggestions = [];

        // Clean the requested username to match username regex
        $base = Str::lower($requestedUsername);
        $base = preg_replace('/[^a-z0-9_.-]/', '', $base);

        // Ensure it starts with alphanumeric
        if (! preg_match('/^[a-z0-9]/', $base)) {
            $base = 'user'.$base;
        }

        if (strlen($base) < 3) {
            $base = 'user'.$base;
        }

        // Generate 5 suggestions
        for ($i = 1; $i <= 5; $i++) {
            $candidate = $base.$i;
            $query = static::where('username', $candidate);

            if ($existingEmail) {
                $query->where('email', '!=', $existingEmail);
            }

            if (! $query->exists()) {
                $suggestions[] = $candidate;
            }
        }

        // Add more creative suggestions if needed
        if (count($suggestions) < 5) {
            $suffixes = ['pro', 'master', 'star', 'ace', 'ninja', 'guru', 'wizard'];
            foreach ($suffixes as $suffix) {
                if (count($suggestions) >= 5) {
                    break;
                }

                $candidate = $base.$suffix;
                $query = static::where('username', $candidate);

                if ($existingEmail) {
                    $query->where('email', '!=', $existingEmail);
                }

                if (! $query->exists()) {
                    $suggestions[] = $candidate;
                }
            }
        }

        return array_slice($suggestions, 0, 5);
    }

    /**
     * Check if username is available
     */
    public static function isUsernameAvailable(string $username, ?string $existingEmail = null): bool
    {
        $query = static::where('username', $username);

        if ($existingEmail) {
            $query->where('email', '!=', $existingEmail);
        }

        return ! $query->exists();
    }

    /**
     * Validate username format
     */
    public static function isValidUsername(string $username): bool
    {
        return preg_match('/^([a-zA-Z0-9][a-zA-Z0-9_\-.]{0,47})$/', $username) === 1;
    }

    /**
     * Get username validation rules
     */
    public static function getUsernameValidationRules(?int $ignoreUserId = null): array
    {
        $rules = [
            'required',
            'string',
            'max:48',
            'regex:/^([a-zA-Z0-9][a-zA-Z0-9_\-.]{0,47})$/',
        ];

        if ($ignoreUserId) {
            $rules[] = Rule::unique('users')->ignore($ignoreUserId);
        } else {
            $rules[] = 'unique:users,username';
        }

        return $rules;
    }
}
