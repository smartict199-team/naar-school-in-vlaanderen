<?php

declare(strict_types=1);

namespace App\Model;

final class AuditLogFields
{
    public const LOG_PREFIX = 'underrepresented_';

    public const BOOLEAN_FIELDS = [
        'capacity_reached',
        'hidden',
        'indicator_student_seats_percentage_visible',
    ];
    /**
     * @var array<array-key, string>
     */
    public static array $fields = [
        'capacity',
        'student_seats_taken',
        'capacity_reached',
        'indicator_student_seats_percentage',
        'indicator_student_seats_percentage_visible',
        'indicator_student_seats_taken',
        'student_seats_taken_urg',
        'underrepresented_student_seats_percentage',
        'underrepresented_student_seats_percentage_visible',
        'underrepresented_student_seats_taken',
    ];

    public static array $underrepresentedGroupFields = [
        'description',
        'student_seats_percentage',
        'student_seats_taken',
    ];
}
