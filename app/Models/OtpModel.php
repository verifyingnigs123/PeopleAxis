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

        // Create expiration time (10 minutes from now) using app timezone
        $now = time();
        $expiresAt = date('Y-m-d H:i:s', $now + (10 * 60));

        log_message('debug', '[generateOtp] Creating OTP. Now: ' . date('Y-m-d H:i:s', $now) . ', ExpiresAt: ' . $expiresAt . ', AppTimezone: ' . app_timezone());

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
        // Trim OTP in case of whitespace
        $otp = trim($otp);
        
        log_message('debug', '[OtpModel.verifyOtp] Checking OTP for email: ' . $email . ', provided OTP: ' . $otp);

        $record = $this->where('email', $email)
                       ->where('otp', $otp)
                       ->where('is_used', 0)
                       ->first();

        if (!$record) {
            // Debug: Let's see what OTPs exist for this email
            $allRecords = $this->where('email', $email)->orderBy('created_at', 'DESC')->findAll();
            log_message('debug', '[OtpModel.verifyOtp] No matching OTP found. Total OTP records for email: ' . count($allRecords));
            foreach ($allRecords as $rec) {
                log_message('debug', '[OtpModel.verifyOtp] Existing OTP: ' . $rec->otp . ', is_used: ' . $rec->is_used . ', expires_at: ' . $rec->expires_at);
            }
            return false;
        }

        log_message('debug', '[OtpModel.verifyOtp] OTP record found, checking expiration');

        // Check if OTP has expired
        if (strtotime($record->expires_at) < time()) {
            log_message('warning', '[OtpModel.verifyOtp] OTP has expired. expires_at: ' . $record->expires_at . ', current_time: ' . date('Y-m-d H:i:s'));
            return false;
        }

        log_message('info', '[OtpModel.verifyOtp] OTP verified successfully');
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
