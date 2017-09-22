<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Guardian;
use App\Term;
use App\Semester;


/**
 * This controller handles redirecting logged in guardians
 * to the home or guardian dashboard page.
 * It also posses functions that allows a guardian to view scores
 * of student(s) assigned to them.
 */

class GuardianController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:guardian');
        $this->middleware('preventBackHistory');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $guardians = Guardian::with('student')->where('id', Auth::guard('guardian')->user()->id)->get();

        return view('guardian.home', compact('guardians'));
    }

    /**
     * Show the form to search for a specific.
     * student term(periodic) report
     *
     * @return \Illuminate\Http\Response
     */
    public function termForm(Request $request)
    {
        //
        $terms = Term::all();
        $guardians = Guardian::with('student')->where('id', Auth::guard('guardian')->user()->id)->get();
        return view('guardian.student-term', compact('terms', 'guardians'));
    }
    /**
     * Show the form to search for a specific.
     * student term(periodic) report
     *
     * @return \Illuminate\Http\Response
     */
    public function termResults(Request $request, Score $score)
    {
        //
        return $score->termReport($request->term_id, $request->student_id);
    }
    /**
     * Show the form to search for a specific.
     * student term(periodic) report
     *
     * @return \Illuminate\Http\Response
     */
    public function semesterForm(Request $request)
    {
        //
        $semesters = Semester::all();
        $guardians = Guardian::with('student')->where('id', Auth::guard('guardian')->user()->id)->get();
        return view('guardian.student-semester', compact('semesters', 'guardians'));
    }
    /**
     * display a student semester report
     *
     * @return \Illuminate\Http\Response
     */
    public function semesterResults(Request $request, Score $score)
    {
        return $score->semesterReport($request->student_id, $request->semester_id);
    }

}