<?php

namespace App\Exports;

use App\Report;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReportsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, ShouldQueue
{
    protected $date_from;
    protected $date_to;
    protected $mail_status;

    function __construct($date_from, $date_to, $mail_status) {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->mail_status = $mail_status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $reports = DB::table('reports')
                    ->select('reports.date',
                        'users.name as student_name',
                        'users.student_number as student_number',
                        DB::raw('(CASE
                        WHEN users.section = "male" THEN "بنين"
                        ELSE "بنات"
                        END) AS user_section'),
                        'classes.title as class_name',
                        'teachers.name as teacher_name',
                        'reports.new_lesson',
                        'reports.new_lesson_from',
                        'reports.new_lesson_to',
                        'reports.last_5_pages',
                        'reports.daily_revision',
                        'reports.daily_revision_from',
                        'reports.daily_revision_to',
                        'reports.mistake',
                        'reports.alert',
                        'reports.number_pages',
                        'reports.listener_name',
                        'reports.lesson_grade',
                        'reports.last_5_pages_grade',
                        'reports.daily_revision_grade',
                        'reports.behavior_grade',
                        'reports.total',
                        'reports.notes_to_parent',
                        DB::raw('IF(reports.mail_status = 1, "تم الارسال", "لم يتم")')
                    )
                    ->join('users', 'users.id', '=', 'reports.student_id')
                    ->join('classes', 'classes.class_number', '=', 'reports.class_number')
                    ->join('classes_teachers', 'reports.class_number', '=', 'classes_teachers.class_number')
                    ->join('teachers', 'teachers.email', '=', 'classes_teachers.teacher_email')
                    ->whereBetween('reports.created_at', [$this->date_from, $this->date_to])
                    ->where('reports.mail_status', '=', $this->mail_status);

        return $reports->get();
    }

    public function headings(): array
    {
        return [
            'اليوم والتاريخ',
            'اسم الطالب',
            'رقم الطالب',
            'القسم',
            'عنوان الحلقة',
            'اسم المعلم',
            'الدرس الجديد',
            'من',
            'إلى',
            'أخر 5 صفحات',
            'المراجعة اليومية',
            'من',
            'إلى',
            'الاخطاء',
            'تنبيه',
            'عدد الصفحات',
            'اسم المستمع',
            'درجة الدرس',
            'درجة أخر 5 صفحات',
            'درجة المراجعة اليومية',
            'درجة السلوك',
            'المجموع',
            'ملاحظات إلى ولي الأمر',
            'حالة ارسال البريد',
        ];
    }

    public function styles(Worksheet $sheet)
    {

//        foreach(range('A1','W1') as $columnID) {
//            $sheet->getColumnDimension($columnID)
//                ->setAutoSize(true);
//        }

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }

    /**
     * @return array
     */
//    public function registerEvents(): array
//    {
//        return [
//            AfterSheet::class    => function(AfterSheet $event) {
//                $cellRange = 'A1:W1'; // All headers
////                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setAutoSize(true)->setSize(11);
//                $event->sheet->getColumnDimension($cellRange)->setAutoSize(false) ;
//
//            },
//        ];
//    }

}
