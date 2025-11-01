<?php

namespace App\Actions\Core\Role;

class GetRoleOptions
{
    public function getAllowedActionsOptions(): array
    {
        return [
            'Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services',
            "Create, view, edit, archive, download, and print reports, including the AMPING's financial status and user activity data",
            'Create, view, edit, archive, download, and print templates for assistance request forms, guarantee letters, and text messages',
            'Create, view, edit, and delete tariff lists and change the version of tariff lists to use for assistance amount calculation',
            'Create, view, edit, and delete staff role names and client occupation names',
            'Assign and reassigned roles to staff members',
            'Approve or reject assistance requests and authorize guarantee letters',
            'Send text messages to applicants with approved guarantee letters',
            'Update, add to, and monitor the program budget from government funds, sponsors, and other sources',
            'Delete system cache and log data when necessary',
            'View and use assistance request templates to create assistance request forms',
            "View the AMPING's financial status, including the program budget sources from government funds, sponsors, and other sources",
            'View the staff role names and client occupation names',
            'View the roles of staff members',
            'View the version of tariff lists to use for assistance amount calculation',
            'View and use guarantee letter templates to create guarantee letters',
            'View accounts of staff members, applicants, sponsors, affiliate partners, and services',
            'View and use text message templates to create text messages',
        ];
    }

    public function getAccessScopeOptions(): array
    {
        return [
            'Full access to every web page, every feature, and every module, without restrictions',
            'Full access to profiles and system activities of staff members, applicants, patients, sponsors, and affiliate partners',
            'Full access to templates for assistance request forms, guarantee letters, and text messages',
            'Full access to financial records, such as budgets, expenses, and funding sources',
            'Full access to staff role and client occupation names, and tariff lists',
            'Full access to staff role and tariff list adjustments',
            'Full access to data and account archiving, deletion, and deactivation',
            'Full access to logs and reports',
            'Access limited to viewing and editing account profiles',
            'Access limited to viewing templates for assistance request forms',
            'Access limited to viewing financial records, such as budgets, expenses, and funding sources',
            'Access limited to viewing staff roles, client occupations, and tariff list versions',
            'Access limited to viewing account profiles',
            'Access limited to viewing templates for guarantee letters',
            'Access limited to approving and rejecting assistance requests and authorizing guarantee letters',
            'Access limited to viewing templates for text messages',
            'Access limited to sending text messages to applicants with approved guarantee letters',
        ];
    }

    public function matchOptionsFromString(?string $stored, array $options): array
    {
        $result = [];

        if ($stored === null || $stored === '') {
            return $result;
        }

        $hay = mb_strtolower($stored);

        foreach ($options as $opt) {
            if ($opt === null) {
                continue;
            }

            $needle = mb_strtolower($opt);

            if (mb_strpos($hay, $needle) !== false) {
                $result[] = $opt;
            }
        }

        return $result;
    }
}
