<?php

namespace App\Exports;

use App\Models\CareerApply;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CareerApplyExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $careerId;

    public function __construct($careerId = null)
    {
        $this->careerId = $careerId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = CareerApply::query()->with('career');
        
        if ($this->careerId) {
            $query->where('careerid', $this->careerId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No.',
            'Career Position',
            'Career Location',
            'Applicant Name',
            'Email',
            'Phone Number',
            'Birth of Date',
            'Education',
            'Experienced',
            'Current Salary',
            'Expectation Salary',
            'Status',
            'Reject Reason',
            'Experience List',
            'Applied Date',
        ];
    }

    /**
     * @param mixed $careerApply
     * @return array
     */
    public function map($careerApply): array
    {
        static $number = 0;
        $number++;

        // Get career information
        $career = $careerApply->career;
        $position = $career ? $career->position : '-';
        $location = $career ? $career->location : '-';

        // Format applicant name
        $applicantName = trim($careerApply->firstname . ' ' . $careerApply->lastname);

        // Format education
        $education = trim($careerApply->lasteducation . ' - ' . $careerApply->major);

        // Format experience
        $experienced = $careerApply->isexperience ? 'Yes' : 'No';

        // Format status
        $status = '';
        if ($careerApply->isapprove) {
            $status = 'Approved';
        } else {
            $status = $careerApply->rejectreason != "" ? 'Rejected' : 'New';
        }

        // Format experience list
        $experienceList = '';
        if ($careerApply->experiencelist) {
            $experiences = json_decode($careerApply->experiencelist, true);
            if (is_array($experiences)) {
                $expArray = [];
                foreach ($experiences as $exp) {
                    $company = $exp['companyname'] ?? '';
                    $industry = $exp['industri'] ?? '';
                    $position = $exp['position'] ?? '';
                    $duration = $exp['duration'] ?? '';
                    $expArray[] = "Company: {$company}, Industry: {$industry}, Position: {$position}, Duration: {$duration} years";
                }
                $experienceList = implode(' | ', $expArray);
            }
        }

        return [
            $number,
            $position,
            $location,
            $applicantName,
            $careerApply->email,
            $careerApply->phone,
            $careerApply->bod,
            $education,
            $experienced,
            $careerApply->currentsalary,
            $careerApply->expectationsalary,
            $status,
            $careerApply->rejectreason,
            $experienceList,
            $careerApply->created_at ? $careerApply->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
