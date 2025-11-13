<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebSocketBroadcast
{
    private static $wsServerUrl = 'http://localhost:6001';

    /**
     * Broadcast update to specific channel
     */
    public static function broadcast($channel, $event, $data = [])
    {
        try {
            $response = Http::timeout(2)->post(self::$wsServerUrl . '/broadcast', [
                'channel' => $channel,
                'event' => $event,
                'data' => $data
            ]);

            if ($response->successful()) {
                Log::info("WebSocket broadcast sent: {$event} on channel {$channel}");
                return true;
            } else {
                Log::warning("WebSocket broadcast failed: {$response->body()}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WebSocket broadcast error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast to all clients
     */
    public static function broadcastAll($event, $data = [])
    {
        try {
            $response = Http::timeout(2)->post(self::$wsServerUrl . '/broadcast-all', [
                'event' => $event,
                'data' => $data
            ]);

            if ($response->successful()) {
                Log::info("WebSocket broadcast sent to all: {$event}");
                return true;
            } else {
                Log::warning("WebSocket broadcast to all failed: {$response->body()}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WebSocket broadcast to all error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast kunjungan update
     */
    public static function kunjunganUpdated($kunjungan)
    {
        return self::broadcast('kunjungan', 'kunjungan.updated', [
            'id' => $kunjungan->id,
            'no_reg' => $kunjungan->no_reg,
            'nm_p' => $kunjungan->nm_p,
            'tgl_reg' => $kunjungan->tgl_reg,
        ]);
    }

    /**
     * Broadcast kunjungan created
     */
    public static function kunjunganCreated($kunjungan)
    {
        return self::broadcast('kunjungan', 'kunjungan.created', [
            'id' => $kunjungan->id,
            'no_reg' => $kunjungan->no_reg,
            'nm_p' => $kunjungan->nm_p,
            'tgl_reg' => $kunjungan->tgl_reg,
        ]);
    }

    /**
     * Broadcast kunjungan deleted
     */
    public static function kunjunganDeleted($kunjunganId)
    {
        return self::broadcast('kunjungan', 'kunjungan.deleted', [
            'id' => $kunjunganId
        ]);
    }

    /**
     * Broadcast poli update
     */
    public static function poliUpdated($poli)
    {
        return self::broadcast('poli', 'poli.updated', [
            'id' => $poli->id,
            'poli_desc' => $poli->poli_desc,
            'aktif' => $poli->aktif,
        ]);
    }

    /**
     * Broadcast poli created
     */
    public static function poliCreated($poli)
    {
        return self::broadcast('poli', 'poli.created', [
            'id' => $poli->id,
            'poli_desc' => $poli->poli_desc,
            'aktif' => $poli->aktif,
        ]);
    }

    /**
     * Broadcast poli deleted
     */
    public static function poliDeleted($poliId)
    {
        return self::broadcast('poli', 'poli.deleted', [
            'id' => $poliId
        ]);
    }
}

