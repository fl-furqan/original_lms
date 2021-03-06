<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'dashboard-admins', 'namespace' => '\App\Http\Controllers\Auth', 'as' => 'admins.'], function (){
    Route::get('/login', 'AdminLoginController@showLoginForm')->name('login.form');
    Route::post('/login', 'AdminLoginController@login')->name('login.post');
});

Route::group(['prefix' => 'dashboard-admins', 'as' => 'admins.', 'middleware' => ['auth:admin_web']], function (){

    // logout
    Route::post('/logout', '\App\Http\Controllers\Auth\AdminLoginController@logout')->name('logout');

    // home
    Route::get('/', 'HomeController@index')->name('home');

    // admins
    Route::get('/admins', 'AdminController@index')->name('admins.index');
    Route::get('/admins/{admin}/edit', 'AdminController@edit')->name('admins.edit');
    Route::put('/admins/{admin}/update', 'AdminController@update')->name('admins.update');
    Route::delete('/admins/destroy/all', 'AdminController@deleteAll')->name('admins.deleteAll');
    Route::delete('/admins/destroy/{id}', 'AdminController@destroy')->name('admins.destroy');
    Route::get('/admins/create', 'AdminController@create')->name('admins.create');
    Route::post('/admins/store', 'AdminController@store')->name('admins.store');

    // import students
    Route::get('importStudents', 'ImportExportController@importStudentsView')->name('import.students.view');
    Route::post('importStudents', 'ImportExportController@importStudents')->name('import.students.store');

    // import lessons and chapters
    Route::get('importLessons', 'ImportExportController@importLessons')->name('import.lessons');
    Route::get('importChapters', 'ImportExportController@importChapters')->name('import.chapters');

    // import classes
//    Route::get('importClasses', 'ImportExportController@importClassesView')->name('import.classes.view');
//    Route::post('importClasses', 'ImportExportController@importClasses')->name('import.classes.store');

    // import teachers
//    Route::get('importTeachers', 'ImportExportController@importTeachersView')->name('import.teachers.view');
//    Route::post('importTeachers', 'ImportExportController@importTeachers')->name('import.teachers.store');

    // import classes_teachers
//    Route::get('importClassesTeachers', 'ImportExportController@importClassesTeachersView')->name('import.classes_teachers.view');
//    Route::post('importClassesTeachers', 'ImportExportController@importClassesTeachers')->name('import.classes_teachers.store');
//    Route::get('export', 'ImportExportController@export')->name('export');

    // classes
    Route::get('classes', 'ClassesController@index')->name('classes.index');
    Route::get('/class_students/{class_number}', 'ClassesController@classStudents')->name('classes.students');

    // students
    Route::get('students', 'UserController@index')->name('student.index');

    // reports
    Route::get('reports', 'ReportController@index')->name('report.index');
    Route::post('reports', 'ReportController@export')->name('report.export');

    // disable teachers login
    Route::get('disable_teachers_login', 'AdminController@disableTeachersLogin')->name('disable.teachers.login');

    Route::get('absences', 'AbsenceController@index')->name('absences.index');
    Route::get('absence_type', 'AbsenceController@absenceType')->name('absence.type');

    Route::get('/report/table/{student_id}', 'ReportController@reportTable')->name('report.table');
    Route::post('/report/table/{student_id}', 'ReportController@reportTableStore')->name('report.table');
    Route::post('/report/send/{student_id}', 'ReportController@sendReportTable')->name('send.report');
    
});

    Route::get('optimize', function(){
        \Artisan::call('optimize:clear');
        \Artisan::call('key:generate');
        \Artisan::call('cache:clear');
        echo "Done";
    });
