<?php

namespace App\Exceptions;

use Exception;

class OptimisticLockingException extends Exception
{
    protected $message = 'Data telah dimodifikasi oleh pengguna lain. Silakan refresh halaman dan coba lagi.';
    protected $code = 409; // Conflict

    public function __construct($message = null, $code = 409, Exception $previous = null)
    {
        $message = $message ?? $this->message;
        parent::__construct($message, $code, $previous);
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
                'code' => $this->getCode()
            ], $this->getCode());
        }

        return back()->withErrors(['error' => $this->getMessage()]);
    }
}
