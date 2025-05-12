<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    protected $fillable = ['name', 'email', 'password', 'is_pediatrician'];
    protected $hidden = ['password'];



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
    // In your User or ParentModel
    public function getIsPediatricianAttribute()
    {
        return $this->attributes['is_pediatrician'];  // Use $this->attributes to access the column value
    }

    // App\Models\User.php
    public function pediatrician()
{
    return $this->hasOne(Pediatrician::class, 'user_id');
}

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class, 'pediatrician_id');
    }
    // App\Models\User.php (أو App\Models\Parent.php إن وُجد)

    public function children()
    {
        return $this->hasMany(Child::class, 'parent_id');
    }
    // موديل User
    public function parent()
    {
        return $this->hasOne(ParentModel::class);  // assuming `parent` is a table with a foreign key `user_id`
    }
}
