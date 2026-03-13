<?php

namespace App\Exports;

use App\Models\CareerApply;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CareerApplyExport implements FromArray, WithStyles, ShouldAutoSize
{
    private const TOTAL_COLUMNS = 14;

    protected $careerId;
    protected array $sectionTitleRows = [];
    protected array $sectionHeaderRows = [];

    public function __construct($careerId = null)
    {
        $this->careerId = $careerId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
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
    public function array(): array
    {
        $this->sectionTitleRows = [];
        $this->sectionHeaderRows = [];

        $rows = [[
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
            'Applied Date',
        ]];

        $number = 0;
        foreach ($this->collection() as $careerApply) {
            $number++;
            $career = $careerApply->career;
            $careerPosition = $career ? $career->position : '-';
            $location = $career ? $career->location : '-';
            $applicantName = trim($careerApply->firstname . ' ' . $careerApply->lastname);
            $education = trim($careerApply->lasteducation . ' - ' . $careerApply->major);
            $experienced = $careerApply->isexperience ? 'Yes' : 'No';

            $status = '';
            if ($careerApply->isapprove) {
                $status = 'Approved';
            } else {
                $status = $careerApply->rejectreason != '' ? 'Rejected' : 'New';
            }

            $rows[] = [
                $number,
                $careerPosition,
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
                $careerApply->created_at ? $careerApply->created_at->format('Y-m-d H:i:s') : '',
            ];

            $experiences = $this->parseExperienceList($careerApply->experiencelist);

            $rows[] = $this->blankRow();
            $this->sectionTitleRows[] = count($rows) + 1;
            $rows[] = $this->padRow(['', 'Experience List']);

            $this->sectionHeaderRows[] = count($rows) + 1;
            $rows[] = $this->padRow(['', 'Company Name', 'Industry', 'Position', 'Duration (Years)']);

            if (count($experiences) === 0) {
                $rows[] = $this->padRow(['', '-', '-', '-', '-']);
            } else {
                foreach ($experiences as $experience) {
                    $rows[] = $this->padRow([
                        '',
                        $experience['companyName'],
                        $experience['industri'],
                        $experience['position'],
                        $experience['lengthOfWork'],
                    ]);
                }
            }

            $rows[] = $this->blankRow();
        }

        return $rows;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $styles = [1 => ['font' => ['bold' => true]]];

        foreach ($this->sectionTitleRows as $row) {
            $styles[$row] = ['font' => ['bold' => true]];
        }

        foreach ($this->sectionHeaderRows as $row) {
            $styles[$row] = ['font' => ['bold' => true]];
        }

        return $styles;
    }

    private function parseExperienceList(mixed $experienceList): array
    {
        if (empty($experienceList)) {
            return [];
        }

        $experiences = is_string($experienceList)
            ? json_decode($experienceList, true)
            : $experienceList;

        if (! is_array($experiences)) {
            return [];
        }

        $normalized = [];
        foreach ($experiences as $experience) {
            if (! is_array($experience)) {
                continue;
            }

            $companyName = trim((string) ($experience['companyName'] ?? $experience['companyname'] ?? ''));
            $industry = trim((string) ($experience['industri'] ?? $experience['industry'] ?? ''));
            $position = trim((string) ($experience['position'] ?? ''));
            $lengthOfWork = trim((string) ($experience['lengthOfWork'] ?? $experience['duration'] ?? ''));

            if ($companyName === '' && $industry === '' && $position === '' && $lengthOfWork === '') {
                continue;
            }

            $normalized[] = [
                'companyName' => $companyName,
                'industri' => $industry,
                'position' => $position,
                'lengthOfWork' => $lengthOfWork,
            ];
        }

        return $normalized;
    }

    private function blankRow(): array
    {
        return array_fill(0, self::TOTAL_COLUMNS, '');
    }

    private function padRow(array $row): array
    {
        return array_pad($row, self::TOTAL_COLUMNS, '');
    }
}
