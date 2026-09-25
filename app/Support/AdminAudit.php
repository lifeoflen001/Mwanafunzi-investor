<?php

namespace App\Support;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;

final class AdminAudit
{
    public static function record(string $action, string $summary, ?Model $record = null, array $payload = []): void
    {
        AdminAuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $record ? $record::class : null,
            'auditable_id' => $record?->getKey(),
            'summary' => $summary,
            'payload' => $payload ?: null,
        ]);
    }
}
