<?php

namespace App\Actions\GuaranteeLetter;

use App\Models\Operation\Application;
use App\Models\Operation\GuaranteeLetter;
use App\Support\Number;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateGLPDF
{
    public function execute($applicationId)
    {
        $application = Application::where('application_id', $applicationId)
            ->with([
                'applicant.client.member',
                'patient.applicant.client.member',
                'affiliate_partner',
                'expense_range.service',
            ])
            ->first();

        if (! $application) {
            abort(404);
        }

        $gl = GuaranteeLetter::where('application_id', $application->application_id)->orderBy('gl_id', 'desc')->first();

        if (! $gl) {
            abort(404);
        }

        $assistanceAmount = $application->assistance_amount ?? 0;
        $billedAmount = $application->billed_amount ?? 0;

        $mayor = DB::table('signers')
            ->join('members', 'signers.member_id', '=', 'members.member_id')
            ->where('members.member_type', 'Mayor')
            ->select('members.first_name', 'members.middle_name', 'members->last_name', 'members->suffix', 'signers->post_nominal_letters')
            ->first();

        $assistant = DB::table('signers')
            ->join('members', 'signers.member_id', '=', 'members.member_id')
            ->where('members.member_type', 'Executive Assistant')
            ->select('members.first_name', 'members.middle_name', 'members->last_name', 'members->suffix', 'signers->post_nominal_letters')
            ->first();

        $applicationDate = $application->application_date ? Carbon::parse($application->application_date)->format('m/d/Y') : '';
        $reapplicationDate = $application->reapplication_date ? Carbon::parse($application->reapplication_date)->format('m/d/Y') : '';
        $assistanceAmountWords = Str::upper($this->numberToWords($assistanceAmount));

        $applicantMember = $application->applicant->client->member ?? null;
        $applicantMiddleName = $applicantMember->middle_name ?? '';
        $applicantMiddleInitial = $applicantMiddleName ? Str::limit($applicantMiddleName, 1, '').'.' : '';

        $patientMember = $application->patient->applicant->client->member ?? null;
        $patientMiddleName = $patientMember->middle_name ?? '';
        $patientMiddleInitial = $patientMiddleName ? Str::limit($patientMiddleName, 1, '').'.' : '';

        $placeholders = [
            '[$application->application_date]' => $applicationDate,
            '[$application->reapplication_date]' => $reapplicationDate,
            '[$application->affiliate_partner->ap_name]' => $application->affiliate_partner->ap_name ?? '[Affiliate Partner]',
            '[$application->billed_amount]' => number_format($billedAmount, 0),
            '[$application->assistance_amount]' => number_format($assistanceAmount, 0),
            '[$application->assistance_amount_words]' => $assistanceAmountWords,
            '[$application->applicant->client->member->first_name]' => $applicantMember->first_name ?? '[Applicant\'s First Name]',
            '[$application->applicant->client->member->middle_name]' => $applicantMiddleInitial,
            '[$application->applicant->client->member->last_name]' => $applicantMember->last_name ?? '[Applicant\'s Last Name]',
            '[$application->applicant->client->member->suffix]' => $applicantMember->suffix ?? '',
            '[$application->patient->applicant->client->member->first_name]' => $patientMember->first_name ?? '[Patient\'s First Name]',
            '[$application->patient->applicant->client->member->middle_name]' => $patientMiddleInitial,
            '[$application->patient->applicant->client->member->last_name]' => $patientMember->last_name ?? '[Patient\'s Last Name]',
            '[$application->patient->applicant->client->member->suffix]' => $patientMember->suffix ?? '',
            '[$application->expense_range->service->service]' => $application->expense_range->service->service ?? '[Service]',
            '[$application->applicant->barangay]' => $application->applicant->barangay ?? '[Barangay]',
        ];

        $content = Str::replace(array_keys($placeholders), array_values($placeholders), $gl->gl_content);

        $pdf = PDF::loadView('pages.sidebar.application-entry.guarantee-letter', ['content' => $content, 'application' => $application, 'mayor' => $mayor, 'assistant' => $assistant])
            ->setOption('zoom', 1.1)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('dpi', 300)
            ->setPaper('letter', 'portrait');

        return $pdf->inline($gl->gl_id.'.pdf');
    }

    private function numberToWords($number)
    {
        $hyphen = ' ';
        $conjunction = ' ';
        $separator = ' ';
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
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < -2147483648) {
            return 'Cannot convert number.';
        }

        $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        $string = $i = '';

        if ($number < 0) {
            $string = $negative;
            $number = abs($number);
        }

        switch (true) {
            case $number < 21:
                $string .= $dictionary[$number];

                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string .= $dictionary[$tens];

                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }

                break;
            case $number < 1000:
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
                $string .= $dictionary[$hundreds].' '.$dictionary[100];

                if ($remainder) {
                    $string .= $conjunction.$this->numberToWords($remainder);
                }

                break;
            default:
                $baseUnit = Number::pow(1000, Number::floor(Number::log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string .= $this->numberToWords($numBaseUnits).' '.$dictionary[$baseUnit];

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
}
