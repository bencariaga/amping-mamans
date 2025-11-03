<?php

namespace App\Actions\Miscellaneous;

class GetAccessScopeOptions
{
    public static function execute(): array
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
}
