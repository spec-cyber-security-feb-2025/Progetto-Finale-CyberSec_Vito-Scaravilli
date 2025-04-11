<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log an action.
     *
     * @param string $action The action being performed
     * @param mixed $model The model being affected (optional)
     * @param array $oldValues Old values before change (optional)
     * @param array $newValues New values after change (optional)
     * @param array $details Additional details (optional)
     * @return \App\Models\AuditLog
     */
    public static function log($action, $model = null, $oldValues = null, $newValues = null, $details = null)
    {
        $userId = Auth::id();
        $ipAddress = Request::ip();
        $userAgent = Request::userAgent();
        
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'details' => $details,
        ];
        
        if ($model) {
            $logData['model_type'] = get_class($model);
            $logData['model_id'] = $model->id;
        }
        
        if ($oldValues) {
            $logData['old_values'] = $oldValues;
        }
        
        if ($newValues) {
            $logData['new_values'] = $newValues;
        }
        
        return AuditLog::create($logData);
    }
    
    /**
     * Log authentication events.
     *
     * @param string $event The authentication event (login, logout, register, etc.)
     * @param array $details Additional details (optional)
     * @return \App\Models\AuditLog
     */
    public static function authLog($event, $details = null)
    {
        return self::log('auth.' . $event, null, null, null, $details);
    }
    
    /**
     * Log article events.
     *
     * @param string $event The article event (create, update, delete, etc.)
     * @param \App\Models\Article $article The article being affected
     * @param array $oldValues Old values before change (optional)
     * @param array $newValues New values after change (optional)
     * @return \App\Models\AuditLog
     */
    public static function articleLog($event, $article, $oldValues = null, $newValues = null)
    {
        return self::log('article.' . $event, $article, $oldValues, $newValues);
    }
    
    /**
     * Log user role events.
     *
     * @param string $event The role event (assign, change, etc.)
     * @param \App\Models\User $user The user being affected
     * @param string $oldRole Old role before change
     * @param string $newRole New role after change
     * @return \App\Models\AuditLog
     */
    public static function roleLog($event, $user, $oldRole, $newRole)
    {
        return self::log('role.' . $event, $user, ['role' => $oldRole], ['role' => $newRole]);
    }
    
    /**
     * Log security events.
     *
     * @param string $event The security event (unauthorized_access, rate_limit, etc.)
     * @param array $details Additional details
     * @return \App\Models\AuditLog
     */
    public static function securityLog($event, $details)
    {
        return self::log('security.' . $event, null, null, null, $details);
    }
}