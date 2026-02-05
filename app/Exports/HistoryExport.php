<?php

namespace App\Exports;

use App\Models\Survey;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HistoryExport implements FromQuery, WithColumnFormatting, WithMapping, WithHeadings, WithColumnWidths
{
    use Exportable;

    protected $dateStart;
    protected $dateEnd;
    protected $search;

    public function __construct($dateStart, $dateEnd, $search)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->search = $search;
    }

    public function query()
    {
        $data = Survey::with('user');
        if ($this->dateStart) {
            $data->whereDate('tanggal_kejadian', '>=', $this->dateStart);
        }

        if ($this->dateEnd) {
            $data->whereDate('tanggal_kejadian', '<=', $this->dateEnd);
        }

        if ($this->search) {
            $data->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return $data;
    }

    public function map($survey): array
    {
        return [
            $survey->user->name,
            new DateTime($survey->tanggal_kejadian),
            $survey->latitude . ', ' . $survey->longitude,
            $survey->tinggi,
            $survey->foto ? asset('storage/' . $survey->foto) : null,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Petugas',
            'Tanggal',
            'Koordinat Lokasi',
            'Tinggi Genangan (cm)',
            'Foto',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 20,
            'E' => 100,
        ];
    }
}
