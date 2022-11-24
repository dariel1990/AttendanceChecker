<?php

namespace App\Http\Controllers;

use App\Employee;
use Carbon\Carbon;
use App\IDImageRequests;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{
    public function index()
    {
        $employees = Employee::where('isReg', NULL)->where('isActive', 1)->orderBy('LastName', 'ASC')->get(['Employee_id', 'FirstName', 'MiddleName', 'LastName', 'PosCode', 'OfficeCode2']);
		$pageTitle = 'Attendee Registration';

        return view('dashboard', [
            'employees' => $employees,
			'pageTitle' => $pageTitle,
        ]);
    }

    public function attendanceList()
    {
        $employees = Employee::where('isReg', 1)->where('isActive', 1)->orderBy('LastName', 'ASC')->get(['Employee_id', 'FirstName', 'MiddleName', 'LastName', 'PosCode', 'OfficeCode2']);
        $pageTitle = 'Attendance List';

        return view('attendance', [
            'employees' => $employees,
			'pageTitle' => $pageTitle,
        ]);
    }

    public function generateIdPicture($id)
    {
        $employees = Employee::where('Employee_id', $id)->first([
            'Employee_id',
            'ImagePhoto',
        ]);

        file_put_contents(public_path('assets\\GeneratedImages\\'.$employees->Employee_id.'.jpg'), $employees->ImagePhoto);
    }

    public function production($id, $IDNoFontSize, $nameFontSize, $positionFontSize, $addressFontSize, $officeFontSize, $pictureSize, $position, $office)
    {
        $employeeID = '';
        $employees = Employee::where('Employee_id', $id)->first([
                                'Employee_id',
                                'FirstName',
                                'MiddleName',
                                'LastName',
                                'Suffix',
                                'Work_Status',
                                'PosCode',
                                'OfficeCode2',
                                'Address',
                                'gsis_no',
                                'gsis_umid_no',
                                'pagibig_no',
                                'tin_no',
                                'philhealth_no',
                                'blood_type',
                                'Birthdate',
                                'name_emergency',
                                'address_emergency',
                                'contact_emergency',
                                'ImagePhoto',
                                'signaturephoto'
                            ]);

        //Generate Id Picture and Signature Images
        file_put_contents(public_path('assets\\GeneratedImages\\IdPicture.jpg'), $employees->ImagePhoto);
        file_put_contents(public_path('assets\\GeneratedImages\\Signature.jpg'), $employees->signaturephoto);

        if($employees->Work_Status === 'JOB ORDER'){
            $employeeID = 'JO-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'CONTRACT OF SERVICE'){
            $employeeID = 'CoS-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'PERMANENT'){
            $employeeID = 'P-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'ELECTED'){
            $employeeID = 'E-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'CO-TERMINUS'){
            $employeeID = 'CT-' . $employees->Employee_id;
        }

        $pagibigNo = $employees->pagibig_no;
        $philhealthNo = $employees->philhealth_no;
        $tinNo = '';
		// dd(public_path().'\assets\SIGNATURES\\'.$employees->Employee_id.'.jpg');
		// if (file_exists(public_path().'\assets\SIGNATURES\\'.$employees->Employee_id.'.jpg')) {
			// dd('ok');
		// }else{
			// dd('not found');
		// }

        // if(  preg_match( '/(\d{4})(\d{4})(\d{4})$/', $employees->pagibig_no,  $matches ) )
        // {
        //     $pagibigNo = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
        // }

        if(  preg_match( '/(\d{3})(\d{3})(\d{3})$/', $employees->tin_no,  $matches ) )
        {
            $tinNo = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
        }

        $pdf = App::make('snappy.pdf.wrapper');

        $pages = [
            '<html>
                <head>
                    <style>
                        body{
                            background-image: url("file:///xampp/htdocs/IDProduction/public/assets/images/front.jpg");
                            background-size: 100% 100%;
                        }
                        div.idPicture{
                            position: absolute;
                            left: 0;
                            right: 0;
                            margin-left: auto;
                            margin-right: auto;
                            width: 23.5mm;
                            height: 27.6mm;
                            border: none;
                            margin-top: 14mm;
                            text-align: center;
                            overflow: hidden;
                        }

                        .idPicture img {
                            width: '.$pictureSize.'%;
                            height: 100%;
                            margin: 0 -9.5mm  0;
                        }

                        div.idNumber{
                            position: absolute;
                            width: 15mm;
                            height: 4mm;
                            border: none;
                            padding: 4px 0px;
                            margin-top: 35.5mm;
                            margin-left: 47.4mm;
                            text-align: center;
                            font-size: '.$IDNoFontSize.'px;
                        }

                        div.employeeName{
                            position: absolute;
                            left: 0;
                            right: 0;
                            margin-left: auto;
                            margin-right: auto;
                            width: 61mm;
                            height: 5.5mm;
                            border: none;
                            padding: 7px 2px;
                            margin-top: 44.5mm;
                            text-align: center;
                            font-family: Century Gothic;
                            font-weight: bold;
                            font-size: '.$nameFontSize.'px;
                            color: white;
                        }

                        div.employeePosition{
                            position: absolute;
                            left: 0;
                            right: 0;
                            margin-left: 7mm;
                            margin-right: auto;
                            width: 55mm;
                            height: 8.8mm;
                            border: none;
                            padding: 0px 2px;
                            margin-top: 55.5mm;
                            text-align: center;
                            font-family: Century;
                            text-shadow: -1px 1px #FFF;
                            font-size: '.$positionFontSize.'px;
                            color: black;
                            display: table;
                        }

                        .employeePosition span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.employeeAddress{
                            position: absolute;
                            width: 47mm;
                            height: 7mm;
                            border: none;
                            padding: 0px 2px;
                            margin-top: 66mm;
                            margin-left: 16mm;
                            text-align: left;
                            font-family: Century Gothic;
                            font-weight: bold;
                            letter-spacing: 1px;
                            font-size: '.$addressFontSize.'px;
                            color: black;
                            display: table;
                        }

                        .employeeAddress span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.dateIssued{
                            position: absolute;
                            width: 13mm;
                            height: 3.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 81.3mm;
                            margin-left: 29.5mm;
                            text-align: left;
                            font-family: Arial;
                            letter-spacing: 1pt;
                            font-size: 8px;
                            color: black;
                            display: table;
                        }

                        div.dateValid{
                            position: absolute;
                            width: 13mm;
                            height: 3.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 85.1mm;
                            margin-left: 29.5mm;
                            text-align: left;
                            font-family: Arial;
                            letter-spacing: 1pt;
                            font-size: 8px;
                            color: black;
                            display: table;
                        }

                        div.signature{
                            position: absolute;
                            width: 20.8mm;
                            height: 7.5mm;
                            border: none;
                            padding: 0px 0px;
                            align-content: center;
                            margin-top: 83mm;
                            margin-left: 44mm;
                        }

						.signature img {
                            width: 100%;
                            height: 100%;
                            margin: -1.5mm -1mm  0;
                        }

                        div.officeName{
                            position: absolute;
                            left: 0;
                            right: 0;
                            margin-left: auto;
                            margin-right: auto;
                            width: 65mm;
                            height: 7mm;
                            line-height: 12px;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 350px;
                            text-align: center;
                            font-family: Arial;
                            font-size: '.$officeFontSize.'px;
                            display: table;
                            letter-spacing: 1px;
                            color: white;
                        }

                        .officeName span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.qrCode{
                            position: absolute;
                            width: 55px;
                            height: 55px;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 74.5mm;
                            margin-left: 0mm;
                            text-align: center;
                            display: table;
                        }

                        .qrCode span {
                            vertical-align:middle;
                            display: table-cell;
                        }
                    </style>
                </head>
                <body>
                    <p>&nbsp</p>

                    <div class="idNumber">'.$employeeID.'</div>
                    <div class="idPicture">
                        <img src="file:///xampp/htdocs/IDProduction/public/assets/GeneratedImages/IdPicture.jpg" width="100%"/>
                    </div>
                    <div class="employeeName">'.$employees->fullname.'</div>
                    <div class="employeePosition"><span>'.$position.'</span></div>
                    <div class="employeeAddress"><span>'.$employees->Address.'</span></div>
                    <div class="signature"><img src="file:///xampp/htdocs/IDProduction/public/assets/GeneratedImages/Signature.jpg" width="95%"/></div>
                    <div class="dateIssued"><span>'.Carbon::parse(strtotime('07/11/2022'))->format('m-d-Y').'</span></div>
                    <div class="dateValid"><span>'.Carbon::parse(strtotime('06/30/2025'))->format('m-d-Y').'</span></div>
                    <div class="officeName"><span>'.$office.'</span></div>
                    <div class="qrCode"><span>'.QrCode::size(50)->generate('Province of Surigao del Sur').'</span></div>
                </body>
            </html>',
            '<html>
                <head>
                    <style>
                        body{
                            background-image: url("file:///xampp/htdocs/IDProduction/public/assets/images/back.jpg");
                            background-size: 100% 100%;
                        }

                        div.gsisPolicy{
                            position: absolute;
                            width: 105px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 78px;
                            margin-left: 10px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .gsisPolicy span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.gsisUmid{
                            position: absolute;
                            width: 105px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 78px;
                            margin-left: 135.5px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .gsisUmid span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.pagibig{
                            position: absolute;
                            width: 105px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 118px;
                            margin-left: 10px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .pagibig span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.tin{
                            position: absolute;
                            width: 105px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 118px;
                            margin-left: 135.5px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .tin span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.philhealth{
                            position: absolute;
                            width: 105px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 153.8px;
                            margin-left: 10px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .philhealth span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.bloodtype{
                            position: absolute;
                            width: 105px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 153.8px;
                            margin-left: 135.5px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .bloodtype span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.dob{
                            position: absolute;
                            margin-left: 71px;
                            width: 110px;
                            height: 5.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 193.8px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            text-align: center;
                            color: black;
                            display: table;
                        }

                        .dob span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.inCaseName{
                            position: absolute;
                            width: 190px;
                            height: 10px;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 252px;
                            margin-left: 40px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            font-weight: bold;
                            letter-spacing: 1px;
                            color: black;
                        }

                        div.inCaseAddress{
                            position: absolute;
                            width: 190px;
                            height: 22px;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 262px;
                            margin-left: 40px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            letter-spacing: 1px;
                            color: black;
                            display: table;
                        }

                        .inCaseAddress span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.inCaseContact{
                            position: absolute;
                            width: 190px;
                            height: 10px;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 285px;
                            margin-left: 40px;
                            text-align: left;
                            font-family: Arial;
                            font-size: 8px;
                            letter-spacing: 1px;
                            color: black;
                        }
                    </style>
                </head>
                <body>
                    <div class="gsisPolicy"><span>'.$employees->gsis_no.'</span></div>
                    <div class="gsisUmid"><span>'.$employees->gsis_umid_no.'</span></div>
                    <div class="pagibig"><span>'.$pagibigNo.'</span></div>
                    <div class="tin"><span>'.$tinNo.'</span></div>
                    <div class="philhealth"><span>'.$philhealthNo.'</span></div>
                    <div class="bloodtype"><span>'.$employees->blood_type.'</span></div>
                    <div class="dob"><span>'.Carbon::parse($employees->Birthdate)->format('F d, Y').'</span></div>
                    <div class="inCaseName">'.$employees->name_emergency.'</div>
                    <div class="inCaseAddress"><span>'.$employees->address_emergency.'</span></div>
                    <div class="inCaseContact">'.$employees->contact_emergency.'</div>
                </body>
            </html>'
        ];
        $pdf->loadHTML($pages)
            ->setOrientation('portrait')
            ->setOption('page-width', '53.975')
            ->setOption('page-height', '85.725')
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-top', 0);

        return $pdf->inline();
    }

    public function bigProduction($id, $IDNoFontSize, $nameFontSize, $positionFontSize, $addressFontSize, $officeFontSize, $pictureSize, $position, $office)
    {
        $employeeID = '';
        $employees = Employee::where('Employee_id', $id)->first(['Employee_id', 'FirstName', 'MiddleName', 'LastName', 'Suffix', 'Work_Status', 'PosCode', 'OfficeCode2', 'Address', 'ImagePhoto', 'signaturephoto']);

        //generate IdPicture and Signature Images
        file_put_contents(public_path('assets\\GeneratedImages\\IdPicture.jpg'), $employees->ImagePhoto);
        file_put_contents(public_path('assets\\GeneratedImages\\Signature.jpg'), $employees->signaturephoto);

        if($employees->Work_Status === 'JOB ORDER'){
            $employeeID = 'JO-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'CONTRACT OF SERVICE'){
            $employeeID = 'CoS-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'PERMANENT'){
            $employeeID = 'P-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'ELECTED'){
            $employeeID = 'E-' . $employees->Employee_id;
        }elseif($employees->Work_Status === 'CO-TERMINUS'){
            $employeeID = 'CT-' . $employees->Employee_id;
        }

        $pdf = App::make('snappy.pdf.wrapper');

        $pages = [
            '<html>
                <head>
                    <style>
                        div.cardImageLeft{
                            float: left;
                            width: 4in;
                            height: 6.232in;
                            border: 2.5px solid gray;
                            margin-left: 4px;
                            background: url("file:///xampp/htdocs/IDProduction/public/assets/images/bigfront.jpg");
                            background-size: cover;
                            background-repeat: no-repeat;
                            background-position: center center;
                        }

                        div.cardImageRight{
                            float: right;
                            width: 4in;
                            height: 6.232in;
                            border: 2.5px solid gray;
                            margin-right: 4px;
                            background: url("file:///xampp/htdocs/IDProduction/public/assets/images/bigfront.jpg");
                            background-size: cover;
                            background-repeat: no-repeat;
                            background-position: center center;
                        }

                        div.idPictureleft{
                            position: absolute;
                            width: 34.5mm;
                            height: 41mm;
                            border: none;
                            margin-top: 17.8mm;
                            margin-left: 33.5mm;
                            text-align: center;
                            overflow: hidden;
                        }

                        .idPictureleft img {
                            width: '.$pictureSize.'%;
                            height: 100%;
                            margin: 0 -9mm  0;
                        }

                        div.idPictureright{
                            position: absolute;
                            width: 34.5mm;
                            height: 41mm;
                            border: none;
                            margin-top: 17.8mm;
                            margin-left: 33.5mm;
                            text-align: center;
                            overflow: hidden;
                        }

                        .idPictureright img {
                            width: '.$pictureSize.'%;
                            height: 100%;
                            margin: 0 -9mm  0;
                        }

                        div.idNumber{
                            width: 21.5mm;
                            height: 8.5mm;
                            border: none;
                            padding: 3px;
                            margin-top: 52mm;
                            margin-left: 75mm;
                            font-size: '.$IDNoFontSize.'px;
                            text-align: center;
                            display: table;
                        }

                        .idNumber span{
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.employeeName{
                            width: 95mm;
                            height: 13mm;
                            border: none;
                            padding: 7px 2px;
                            margin-top: 6.3mm;
                            margin-left: 4mm;
                            text-align: center;
                            font-family: Century Gothic;
                            font-weight: bold;
                            font-size: '.$nameFontSize.'px;
                            display: block;
                            white-space: nowrap;
                            color: white;
                            display: table;
                        }

                        .employeeName span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.employeePosition{
                            width: 82mm;
                            height: 12mm;
                            border: none;
                            padding: 0px 2px;
                            margin-top: 2.8mm;
                            margin-left: 11mm;
                            text-align: center;
                            font-family: Century;
                            text-shadow: -1px 1px #FFF;
                            font-size: '.$positionFontSize.'px;
                            color: black;
                            display: table;
                        }

                        .employeePosition span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.employeeAddress{
                            width: 70mm;
                            height: 11mm;
                            border: none;
                            padding: 0px 2px;
                            margin-top: 5mm;
                            margin-left: 28mm;
                            text-align: left;
                            font-family: Century Gothic;
                            font-weight: bold;
                            font-size: '.$addressFontSize.'px;
                            color: black;
                            display: table;
                        }

                        .employeeAddress span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.dateIssued{
                            width: 18mm;
                            height: 3.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 15.1mm;
                            margin-left: 48mm;
                            text-align: left;
                            font-family: Century Gothic;
                            font-size: 10px;
                            letter-spacing: 1px;
                            color: black;
                            display: table;
                        }

                        div.dateValid{
                            width: 18mm;
                            height: 3.5mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 1.6mm;
                            margin-left: 48mm;
                            text-align: left;
                            font-family: Century Gothic;
                            font-size: 10px;
                            letter-spacing: 1px;
                            color: black;
                            display: table;
                        }

                        div.officeName{
                            position: absolute;
                            width: 99mm;
                            height: 11mm;
                            border: none;
                            padding: 5px 0px;
                            margin-top: 3mm;
                            margin-left: 1.5mm;
                            text-align: center;
                            font-family: Arial;
                            font-weight: bold;
                            font-size: '.$officeFontSize.'px;
                            display: table;
                            letter-spacing: 1px;
                            color: white;
                        }

                        .officeName span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                        div.signature{
                            float: right;
                            width: 30mm;
                            height: 11mm;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 15.5mm;
                            margin-left: 68mm;
                            position: absolute;
                        }

						.signature img {
                            width: 100%;
                            height: 100%;
                            margin: -1mm -1mm  0;
                        }

                        div.qrCode{
                            position: absolute;
                            width: 86px;
                            height: 86px;
                            border: none;
                            padding: 0px 0px;
                            margin-top: 3.5mm;
                            margin-left: 3mm;
                            text-align: center;
                            display: table;
                        }

                        .qrCode span {
                            vertical-align:middle;
                            display: table-cell;
                        }

                    </style>
                </head>
                <body>
                    <div class="cardImageLeft">
                        <p>&nbsp</p>
                        <div class="idPictureleft">
                            <img src="file:///xampp/htdocs/IDProduction/public/assets/GeneratedImages/IdPicture.jpg" width="'.$pictureSize.'%" />
                        </div>
                        <div class="idNumber">'.$employeeID.'</div>
                        <div class="employeeName"><span>'.$employees->fullname.'</span></div>
                        <div class="employeePosition"><span>'.$position.'</span></div>
                        <div class="employeeAddress"><span>'.$employees->Address.'</span></div>
                        <div class="signature"><img src="file:///xampp/htdocs/IDProduction/public/assets/GeneratedImages/Signature.jpg" width="100%"/></div>
                        <div class="qrCode"><span>'.QrCode::size(70)->generate('Province of Surigao del Sur').'</span></div>
                        <div class="dateIssued"><span>'.Carbon::parse(strtotime('07/11/2022'))->format('m-d-Y').'</span></div>
                        <div class="dateValid"><span>'.Carbon::parse(strtotime('06/30/2025'))->format('m-d-Y').'</span></div>
                        <div class="officeName"><span>'.$office.'</span></div>
                    </div>
                    <div class="cardImageRight">
                        <p>&nbsp</p>
                        <div class="idPictureright">
                            <img src="file:///xampp/htdocs/IDProduction/public/assets/GeneratedImages/IdPicture.jpg" width="'.$pictureSize.'%"/>
                        </div>
                        <div class="idNumber">'.$employeeID.'</div>
                        <div class="employeeName"><span>'.$employees->fullname.'</span></div>
                        <div class="employeePosition"><span>'.$position.'</span></div>
                        <div class="employeeAddress"><span>'.$employees->Address.'</span></div>
                        <div class="signature"><img src="file:///xampp/htdocs/IDProduction/public/assets/GeneratedImages/Signature.jpg" width="100%"/></div>
                        <div class="qrCode"><span>'.QrCode::size(70)->generate('Province of Surigao del Sur').'</span></div>
                        <div class="dateIssued"><span>'.Carbon::parse(strtotime('07/11/2022'))->format('m-d-Y').'</span></div>
                        <div class="dateValid"><span>'.Carbon::parse(strtotime('06/30/2025'))->format('m-d-Y').'</span></div>
                        <div class="officeName"><span>'.$office.'</span></div>
                    </div>
                </body>
            </html>'
        ];
        $pdf->loadHTML($pages)
            ->setOrientation('portrait')
            ->setOption('page-width', '210')
            ->setOption('page-height', '297')
            ->setOption('margin-left', '20')
            ->setOption('margin-right', '20');

        return $pdf->inline();
    }
}
