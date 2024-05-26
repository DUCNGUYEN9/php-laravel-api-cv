<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carreer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'company_id'
    ];

    public function post()
    {
        return $this->hasMany(Post::class);
    }
    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
