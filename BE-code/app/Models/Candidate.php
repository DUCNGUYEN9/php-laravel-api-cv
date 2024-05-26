<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'birth_date',
        'phone',
        // 'avatar',
        'school',
        'address',
        'bio',
        'job_position',
        'certification',
        'user_id'
    ];
    /**
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mailBox()
    {
        return $this->hasMany(MailBox::class);
    }
    public function applyListPost()
    {
        return $this->hasMany(ApplyListPost::class);
    }
    public function interestsListPost()
    {
        return $this->hasMany(InterestsListPost::class);
    }
}
