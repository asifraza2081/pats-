<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot the Auditable trait.
     */
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::logActivity($model, 'create');
        });

        static::updated(function (Model $model) {
            // Only log if something actually changed
            if ($model->wasChanged()) {
                self::logActivity($model, 'update');
            }
        });

        static::deleted(function (Model $model) {
            self::logActivity($model, 'delete');
        });
    }

    /**
     * Helper to log activity.
     */
    protected static function logActivity(Model $model, string $action)
    {
        if (app()->runningInConsole()) return;

        $piiFields = [
            'father_name', 'dob', 'cnic', 'phone',
            'permanent_address', 'postal_address',
            'photo_path', 'cnic_front_path',
            'bank_name', 'branch_code', 'transaction_id',
            'password', 'remember_token', 'otp'
        ];

        $payload = [
            'attributes' => array_diff_key($model->getAttributes(), array_flip($piiFields)),
            'changes'    => $action === 'update' ? array_diff_key($model->getChanges(), array_flip($piiFields)) : null,
        ];

        ActivityLog::log($action, $model, $payload);
    }
}
