<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PopDatabaseTest extends TestCase
{
    /**
     * Test koneksi dan ketersediaan tabel m_pop di database.
     */
    public function test_tabel_m_pop_exists_in_database()
    {
        $this->assertTrue(
            Schema::hasTable('m_pop'),
            'Tabel m_pop harus tersedia di database.'
        );
    }

    /**
     * Test query data POP dari database.
     */
    public function test_query_pop_list_from_database()
    {
        $popList = DB::table('m_pop')
            ->where(function ($q) {
                $q->where('hide', '0')->orWhereNull('hide');
            })
            ->orderBy('nama_pop')
            ->get();

        $this->assertNotNull($popList);
    }

    /**
     * Test endpoint /test-pop-status mengembalikan response JSON valid.
     */
    public function test_pop_status_endpoint_returns_success()
    {
        $response = $this->get('/test-pop-status');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'source',
            'total_pop_aktif',
            'daftar_pop',
        ]);
        $response->assertJson([
            'status' => 'success',
            'source' => 'DATABASE (table: m_pop)',
        ]);
    }
}
