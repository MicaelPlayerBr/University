<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Course;
use App\Student;
use App\Enrollment;
use App\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function admin()
    {
        return view('/admin.index');
    }
    public function courseIndex()
    {

        $course = Course::paginate(5);

        return view('admin/courses.index', ['courses' => $course]);
    }

    public function studentIndex()
    {

        $student = Student::paginate(5);

        return view('admin/student.index', ['student' => $student]);
    }

    public function courseStore(Request $request) 
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'menu' => 'required|string|max:255',
            'student_amount' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            \Session::flash('status', 'There Was an Error');
            return view('admin/courses/new');
        }

        $p = new course;
        $p->name = $request->input('name');
        $p->menu = $request->input('menu');
        $p->students_amount = $request->input('student_amount');
        
        if ($p->save()) {
            \Session::flash('status', 'Course Registred With Sucess');
            return redirect('/admin/courses');
        } else {
            \Session::flash('status', 'There Was an Error');
            return view('admin/courses/new');
        }
    }

    public function courseEdit($id) 
    {
        $courses = Course::findOrFail($id);

        return view('admin/courses.edit', ['course' => $courses]);
    }

    public function courseUpdate(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'menu' => 'required|string|max:255',
            'student_amount' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            \Session::flash('status', 'There Was an Error');
            return view('admin/courses/edit');
        }

        $id = $request->input('id');
        $p = Course::findOrFail($id);
        $p->name = $request->input('name');
        $p->menu = $request->input('menu');
        $p->students_amount = $request->input('student_amount');
        
        if ($p->save()) {
            \Session::flash('status', 'Course Update With Success');
            return redirect('/admin/courses');
        } else {
            \Session::flash('status', 'There was an Error');
            return view('admin/courses/edit', ['courses' => $p]);
        }
    }

    public function courseDelete($id) 
    {
        $p = Course::findOrFail($id);
        $p->delete();

        \Session::flash('status', 'Course Deleted With Success');
        return redirect('/admin/courses');
    }

    public function enrollmentIndex()
    {

        // $enrollment = DB::table('enrollments')
        //     ->join('students', 'enrollments.id_student', '=', 'students.id')
        //     ->join('courses', 'enrollments.id_course', '=', 'courses.id')
        //     ->select('courses.name as course_name', 'students.name as student_name', 'enrollments.*')->paginate(5);

        $enrollment = Enrollment::with(['student', 'course'])->paginate(5);


        return view('admin/enrollments.index', ['enrollment' => $enrollment]);
    }

    public function enrollmentNew()
    {

        $courses = Course::all();
        $students = Student::all();

        return view('admin/enrollments.new', ['courses' => $courses], ['students' => $students]);
    }

    public function enrollmentStore(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'id_student' => 'required|integer',
            'id_course' => 'required|integer',
        ]);

        if ($validator->fails()) {
            \Session::flash('status', 'There Was an Error');
            return view('admin/courses/edit');
        }

        $p = new Enrollment;
        $p->id_student = $request->input('id_student');
        $p->authorization = 1;
        $p->id_course = $request->input('id_course');
        
        if ($p->save()) {
            \Session::flash('status', 'Enrollment Registred With Sucess');
            return redirect('/admin/enrollment');
        } else {
            \Session::flash('status', 'There Was an Error');
            return view('admin/enrollments/new');
        }
    }


    public function enrollmentAuthorize($id)
    {
        $p = Enrollment::findOrFail($id);
        $p->authorization = 1;
            
        if ($p->save()) {
            \Session::flash('status', 'Enrollment Authorizade With Success');
            return redirect('/admin/enrollment');
        } else {
            \Session::flash('status', 'There was an Error');
                return view('admin/courses/edit', ['enrollments' => $p]);
        }
 
    }

    public function enrollmentEdit($id) 
    {
        // $row = DB::table('enrollments')
        //     ->join('students', 'enrollments.id_student', '=', 'students.id')
        //     ->join('courses', 'enrollments.id_course', '=', 'courses.id')
        //     ->select('courses.name as course_name', 'students.name as student_name', 'enrollments.*')
        //     ->where('enrollments.id', '=', $id)->get();

        $row = Enrollment::with(['student', 'course'])->where('id', $id)->get();

        $courses = Course::all();
        $students = Student::all();

        


        return view('admin/enrollments.edit', ['row' => $row, 'courses' => $courses, 'students' => $students]);
    }

    public function enrollmentUpdate(Request $request) 
    {

        $validator = Validator::make($request->all(), [
            'id_student' => 'required|integer',
            'id_course' => 'required|integer',
        ]);

        if ($validator->fails()) {
            \Session::flash('status', 'There Was an Error');
            return view('admin/courses/edit');
        }

        $id = $request->input('id');
        $p = Enrollment::findOrFail($id);
        $p->id_student = $request->input('id_student');
        $p->id_course = $request->input('id_course');
        $p->authorization = 1;
        
        if ($p->save()) {
            \Session::flash('status', 'Enrollment Update With Success');
            return redirect('/admin/enrollment');
        } else {
            \Session::flash('status', 'There was an Error');
            return view('admin/enrollments/edit', ['enrollments' => $p]);
        }
    }

    public function enrollmentDelete($id) 
    {
        $p = Enrollment::findOrFail($id);
        $p->delete();

        \Session::flash('status', 'Enrollment Deleted With Success');
        return redirect('/admin/enrollment');
    }

    public function userIndex()
    {

        $user = User::paginate(5);


        return view('admin/user', ['user' => $user]);
    }

    public function userAuthorize($id)
    {
        $p = User::findOrFail($id);
        $p->admin = 1;
            
        if ($p->save()) {
            \Session::flash('status', 'User Authorized With Success');
            return redirect('/admin/user');
        } else {
            \Session::flash('status', 'There was an Error');
                return view('/admin/user');
        }
 
    }

    public function userDelete($id) 
    {
        $p = User::findOrFail($id);

        if($p->admin){
            \Session::flash('denied', 'Operation Denied');
            return redirect('/admin/user');
        }else{
            $p->delete();

            \Session::flash('status', 'User Deleted With Success');
            return redirect('/admin/user');
        }
        
    }




}
