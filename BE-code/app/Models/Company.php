<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'address',
        'website',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mailBox()
    {
        return $this->hasMany(MailBox::class);
    }
    public function post()
    {
        return $this->hasMany(Post::class);
    }
    public function carreer()
    {
        return $this->hasMany(Carreer::class);
    }
}
