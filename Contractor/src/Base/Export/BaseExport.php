<?php

namespace NawaGrow\Contractor\Base\Export;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BaseExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    public $collection;

    public $headings;

    public $title;

    public function __construct($collection, $headings, $title)
    {
        $this->collection = $collection;
        $this->headings = $headings;
        $this->title = $title;
    }

    public function collection()
    {
        $data = [];
        for ($i = 0; $i < count($this->collection); $i++) {
            foreach ($this->headings as $heading)
                $data[$i][$heading] = $this->collection[$i][$heading] ?? null;
//            $data[$i] = collect($this->collection[$i])->only($this->headings);
        }

        return collect($data);
    }


    public function headings(): array
    {
        return collect($this->headings)->map(function ($value) {
            return trans("admin::lang.$value");
        })->toArray();
    }

    public function title(): string
    {
        return trans("lang.$this->title");
    }

    public function registerEvents(): array
    {
        $char = range('A', 'Z');
        $count = collect($this->headings)->count();
        $charSyntax =$count <= 25 ?  $char[$count - 1] : $char[0];
        $charIndex = $count >= 24 ? $charSyntax . $char[$count - 27] : $charSyntax;
        $collectionCount = collect($this->collection)->count();
        return [AfterSheet::class => function (AfterSheet $event) use ($char, $count, $collectionCount, $charIndex) {
            $event->sheet->autoSize();
            $event->sheet->getDelegate()->getStyle("A1:" . $charIndex . '1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('7A7A7A');
//
            $event->sheet->getDelegate()->getStyle("A1:" . $charIndex . ($collectionCount + 1))->getFont()->setSize(8)->setName('cairo');

            $event->sheet->getDelegate()->getStyle("A1:" . $charIndex . '1')
                ->getFont()->setBold(true)
                ->getColor()
                ->setARGB('ffffff');
        },];
    }

}
