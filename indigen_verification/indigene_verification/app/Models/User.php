<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'state_of_origin',
        'lga',
        'assigned_lga',
        'assigned_staff_id',
        'address',
        'place_of_birth',
        'nin',
        'role',
        'temporary_id',
        'permanent_id',
        'is_verified',
        'nin_verification_status',
        'nin_verification_failure_reason',
        'nin_verification_data',
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
            'password' => 'hashed',
            'nin_verification_data' => 'array',
        ];
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'assigned_staff_id');
    }

    /**
     * Check if this user can view another user based on staff assignment
     */
    public function canViewUser(User $otherUser): bool
    {
        // Admins can view all users
        if ($this->role === 'admin') {
            return true;
        }

        // Staff can only view users assigned to them
        if ($this->role === 'staff') {
            return $otherUser->assigned_staff_id === $this->id;
        }

        // Regular users can only view themselves
        return $this->id === $otherUser->id;
    }

    /**
     * Get users that this staff can view
     */
    public function getViewableUsers()
    {
        if ($this->role === 'admin') {
            return User::where('role', 'user')->orderBy('name');
        }

        if ($this->role === 'staff') {
            return User::where('role', 'user')
                      ->where('assigned_staff_id', $this->id)
                      ->orderBy('name');
        }

        return User::where('id', $this->id);
    }

    /**
     * Get available staff for a specific LGA
     */
    public static function getAvailableStaffForLga($lga)
    {
        return self::where('role', 'staff')
                  ->where('assigned_lga', $lga)
                  ->orderBy('name')
                  ->get();
    }

    /**
     * Assign a user to a random staff member in their LGA
     */
    public static function assignUserToRandomStaff($user)
    {
        if (empty($user->lga)) {
            return false;
        }

        $availableStaff = self::getAvailableStaffForLga($user->lga);
        
        if ($availableStaff->isEmpty()) {
            return false;
        }

        // Randomly select a staff member
        $randomStaff = $availableStaff->random();
        
        $user->assigned_staff_id = $randomStaff->id;
        $user->save();

        return $randomStaff;
    }

    /**
     * Reassign users to staff members in their LGA (for load balancing)
     */
    public static function reassignUsersToStaff()
    {
        $users = self::where('role', 'user')
                    ->whereNull('assigned_staff_id')
                    ->get();

        foreach ($users as $user) {
            self::assignUserToRandomStaff($user);
        }

        // Also reassign existing users for better load balancing
        $usersWithStaff = self::where('role', 'user')
                             ->whereNotNull('assigned_staff_id')
                             ->get();

        foreach ($usersWithStaff as $user) {
            self::assignUserToRandomStaff($user);
        }
    }
}
