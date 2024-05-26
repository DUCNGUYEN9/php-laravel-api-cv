<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailBox extends Model
{
    use HasFactory;
    protected $fillable = [
        'messages',
        'send_at',
        'candidate_id',
        'company_id'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->send_at = now();
        });
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
