<?php

namespace App\Actions\Application;

use App\Actions\GuaranteeLetter\CreateGuaranteeLetter;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Financial\BudgetUpdateController;
use App\Models\Operation\Application;
use App\Models\Operation\ExpenseRange;
use App\Services\FakeSmsService;
use App\Services\TextBeeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateApplication
{
    public function execute(
        Request $request,
        BudgetUpdateController $budgetUpdateController,
        MessageController $messageController,
        TextBeeService $textBeeService,
        FakeSmsService $fakeSmsService
    ): Application {
        $billedAmount = (int) Str::replace(',', '', $request->input('billed_amount'));
        $assistanceAmount = (int) Str::replace(',', '', $request->input('assistance_amount'));
        $serviceId = $request->input('service_id');
        $tariffListVersion = $request->input('tariff_list_version');

        $latestBudget = $budgetUpdateController->getLatestBudget()->getData();
        if ($latestBudget->amount_recent < $assistanceAmount) {
            throw new Exception('Allocate budget or provide supplementary budget first. The AMPING budget is currently lower than the assistance amount needed.');
        }

        $expRange = ExpenseRange::where('tariff_list_id', $tariffListVersion)
            ->where('service_id', $serviceId)
            ->where('exp_range_min', '<=', $billedAmount)
            ->where('exp_range_max', '>=', $billedAmount)
            ->first();

        $data = [
            'applicant_id' => $request->input('applicant_id'),
            'patient_id' => $request->input('patient_id'),
            'affiliate_partner_id' => $request->input('affiliate_partner_id'),
            'exp_range_id' => $expRange->exp_range_id ?? null,
            'billed_amount' => $billedAmount,
            'assistance_amount' => $assistanceAmount,
            'applied_at' => $request->input('applied_at'),
            'reapply_at' => $request->input('reapply_at'),
        ];

        $application = Application::create($data);

        $budgetUpdate = $budgetUpdateController->createForApplication($application, $assistanceAmount);
        app(CreateGuaranteeLetter::class)->execute($application, $budgetUpdate);

        $request->merge(['application_id' => $application->application_id]);
        $messageId = $messageController->sendMessage($request, $textBeeService, $fakeSmsService);
        $application->update(['message_id' => $messageId]);

        return $application;
    }
}
