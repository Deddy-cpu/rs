<?php

namespace App\Console\Commands;

use App\Models\Kunjungan;
use App\Models\Transaksi;
use App\Exceptions\OptimisticLockingException;
use Illuminate\Console\Command;

class TestOptimisticLocking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:optimistic-locking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test optimistic locking functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Optimistic Locking System...');
        
        // Get a kunjungan record
        $kunjungan = Kunjungan::first();
        
        if (!$kunjungan) {
            $this->error('No kunjungan records found. Please create some test data first.');
            return;
        }
        
        $this->info("Testing with Kunjungan ID: {$kunjungan->id}, Version: {$kunjungan->version}");
        
        // Test 1: Normal update should work
        $this->info('Test 1: Normal update...');
        try {
            $kunjungan->updateWithOptimisticLock(['nm_p' => 'Test Patient'], $kunjungan->version);
            $this->info('✓ Normal update successful');
        } catch (OptimisticLockingException $e) {
            $this->error('✗ Normal update failed: ' . $e->getMessage());
        }
        
        // Test 2: Version conflict should fail
        $this->info('Test 2: Version conflict...');
        try {
            $kunjungan->updateWithOptimisticLock(['nm_p' => 'Another Test'], $kunjungan->version - 1);
            $this->error('✗ Version conflict test failed - should have thrown exception');
        } catch (OptimisticLockingException $e) {
            $this->info('✓ Version conflict detected correctly: ' . $e->getMessage());
        }
        
        // Test 3: Check version increment
        $this->info('Test 3: Version increment...');
        $originalVersion = $kunjungan->version;
        $kunjungan->incrementVersion('Test User');
        $this->info("Version incremented from {$originalVersion} to {$kunjungan->version}");
        
        $this->info('Optimistic Locking Test Complete!');
    }
}
