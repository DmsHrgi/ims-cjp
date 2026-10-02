<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadband extends Model
{
    /**
     * Database connection khusus untuk database ke-2 (ims_v3)
     */
    protected $connection = 'ims_v3';

    /**
     * Tabel trx_batchjob_register
     */
    protected $table = 'trx_batchjob_register';

    /**
     * Primary key nomor_internet (string)
     */
    protected $primaryKey = 'nomor_internet';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Timestamp bawaan Laravel (date_create / date_update digunakan manual)
     */
    public $timestamps = false;

    /**
     * Guarded attributes
     */
    protected $guarded = [];

    /**
     * Kolom tanggal
     */
    protected $dates = [
        'date_create',
        'date_update',
    ];
}
