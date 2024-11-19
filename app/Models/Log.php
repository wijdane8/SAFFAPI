<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'action',
        'description',
        'os',
        'browser',
        'user_agent',
        'ip_address',
        'timestamp',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
