<?php

namespace App\Enums;

enum QuestionType: string
{
    case Produk = 'Produk';
    case Kemitraan = 'Kemitraan';
    case Karir = 'Karir';
    case VendorPenawaran = 'Vendor / Penawaran';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function normalize(array $questionTypes): array
    {
        $valid = self::values();
        $normalized = array_map(
            static fn ($questionType) => trim((string) $questionType),
            $questionTypes
        );

        return array_values(array_unique(array_filter(
            $normalized,
            static fn (string $questionType) => in_array($questionType, $valid, true)
        )));
    }

    public static function isAll(array $questionTypes): bool
    {
        $current = self::normalize($questionTypes);
        $all = self::values();

        sort($current);
        sort($all);

        return $current === $all;
    }

    public static function allowedForRoleNames(array $roleNames): array
    {
        $normalizedRoleNames = array_map(
            static fn ($roleName) => strtolower(trim((string) $roleName)),
            $roleNames
        );

        if (self::containsRole($normalizedRoleNames, ['admin access'])) {
            return self::values();
        }

        $allowed = [];

        if (
            self::containsRole($normalizedRoleNames, ['mkt access', 'marketing access']) ||
            self::containsKeyword($normalizedRoleNames, ['marketing', 'mkt'])
        ) {
            $allowed[] = self::Produk->value;
            $allowed[] = self::Kemitraan->value;
        }

        if (
            self::containsRole($normalizedRoleNames, ['hr access', 'hrd access']) ||
            self::containsKeyword($normalizedRoleNames, ['hrd', 'human resource', 'hr'])
        ) {
            $allowed[] = self::Karir->value;
        }

        if (
            self::containsRole($normalizedRoleNames, ['pch access', 'purchasing access']) ||
            self::containsKeyword($normalizedRoleNames, ['purchasing', 'procurement', 'pch'])
        ) {
            $allowed[] = self::VendorPenawaran->value;
        }

        return $allowed !== [] ? array_values(array_unique($allowed)) : self::values();
    }

    private static function containsRole(array $roleNames, array $targets): bool
    {
        foreach ($targets as $target) {
            if (in_array($target, $roleNames, true)) {
                return true;
            }
        }

        return false;
    }

    private static function containsKeyword(array $roleNames, array $keywords): bool
    {
        foreach ($roleNames as $roleName) {
            foreach ($keywords as $keyword) {
                if (str_contains($roleName, $keyword)) {
                    return true;
                }
            }
        }

        return false;
    }
}
