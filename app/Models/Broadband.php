<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadband extends Model
{
    use HasFactory;

    protected $table = 'trx_broadband';
    protected $primaryKey = 'nomor_internet';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'is_login' => 'integer',
        'periode_billing' => 'integer',
        'date_create' => 'datetime',
        'date_update' => 'datetime',
    ];
}
