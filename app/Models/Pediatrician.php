<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Pediatrician extends Model
{
    use HasFactory;

    protected $fillable = ['name','prenom', 'email', 'password', 'hospital', 'rpps_number'];

    // في نموذج Pediatrician
public function children()
{
    return $this->hasMany(Child::class);
}
public function getFullNameAttribute()
{
    return 'Dr. ' . trim($this->prenom . ' ' . $this->name);
}


}
