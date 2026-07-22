<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\ConferenceUserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function talks()
    {
        return $this->hasMany(Talk::class);
    }

    public function bios(): HasMany
    {
        return $this->hasMany(Bio::class);
    }

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    public function conferencesStates(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class)
            ->using(ConferenceUser::class)
            ->withPivot('status');
    }

    public function favoritedConferences(): BelongsToMany
    {
        return $this->conferencesStates()
            ->wherePivot('status',ConferenceUserStatus::FAVORITED);
    }

    public function dismissedConferences(): BelongsToMany
    {
        return $this->conferencesStates()
            ->wherePivot('status',ConferenceUserStatus::DISMISSED);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('username', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }
}
