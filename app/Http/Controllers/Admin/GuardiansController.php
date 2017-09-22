<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Guardian;
use App\User;
use App\Term;
use App\Score;
use App\Student;
use App\Semester;
use App\Common;


class GuardiansController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin,web');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $guardians = Guardian::all();

        return view('admin-guardians.home', compact('guardians'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $relationships = Guardian::relationships();

        return view('admin-guardians.create', compact('relationships'));
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(),[
            'first_name' => 'required|string|max:200|regex:/^[a-z ,.\'-]+$/i',
            'surname' => 'required|string|max:200|regex:/^[a-z ,.\'-]+$/i',
            'gender' => 'required|string',
            'relationship' => 'required|string',
            'address' => 'required|string|max:255|regex:/^[a-z ,.\'-]+$/i',
            'phone' => 'required|unique:guardians',
            'user_name' => 'required|string|unique:guardians|max:20',
            'email' => 'sometimes|email|unique:guardians|nullable',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $guardian = Guardian::create([
            'first_name' => request('first_name'),
            'surname' => request('surname'),
            'gender' => request('gender'),
            'relationship' => request('relationship'),
            'address' => request('address'),
            'phone' => request('phone'),
            'user_name' => request('user_name'),
            'email' => request('email'),
            'password' => bcrypt(request('password'))
        ]);

        // notify guardian was created
        session()->flash('message', $guardian->first_name." ".$guardian->surname);
        return back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $guardian = Guardian::findOrfail($id);

        //pass guardian with students assigned
        $guardians = Guardian::with('student')->where('id', $guardian->id)->get();
        $genders = Common::genders();
        $relationships = Guardian::relationships();

        return view('admin-guardians.edit', compact('guardian', 'guardians', 'genders', 'relationships'));

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate(request(), [
            'first_name' => 'required|string|max:200|regex:/^[a-z ,.\'-]+$/i',
            'surname' => 'required|string|max:200|regex:/^[a-z ,.\'-]+$/i',
            'gender' => 'required|string',
            'relationship' => 'required|string',
            'address' => 'required|string|max:255|regex:/^[a-z ,.\'-]+$/i',
            'phone' => 'required|unique:guardians,phone,'.$id,
            'user_name' => 'required|string|max:30|unique:guardians,user_name,'.$id,
            'email' => 'sometimes|nullable|email|unique:guardians,email,'.$id
        ]);

        $guardian = Guardian::findOrfail($id);

        // update guardian/user
        $guardian->first_name = $request->first_name;
        $guardian->surname = $request->surname;
        $guardian->gender = $request->gender;
        $guardian->relationship = $request->relationship;
        $guardian->address = $request->address;
        $guardian->phone = $request->phone;
        $guardian->user_name = $request->user_name;
        $guardian->email = $request->email;

        // if password is being updated
        if ($request->has('password')) {

            $this->validate(request(),[
                'password' => 'required|string|min:6|confirmed'
            ]);

            $guardian->password = bcrypt($request->password);  
        }

        $guardian->update();

        // notify guardian has been updated
        session()->flash('message', $guardian->first_name." ".$guardian->surname);

        return redirect()->route('guardians.home');
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find user to be deleted
        $guardian = Guardian::findOrFail($id);
        //delete user from user table
        $guardian->delete();

        return response()->json ( array (
            'message' => "Guardian deleted!"
        ) );
        
    }
}
