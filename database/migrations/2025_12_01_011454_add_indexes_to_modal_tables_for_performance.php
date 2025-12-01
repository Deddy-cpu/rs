<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * OPTIMASI: Menambahkan index untuk mempercepat query filtering dan searching
     */
    public function up(): void
    {
        // Index untuk tindakan_tarif table
        Schema::table('tindakan_tarif', function (Blueprint $table) {
            // Index untuk filtering aktif
            if (!$this->hasIndex('tindakan_tarif', 'tindakan_tarif_aktif_index')) {
                $table->index('aktif', 'tindakan_tarif_aktif_index');
            }
            
            // Index untuk filtering eselon_grp_id
            if (!$this->hasIndex('tindakan_tarif', 'tindakan_tarif_eselon_grp_id_index')) {
                $table->index('eselon_grp_id', 'tindakan_tarif_eselon_grp_id_index');
            }
            
            // Composite index untuk aktif + eselon_grp_id (sering digunakan bersama)
            if (!$this->hasIndex('tindakan_tarif', 'tindakan_tarif_aktif_eselon_grp_id_index')) {
                $table->index(['aktif', 'eselon_grp_id'], 'tindakan_tarif_aktif_eselon_grp_id_index');
            }
        });
        
        // Index untuk farmalkes table
        Schema::table('farmalkes', function (Blueprint $table) {
            // Index untuk filtering aktif
            if (!$this->hasIndex('farmalkes', 'farmalkes_aktif_index')) {
                $table->index('aktif', 'farmalkes_aktif_index');
            }
            
            // Index untuk nama_item (sering digunakan untuk search)
            if (!$this->hasIndex('farmalkes', 'farmalkes_nama_item_index')) {
                $table->index('nama_item', 'farmalkes_nama_item_index');
            }
        });
        
        // Index untuk tindakan_q table (untuk search tindakan_q_desc)
        if (Schema::hasTable('tindakan_q')) {
            Schema::table('tindakan_q', function (Blueprint $table) {
                if (!$this->hasIndex('tindakan_q', 'tindakan_q_desc_index')) {
                    $table->index('tindakan_q_desc', 'tindakan_q_desc_index');
                }
            });
        }
        
        // Index untuk grp_eselon table (untuk search grp_eselon_desc)
        if (Schema::hasTable('grp_eselon')) {
            Schema::table('grp_eselon', function (Blueprint $table) {
                if (!$this->hasIndex('grp_eselon', 'grp_eselon_desc_index')) {
                    $table->index('grp_eselon_desc', 'grp_eselon_desc_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindakan_tarif', function (Blueprint $table) {
            $table->dropIndex('tindakan_tarif_aktif_index');
            $table->dropIndex('tindakan_tarif_eselon_grp_id_index');
            $table->dropIndex('tindakan_tarif_aktif_eselon_grp_id_index');
        });
        
        Schema::table('farmalkes', function (Blueprint $table) {
            $table->dropIndex('farmalkes_aktif_index');
            $table->dropIndex('farmalkes_nama_item_index');
        });
        
        if (Schema::hasTable('tindakan_q')) {
            Schema::table('tindakan_q', function (Blueprint $table) {
                $table->dropIndex('tindakan_q_desc_index');
            });
        }
        
        if (Schema::hasTable('grp_eselon')) {
            Schema::table('grp_eselon', function (Blueprint $table) {
                $table->dropIndex('grp_eselon_desc_index');
            });
        }
    }
    
    /**
     * Check if index exists
     */
    private function hasIndex(string $table, string $index): bool
    {
        try {
            $connection = Schema::getConnection();
            $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
            $doctrineTable = $doctrineSchemaManager->introspectTable($table);
            return $doctrineTable->hasIndex($index);
        } catch (\Exception $e) {
            // If table doesn't exist or error, return false
            return false;
        }
    }
};
