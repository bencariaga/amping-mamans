<?php

namespace App\Actions\Miscellaneous;

class GetAllowedActionsOptions
{
    public static function execute(): array
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
}
