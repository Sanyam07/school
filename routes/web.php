<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// the default laravel route for register has been modified 

Route::get('/home', 'HomeController@index')->name('home');


//register admin
Route::middleware(['master'])->group(function(){
    Route::prefix('register')->group(function(){
        Route::get('admin' , function(){
            session(['register_role' => 'admin']);
            return redirect()->route('register');
        });
        Route::post('admin' , 'UserController@storeAdmin');
    });
});

//register admin and other staff
Route::middleware(['auth' , 'admin.master'])->group(function(){

    Route::get('/register' , function(){

        if( session()->has('register_role') && session('register_role') == 'student' ){
            $sections = \App\Section::has('schoolClass')->with('schoolClass')->get();
            $schoolSessions = \App\SchoolSession::all();
            return view('auth.register')->with(['sections' => $sections , 'schoolSessions' => $schoolSessions]);
        }

        return view('auth.register');

    })->name('register');

     //group all the different route to register users based on their roles
    Route::prefix('register')->group(function(){

        Route::get('student' , function(){
            session(['register_role' => 'student']);
            return redirect()->route('register');
        });

        Route::get('teacher' , function(){
            session(['register_role' => 'teacher']);
            return redirect()->route('register');
        });

        Route::post('student' , 'UserController@storeStudent');
        Route::post('teacher' , 'UserController@storeTeacher');

    });

});

//editing users 
Route::prefix('users')->group(function(){

    Route::middleware(['auth' , 'admin.master'])->group(function(){
        Route::get('view/{role}/status/{active}/{searchInput?}' , 'UserController@index')
        ->name('view.users')->where('role' , '^(?!.*master).*$');
        Route::get('edit/{id}' , 'UserController@edit')->name('user.edit');
        Route::post('update/student' , 'UserController@updateStudent')->name('student.update');
        Route::post('update/staff' , 'UserController@updateStaff')->name('staff.update');
    });

    Route::middleware(['auth' , 'master'])->group(function(){
        Route::post('update/admin' , 'UserController@updateAdmin')->name('admin.update');
    });
    
});

Route::middleware(['auth' , 'admin'])->group(function(){
    Route::get('settings' , function(){
        return view('settings.index');
    })->name('settings');
});


Route::middleware(['auth' , 'admin'])->group(function(){

    Route::prefix('settings')->group(function(){

        Route::get('addclass', 'SchoolClassController@create');
        Route::post('addclass' , 'SchoolClassController@store');
        Route::get('viewclasses' , 'SchoolClassController@index');
        Route::get('showclass/{id}' , 'SchoolClassController@show'); // takes you to where you can view and add section of a class room

        Route::post('addsection' , 'SectionController@store');

        Route::get('addsession' , 'SchoolSessionController@create');
        Route::post('addsession' , 'SchoolSessionController@store');
        Route::get('viewsessions' , 'SchoolSessionController@index');
        Route::get('editsession/{id}' , 'SchoolSessionController@edit');
        Route::post('updatesession' , 'SchoolSessionController@update');
        Route::get('showsession/{id}' , 'SchoolSessionController@show'); //takes you to where you can add semester


        Route::post('addsemester' , 'SemesterController@store');

        /**
         * add courses
         */
        Route::get('section/addcourse' , 'CourseController@indexToSemester')->name('section.addcourse');
        Route::get('addcourse/section/{section_id}/semester/{semester_id}' , 'CourseController@addCourse')->name('addcourse');
        Route::post('addcourse' , 'CourseController@store');

    });

});


//courses for teachers
Route::middleware(['auth' , 'teacher'])->group(function(){
    Route::get('semester/teacher/viewcourse' , 'CourseController@indexTeacher')->name('teacher.viewcourses');
    Route::get('courses/semester/{semester_id}' , 'CourseController@teacherCourses')->name('teacher.courses');
});

//courses for student
Route::middleware(['auth' , 'student'])->group(function(){
    Route::get('courses/student' , 'CourseController@studentCourses')->name('student.courses');
});

/**
 * Attendance
 */
Route::prefix('attendance')->group(function(){
    //attendance for students courses
    Route::middleware(['auth' , 'teacher'])->group(function(){
        //attendance for courses
        Route::get('student/course/{course_id}/section/{section_id}/semester/{semester_id}' , 'AttendanceController@createStudent')->name('create.student.attendance');
        Route::post('student' , 'AttendanceController@storeCoursesAttendance')->name('courses.attendance');
    });

    //general attendance
    Route::middleware(['auth' , 'admin'])->group(function(){
        //attendace for staff
        Route::get('staff' , 'AttendanceController@createStaff')->name('create.staff.attendance');
        Route::post('staff' , 'AttendanceController@storeStaffAttendance')->name('staff.attendance');

        //general attendace for student (NOT attendance for courses)
        Route::get('selectsection' , 'AttendanceController@selectSection')->name('selectsection.attendance');
        Route::get('daily/student/section/{section_id}' , 'AttendanceController@createDailyStudent')->name('create.daily.attendance');
        Route::post('daily/student' , 'AttendanceController@storeDailyAttendance')->name('daily.attendance');
    });

});