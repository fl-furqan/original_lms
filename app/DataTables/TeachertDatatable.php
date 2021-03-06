<?php

namespace App\DataTables;

use App\Teacher;
use App\Classes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\ButtonsServiceProvider;

class TeachertDatatable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        $classes = Classes::join('classes_teachers', 'classes.class_number', '=', 'classes_teachers.class_number')
            ->join('teachers', 'teachers.email', '=', 'classes_teachers.teacher_email')
            ->where('teachers.email', '=', auth('teacher_web')->user()->email)
            ->where('teachers.section', '=', auth('teacher_web')->user()->section)
            ->select([
                'classes.class_number',
                'classes.title',
                'classes.zoom_link',
                'classes.path',
                'classes.period',
                DB::raw('(CASE WHEN classes_teachers.role = "main" THEN "معلم" ELSE "مراقب" END) as role'),
            ])
            ->get();

        return datatables($classes)
            ->addColumn('zoom_link', 'teachers.classes.btn.zoom_link')
            ->addColumn('period', 'teachers.classes.btn.period')
            ->addColumn('title', 'teachers.classes.btn.title')
            ->addColumn('students_count', 'teachers.classes.btn.students_count')
            ->rawColumns([
                'zoom_link',
                'period',
                'title',
                'students_count',
            ]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\AdDatatable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {

//        $classes = Classes::join('classes_teachers', 'classes.class_number', '=', 'classes_teachers.class_number')
//            ->join('teachers', 'teachers.teacher_number', '=', 'classes_teachers.teacher_number')
//            ->where('teachers.teacher_number', '=', auth()->user()->teacher_number)
//            ->select(['classes_teachers.teacher_number', 'classes.title']);


//        $categories = Ad::select('id', 'is_featured', 'title', 'description', 'phone', 'price', 'currency_id', 'is_active', 'created_at')->orderBy('id', 'desc');
        return '';
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('adsdatatable-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
//                    ->parameters([$this->getBuilderParameters()]);
                    ->parameters([
                        'dom' => 'Bfrtip',
                        'lengthMenu' => [
                            [ 10, 25, 50, -1 ],
                            [ '10', '25', '50', 'Show all' ]
                        ],
                        'buttons' => [
                            ['extend' => 'pageLength', 'className' => 'dataTables_length'],
                            ['extend' => 'csv', 'title' => $this->filename(), 'className' => 'btn btn-primary', 'text' => '<i class="la la-file"></i> CSV File'],
                            ['extend' => 'excel', 'title' => $this->filename(), 'className' => 'btn btn-info', 'text' => '<i class="la la-print"></i> Export Excel'],
                        ],
                        'language' => [
                            'url' => 'https://cdn.datatables.net/plug-ins/1.10.12/i18n/Arabic.json'
                        ]
                    ])
                    ->buttons([
//                        Button::make('csv'),
                    ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('title')
                ->data('title')
                ->title('اسم الحلقة'),
            Column::make('role')
                ->data('role')
                ->title('الدور')
                ->searchable(false),
            Column::make('zoom_link')
                ->data('zoom_link')
                ->title('رابط الحلقة'),
            Column::make('students_count')
                ->data('students_count')
                ->title('عدد الطلاب'),
            Column::make('path')
                ->data('path')
                ->title('المسار'),
            Column::make('period')
                ->data('period')
                ->title('الفترة'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Ads_' . date('YmdHis');
    }
}
