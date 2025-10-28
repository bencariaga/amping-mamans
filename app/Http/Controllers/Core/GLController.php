<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Operation\Application;
use App\Models\Operation\GuaranteeLetter;
use App\Support\Number;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GLController extends Controller
{
    public function createForApplication($application, $budgetUpdate)
    {
        $guaranteeLetter = GuaranteeLetter::create([
            'gl_id' => 'GL-'.Carbon::now()->year.'-'.Str::padLeft(GuaranteeLetter::count() + 1, 9, '0'),
            'application_id' => $application->application_id,
            'budget_update_id' => $budgetUpdate->budget_update_id,
        ]);

        return $guaranteeLetter;
    }

    private function numberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion',
        ];

        if (! is_numeric($number)) {
            return '';
        }

        if ($number < 0) {
            return $negative.$this->numberToWords(Number::abs($number));
        }

        $string = $fraction = null;
        $numberString = (string) $number;

        if (Str::contains((string) $number, '.') !== false) {
            $number = Str::beforeLast($numberString, '.');
            $fraction = Str::afterLast($numberString, '.');
        }

        $number = (int) $number;

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];

                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }

                break;
            case $number < 1000:
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];

                if ($remainder) {
                    $string .= $conjunction.$this->numberToWords($remainder);
                }

                break;
            default:
                $baseUnit = Number::pow(1000, Number::floor(Number::log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->numberToWords($numBaseUnits).' '.$dictionary[$baseUnit];

                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->numberToWords($remainder);
                }

                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];

            foreach (Str::of((string) $fraction)->split(1) as $numberDigit) {
                $words[] = $dictionary[(int) $numberDigit];
            }

            $string .= collect($words)->join(' ');
        }

        return $string;
    }

    private function aliasId(string $id, string $prefix): string
    {
        $withoutPrefix = Str::after($id, $prefix.'-');

        if (Str::contains($withoutPrefix, '-') !== false) {
            [$year, $num] = Str::of($withoutPrefix)->explode('-', 2)->all();
        } else {
            $year = Str::substr($withoutPrefix, 0, 4);
            $num = Str::substr($withoutPrefix, 4);
        }

        $numLast5 = Str::substr($num, -5);

        return $year.'-'.$numLast5;
    }

    public function generatePDF($application)
    {
        $application = Application::where('application_id', $application)->first();

        if (! $application) {
            abort(404);
        }

        $gl = GuaranteeLetter::where('application_id', $application->application_id)->orderBy('gl_id', 'desc')->first();

        if (! $gl) {
            abort(404);
        }

        $assistanceAmount = $application->assistance_amount ?? 0;

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
            ->join('clients', 'patients.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->where('patients.patient_id', $application->patient_id)
            ->select('members.first_name', 'members.middle_name', 'members.last_name')
            ->first();

        $applicantFullName = '';

        if ($applicantMember) {
            $middleInitial = (isset($applicantMember->middle_name) && $applicantMember->middle_name) ? Str::substr($applicantMember->middle_name, 0, 1).'.' : '';
            $applicantFullName = Str::of(($applicantMember->last_name ?? '').', '.($applicantMember->first_name ?? '').' '.$middleInitial)->trim()->toString();
        }

        $patientFullName = '';
        if ($patientMember) {
            $middleInitial = (isset($patientMember->middle_name) && $patientMember->middle_name) ? Str::substr($patientMember->middle_name, 0, 1).'.' : '';
            $patientFullName = Str::of(($patientMember->last_name ?? '').', '.($patientMember->first_name ?? '').' '.$middleInitial)->trim()->toString();
        }

        $applicationAlias = $this->aliasId($application->application_id, 'APPLICATION');
        $glAlias = $this->aliasId($gl->gl_id, 'GL');

        $amountInteger = (int) Number::floor($assistanceAmount);
        $amountInWords = Str::upper(Str::of($this->numberToWords($amountInteger))->trim()->toString()).' PESOS ONLY';

        $assistanceAmountFormatted = Number::format($assistanceAmount, 0);
        $billedAmountFormatted = Number::format($application->billed_amount ?? 0, 0);

        $appliedAtFormatted = $application->applied_at ? Carbon::parse($application->applied_at)->format('F j, Y') : Carbon::now()->format('F j, Y');

        $data = [
            'application' => $application,
            'assistance_amount' => $assistanceAmount,
            'assistance_amount_formatted' => $assistanceAmountFormatted,
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
            'billed_amount_formatted' => $billedAmountFormatted,
        ];

        $pdf = PDF::loadView('pages.sidebar.application-entry.guarantee-letter', $data)
            ->setOption('zoom', 1.1)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('dpi', 300)
            ->setPaper('letter', 'portrait');

        return $pdf->inline($gl->gl_id.'.pdf');
    }
}
