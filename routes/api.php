<?php

use App\Employee;
use Carbon\Carbon;
use App\IDImageRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/IDLogin', function(Request $request) {
    $username = $request->username;
    $password = $request->password;
    if($username == null || $password == null){
        echo 'required';
    }else{
        if($username == 'alwin' && $password == '1234'){
            echo 'success';
        }else{
            echo 'failed';
        }
    }
});

Route::get('/getAllEmployees', function () {

    $array['employees'] = array();

    $emps = Employee::orderBy('LastName', 'ASC')
                ->Active()
                ->get(['FirstName', 'MiddleName', 'LastName', 'Suffix', 'Employee_id', 'PosCode', 'OfficeCode2',
                'isActive', 'Address', 'blood_type', 'gsis_umid_no', 'gsis_bp_no', 'pagibig_no', 'philhealth_no',
                'sss_no', 'tin_no', 'gsis_no', 'Birthdate', 'name_emergency', 'address_emergency', 'contact_emergency']);

    foreach($emps as $emp){
        $employees = array();
        $employees['employeeId']       = $emp->Employee_id;
        $employees['fname']            = $emp->FirstName;
        $employees['mname']            = $emp->MiddleName;
        $employees['lname']            = $emp->LastName;
        $employees['fullname']         = $emp->fullname;
        $employees['position']         = $emp->position->Description;
        $employees['office']           = $emp->office_assignment->Description;
        $employees['gsispo']           = $emp->gsis_no;
        $employees['gsisno']           = $emp->gsis_umid_no;
        $employees['pagibig']          = $emp->pagibig_no;
        $employees['tin']              = $emp->tin_no;
        $employees['philhealth']       = $emp->philhealth_no;
        $employees['bloodtype']        = $emp->blood_type;
        $employees['dob']              = $emp->Birthdate;
        $employees['address']          = $emp->Address;
        $employees['emername']         = $emp->name_emergency;
        $employees['emeraddress']      = $emp->address_emergency;
        $employees['emercontact']      = $emp->contact_emergency;
        $employees['signature']        = 'Signature.jpg';
        $employees['employee_image']   = 'IdPicture.jpg';

        array_push($array['employees'], $employees);
    }

    return response()->json($array);
});

Route::get('/getAllEmployees/{keyword}', function ($keyword) {
    $array['employees'] = array();

        $emps = Employee::orderBy('LastName', 'ASC')
                    ->where('FirstName', 'like', '%' . $keyword . '%')
                    ->orWhere('LastName', 'like', '%' . $keyword . '%')
                    ->Active()
                    ->get(['FirstName', 'MiddleName', 'LastName', 'Suffix', 'Employee_id', 'PosCode', 'OfficeCode2', 'OfficeCode',
                    'isActive', 'Address', 'blood_type', 'gsis_umid_no', 'gsis_bp_no', 'pagibig_no', 'philhealth_no',
                    'sss_no', 'tin_no', 'gsis_no', 'Birthdate', 'name_emergency', 'address_emergency', 'contact_emergency']);

    foreach($emps as $emp){
        $employees = array();
        $employees['employeeId']       = $emp->Employee_id;
        $employees['fname']            = $emp->FirstName;
        $employees['mname']            = $emp->MiddleName;
        $employees['lname']            = $emp->LastName;
        $employees['fullname']         = $emp->fullname;
        $employees['position']         = $emp->position->Description;
        $employees['office']           = $emp->office_assignment->Description;
        $employees['address']          = $emp->Address;
        $employees['gsispo']           = $emp->gsis_no;
        $employees['gsisno']           = $emp->gsis_umid_no;
        $employees['pagibig']          = $emp->pagibig_no;
        $employees['tin']              = $emp->tin_no;
        $employees['philhealth']       = $emp->philhealth_no;
        $employees['bloodtype']        = $emp->blood_type;
        $employees['dob']              = Carbon::parse($emp->Birthdate)->format('m-d-Y');
        $employees['emername']         = $emp->name_emergency;
        $employees['emeraddress']      = $emp->address_emergency;
        $employees['emercontact']      = $emp->contact_emergency;
        $employees['signature']        = 'Signature.jpg';
        $employees['employee_image']   = 'IdPicture.jpg';

        array_push($array['employees'], $employees);
    }

    return response()->json($array);
});


Route::get('/getIdPicture/{id}', function ($id) {
    $employees = Employee::where('Employee_id', $id)->first([
        'Employee_id',
        'ImagePhoto',
    ]);

    file_put_contents(public_path('assets\\GeneratedImages\\IdPicture.jpg'), $employees->ImagePhoto);
    return response()->file(public_path('assets\\GeneratedImages\\IdPicture.jpg'), ['Content-Type' => 'image/jpeg']);
});

Route::get('/getSignature/{id}', function ($id) {
    $employees = Employee::where('Employee_id', $id)->first([
        'Employee_id',
        'signaturephoto'
    ]);

    file_put_contents(public_path('assets\\GeneratedImages\\Signature.jpg'), $employees->signaturephoto);
    return response()->file(public_path('assets\\GeneratedImages\\Signature.jpg'), ['Content-Type' => 'image/jpeg']);
});

Route::post('/changeImageRequests', function(Request $request){
    $id = $request->id;

    $idPhoto= $request->file('idphoto');
    $idfilename= "ID-".$id.".jpg";
    $idPhoto->move(public_path('assets/ImageRequests'), $idfilename);

    $sigPhoto= $request->file('signaturephoto');
    $sigfilename= "SIG-".$id.".jpg";
    $sigPhoto->move(public_path('assets/ImageRequests'), $sigfilename);


    IDImageRequests::updateOrCreate(
        [
            'Employee_id' => $id,
        ],
        [
            'Employee_id'       => $id,
            'IdPhoto'           => $idfilename,
            'SignaturePhoto'    => $sigfilename,
        ]);
});

Route::post('/changeIDImageRequests', function(Request $request){
    $id = $request->id;

    $idPhoto= $request->file('idphoto');
    $idfilename= "ID-".$id.".jpg";
    $idPhoto->move(public_path('assets/ImageRequests'), $idfilename);

    IDImageRequests::updateOrCreate(
        [
            'Employee_id' => $id,
        ],
        [
            'Employee_id'       => $id,
            'IdPhoto'           => $idfilename,
            'SignaturePhoto'    => 'null',
        ]);
});

Route::post('/changeSignatureImageRequests', function(Request $request){
    $id = $request->id;

    $sigPhoto= $request->file('signaturephoto');
    $sigFilename= "SIG-".$id.".jpg";
    $sigPhoto->move(public_path('assets/ImageRequests'), $sigFilename);

    IDImageRequests::updateOrCreate(
        [
            'Employee_id' => $id,
        ],
        [
            'Employee_id'       => $id,
            'IdPhoto'           => 'null',
            'SignaturePhoto'    => $sigFilename,
        ]);
});
