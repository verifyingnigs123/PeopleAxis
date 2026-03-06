<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table            = 'otp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';  // No updated_at column in this table

    // Validation
    protected $allowedFields = ['email', 'otp', 'expires_at', 'is_used', 'created_at'];

    /**
     * Generate and save OTP for an email
     */
    public function generateOtp($email)
    {
        // Delete any existing unused OTPs for this email
        $this->where('email', $email)
             ->where('is_used', 0)
             ->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create expiration time (10 minutes from now)
        $expiresAt = date('Y-m-d H:i:s', time() + (10 * 60));

        // Save OTP
        $this->insert([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => $expiresAt,
            'is_used'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOtp($email, $otp)
    {
        $record = $this->where('email', $email)
                       ->where('otp', $otp)
                       ->where('is_used', 0)
                       ->first();

        if (!$record) {
            return false;
        }

        // Check if OTP has expired
        if (strtotime($record->expires_at) < time()) {
            return false;
        }

        return $record;
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed($otpId)
    {
        return $this->update($otpId, ['is_used' => 1]);
    }

    /**
     * Clean expired OTPs
     */
    public function cleanExpiredOtps()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
                    ->delete();
    }
}
