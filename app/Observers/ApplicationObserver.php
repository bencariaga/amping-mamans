<?php

namespace App\Observers;

use App\Models\Operation\Application;
use App\Notifications\SmsNotification;
use Illuminate\Support\Facades\Log;

class ApplicationObserver
{
    public function created(Application $application): void
    {
        Log::info('Application created', [
            'application_id' => $application->application_id,
            'patient_id' => $application->patient_id,
            'assistance_amount' => $application->assistance_amount,
        ]);

        $this->sendNotificationToApplicant($application, 'Your application has been submitted successfully.');
    }

    public function updated(Application $application): void
    {
        if ($application->wasChanged('application_status')) {
            Log::info('Application status changed', [
                'application_id' => $application->application_id,
                'old_status' => $application->getOriginal('application_status'),
                'new_status' => $application->application_status,
            ]);

            $message = $this->getStatusChangeMessage($application->application_status);
            $this->sendNotificationToApplicant($application, $message);
        }
    }

    public function deleted(Application $application): void
    {
        Log::info('Application deleted', [
            'application_id' => $application->application_id,
        ]);
    }

    private function sendNotificationToApplicant(Application $application, string $message): void
    {
        try {
            $patient = $application->patient;
            $applicant = $patient?->applicant;
            $client = $applicant?->client;
            $contact = $client?->contacts()->where('contact_type', 'Application')->first();

            if ($contact && $contact->contact_number) {
                $client->member->account->notify(new SmsNotification($message));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send application notification', [
                'application_id' => $application->application_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getStatusChangeMessage(string $status): string
    {
        return match($status) {
            'Approved' => 'Your application has been approved.',
            'Rejected' => 'Your application has been rejected.',
            'Processing' => 'Your application is now being processed.',
            'Completed' => 'Your application has been completed.',
            'Cancelled' => 'Your application has been cancelled.',
            default => 'Your application status has been updated.',
        };
    }
}
