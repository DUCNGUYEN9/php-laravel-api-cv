<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'technology',
        'salary',
        'contact',
        'expired_date',
        'company_id',
        'carreer_id'
    ];
    public function carreer()
    {
        return $this->belongsTo(Carreer::class, 'carreer_id');
    }
    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function interestsListPost()
    {
        return $this->hasMany(InterestsListPost::class);
    }
    public function applyListPost()
    {
        return $this->hasMany(ApplyListPost::class);
    }
}
