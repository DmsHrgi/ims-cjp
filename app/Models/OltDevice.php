<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OltDevice extends Model
{
    use HasFactory;

    protected $table = 'm_olt';

    protected $fillable = [
        'name',
        'hostname',
        'ip_address',
        'vendor',
        'model',
        'status',
        'snmp_port',
        'snmp_version',
        'snmp_community',
        'location',
        'description',
        'user_create',
        'user_update',
    ];

    protected $casts = [
        'snmp_port' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
