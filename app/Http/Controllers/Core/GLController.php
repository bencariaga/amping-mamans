<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Models\Operation\Application;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\GuaranteeLetter;
use App\Models\Operation\BudgetUpdate;
use App\Models\Storage\Data;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Exception;

class GLController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $lastNum = $max ? (int) Str::afterLast($max, '-') : 0;

        return $base . '-' . Str::padLeft($lastNum + 1, 9, '0');
    }

    private function numberToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return '';
        }

        if ($number < 0) {
            return $negative . $this->numberToWords(\abs($number));
        }

        $string = $fraction = null;

        if (\strpos((string)$number, '.') !== false) {
            list($number, $fraction) = \explode('.', (string)$number);
        }

        $number = (int) $number;

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = (int) ($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->numberToWords($remainder);
                }
                break;
            default:
                $baseUnit = \pow(1000, \floor(\log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->numberToWords($remainder);
                }
                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (\str_split((string) $fraction) as $numberDigit) {
                $words[] = $dictionary[(int)$numberDigit];
            }
            $string .= \implode(' ', $words);
        }

        return $string;
    }

    private function aliasId(string $id, string $prefix): string
    {
        $withoutPrefix = Str::after($id, $prefix . '-');
        if (\strpos($withoutPrefix, '-') !== false) {
            [$year, $num] = \explode('-', $withoutPrefix, 2);
        } else {
            $year = \substr($withoutPrefix, 0, 4);
            $num = \substr($withoutPrefix, 4);
        }
        $numLast5 = \substr($num, -5);
        return $year . '-' . $numLast5;
    }

    public function authorizeApplication(Request $request, $application)
    {
        $application = Application::where('application_id', $application)->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
        }

        $expenseRange = ExpenseRange::where('exp_range_id', $application->exp_range_id)->first();
        $assistAmount = $expenseRange->assist_amount ?? 0;

        DB::beginTransaction();

        try {
            $prevBudget = BudgetUpdate::join('data', 'budget_updates.data_id', '=', 'data.data_id')
                ->orderBy('data.created_at', 'desc')
                ->select('budget_updates.*')
                ->first();

            $prevAmountAccum = $prevBudget->amount_accum ?? 0;
            $prevAmountRecent = $prevBudget->amount_recent ?? 0;
            $prevAmountSpent = $prevBudget->amount_spent ?? 0;

            $amount_before = $prevAmountRecent;
            $amount_change = (float) $assistAmount;
            $amount_recent = $amount_before - $amount_change;
            $amount_spent = $prevAmountSpent + $amount_change;
            $amount_accum = $prevAmountAccum;

            $budgetData = Data::create([
                'data_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $budgetUpdate = BudgetUpdate::create([
                'data_id' => $budgetData->data_id,
                'sponsor_id' => null,
                'possessor' => 'AMPING',
                'amount_accum' => $amount_accum,
                'amount_recent' => $amount_recent,
                'amount_before' => $amount_before,
                'amount_change' => $amount_change,
                'amount_spent' => $amount_spent,
                'direction' => 'Decrease',
                'reason' => 'GL Release'
            ]);

            $glId = $this->generateNextId('GL', 'guarantee_letters', 'gl_id');

            GuaranteeLetter::create([
                'gl_id' => $glId,
                'application_id' => $application->application_id,
                'budget_update_id' => $budgetUpdate->budget_update_id,
                'gl_status' => 'Approved',
                'signers' => null
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assistance request has been approved.',
                'status' => 'Approved',
                'disableButtons' => ['authorize', 'reject'],
                'previewEnabled' => true,
                'previewUrl' => url("/applications/{$application->application_id}/guarantee-letter")
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to authorize request: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $application)
    {
        $application = Application::where('application_id', $application)->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
        }

        try {
            $glId = $this->generateNextId('GL', 'guarantee_letters', 'gl_id');

            GuaranteeLetter::create([
                'gl_id' => $glId,
                'application_id' => $application->application_id,
                'budget_update_id' => null,
                'gl_status' => 'Rejected',
                'signers' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assistance request has been rejected.',
                'status' => 'Rejected',
                'disableButtons' => ['authorize', 'reject'],
                'previewEnabled' => false,
                'previewUrl' => null
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to reject request: ' . $e->getMessage()], 500);
        }
    }

    public function generatePDF($application)
    {
        $application = Application::where('application_id', $application)->first();

        if (!$application) {
            abort(404);
        }

        $gl = GuaranteeLetter::where('application_id', $application->application_id)
            ->orderBy('gl_id', 'desc')
            ->first();

        if (!$gl || $gl->gl_status !== 'Approved') {
            abort(404);
        }

        $expenseRange = ExpenseRange::where('exp_range_id', $application->exp_range_id)->first();
        $assistAmount = $expenseRange->assist_amount ?? 0;

        $mayor = DB::table('signers')
            ->join('members', 'signers.member_id', '=', 'members.member_id')
            ->where('members.member_type', 'Mayor')
            ->select('members.first_name', 'members.middle_name', 'members.last_name', 'members.suffix', 'signers.post_nominal_letters')
            ->first();

        $assistant = DB::table('signers')
            ->join('members', 'signers.member_id', '=', 'members.member_id')
            ->where('members.member_type', 'Executive Assistant')
            ->select('members.first_name', 'members.middle_name', 'members.last_name', 'members.suffix', 'signers.post_nominal_letters')
            ->first();

        $affiliatePartnerName = DB::table('affiliate_partners')->where('affiliate_partner_id', $application->affiliate_partner_id)->value('affiliate_partner_name') ?? '[affiliate partner]';

        $serviceType = DB::table('expense_ranges')
            ->join('services', 'expense_ranges.service_id', '=', 'services.service_id')
            ->where('expense_ranges.exp_range_id', $application->exp_range_id)
            ->value('services.service_type') ?? '[SERVICE TYPE]';

        $barangay = DB::table('applicants')->where('applicant_id', $application->applicant_id)->value('barangay') ?? '';

        $applicantMember = DB::table('applicants')
            ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->where('applicants.applicant_id', $application->applicant_id)
            ->select('members.first_name', 'members.middle_name', 'members.last_name')
            ->first();

        $patientMember = DB::table('patients')
            ->join('members', 'patients.member_id', '=', 'members.member_id')
            ->where('patients.patient_id', $application->patient_id)
            ->select('members.first_name', 'members.middle_name', 'members.last_name')
            ->first();

        $applicantFullName = '';
        if ($applicantMember) {
            $applicantFullName = \trim(($applicantMember->last_name ?? '') . ', ' . ($applicantMember->first_name ?? '') . ' ' . (isset($applicantMember->middle_name) && $applicantMember->middle_name ? \substr($applicantMember->middle_name, 0, 1) . '.' : ''));
        }

        $patientFullName = '';
        if ($patientMember) {
            $patientFullName = \trim(($patientMember->last_name ?? '') . ', ' . ($patientMember->first_name ?? '') . ' ' . (isset($patientMember->middle_name) && $patientMember->middle_name ? \substr($patientMember->middle_name, 0, 1) . '.' : ''));
        }

        $applicationAlias = $this->aliasId($application->application_id, 'APPLICATION');
        $glAlias = $this->aliasId($gl->gl_id, 'GL');

        $amountInteger = (int) \floor($assistAmount);
        $amountInWords = \strtoupper(\trim($this->numberToWords($amountInteger))) . ' PESOS ONLY';

        $assistAmountFormatted = \number_format($assistAmount, 2);
        $billedAmountFormatted = \number_format($application->billed_amount ?? 0, 2);

        $appliedAtFormatted = $application->applied_at ? Carbon::parse($application->applied_at)->format('F j, Y') : Carbon::now()->format('F j, Y');

        $data = [
            'application' => $application,
            'assist_amount' => $assistAmount,
            'assist_amount_formatted' => $assistAmountFormatted,
            'amount_in_words' => $amountInWords,
            'gl_id' => $gl->gl_id,
            'gl_alias' => $glAlias,
            'application_alias' => $applicationAlias,
            'mayor' => $mayor,
            'assistant' => $assistant,
            'pdf' => true,
            'applied_at_formatted' => $appliedAtFormatted,
            'affiliate_partner_name' => $affiliatePartnerName,
            'service_type' => $serviceType,
            'barangay' => $barangay,
            'applicant_full_name' => $applicantFullName,
            'patient_full_name' => $patientFullName,
            'billed_amount_formatted' => $billedAmountFormatted
        ];

        $pdf = PDF::loadView('pages.sidebar.application-entry.guarantee-letter', $data)
            ->setOption('zoom', 1.1)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('dpi', 300)
            ->setPaper('letter', 'portrait');

        return $pdf->inline($gl->gl_id . '.pdf');
    }
}
