<?php

namespace App\Exceptions;

use Exception;

class OptimisticLockingException extends Exception
{
    protected $message = 'Data telah dimodifikasi oleh pengguna lain. Silakan refresh halaman dan coba lagi.';
    protected $code = 409; // Conflict
    protected $conflictData = [];

    public function __construct($message = null, $code = 409, Exception $previous = null, array $conflictData = [])
    {
        $message = $message ?? $this->message;
        $this->conflictData = $conflictData;
        parent::__construct($message, $code, $previous);
    }

    public function getConflictData(): array
    {
        return $this->conflictData;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'optimistic_locking_conflict',
                'message' => $this->getMessage(),
                'code' => $this->getCode(),
                'conflict_data' => $this->conflictData,
            ], $this->getCode());
        }

        return back()->withErrors([
            'error' => $this->getMessage(),
            'conflict_data' => $this->conflictData,
        ]);
    }
}
