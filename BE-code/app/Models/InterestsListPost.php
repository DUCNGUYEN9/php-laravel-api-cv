<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestsListPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'candidate_id',
        'post_id'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
