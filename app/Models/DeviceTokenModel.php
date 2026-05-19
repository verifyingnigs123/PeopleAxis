<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceTokenModel extends Model
{
    protected $table            = 'device_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $allowedFields = ['user_id', 'device_token', 'device_name', 'ip_address', 'user_agent', 'created_at', 'expires_at'];

    /**
     * Create a device token for a user
     */
    public function createDeviceToken($userId, $deviceName, $ipAddress, $userAgent, $expiryDays = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));

        $this->insert([
            'user_id'     => $userId,
            'device_token' => $token,
            'device_name' => $deviceName,
            'ip_address'  => $ipAddress,
            'user_agent'  => $userAgent,
            'created_at'  => date('Y-m-d H:i:s'),
            'expires_at'  => $expiresAt,
        ]);

        return $token;
    }

    /**
     * Verify a device token
     */
    public function verifyDeviceToken($userId, $deviceToken)
    {
        $record = $this->where('user_id', $userId)
                       ->where('device_token', $deviceToken)
                       ->first();

        if (!$record) {
            log_message('warning', '[DeviceTokenModel] Device token not found for user: ' . $userId);
            return false;
        }

        // Check if token has expired
        if (!empty($record->expires_at) && strtotime($record->expires_at) < time()) {
            log_message('warning', '[DeviceTokenModel] Device token expired for user: ' . $userId);
            $this->delete($record->id);
            return false;
        }

        return $record;
    }

    /**
     * Get all active devices for a user
     */
    public function getUserDevices($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('expires_at >', date('Y-m-d H:i:s'), false)
                    ->orWhere('expires_at', null)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Revoke a device token
     */
    public function revokeDevice($userId, $deviceTokenId)
    {
        return $this->where('user_id', $userId)
                    ->where('id', $deviceTokenId)
                    ->delete();
    }

    /**
     * Revoke all devices for a user
     */
    public function revokeAllDevices($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    /**
     * Clean expired device tokens
     */
    public function cleanExpiredTokens()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'), false)->delete();
    }
}
