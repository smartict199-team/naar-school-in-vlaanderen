<?php

declare(strict_types=1);

namespace App\Service\AuditLog;

use App\Entity\AuditLog;
use App\Entity\School;
use App\Entity\SchoolYear;
use App\Model\LoggableUserInterface;

class AuditLogFactory
{
    public function create(
        LoggableUserInterface $user,
        string $field,
        string $oldValue,
        string $newValue,
        ?string $name = null,
        ?School $school = null,
        ?SchoolYear $schoolYear = null
    ): AuditLog {
        $auditLog = new AuditLog();

        $auditLog->setEmail($user->getLoggableUserName());
        $auditLog->setUserId((string) $user->getId());
        $auditLog->setField($field);
        $auditLog->setOldValue($oldValue);
        $auditLog->setNewValue($newValue);
        $auditLog->setSchool($school);
        $auditLog->setSchoolYear($schoolYear);
        $auditLog->setName($name);

        return $auditLog;
    }
}
