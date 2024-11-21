<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cms\QuarterlyReportOnlineForm;
use Illuminate\Support\Facades\Validator;

class QuarterlyReportOnlineFormsController extends BaseController {

    public function index(Request $request): JsonResponse
    {  
        $data = $request->all();
        $phone = clean_single_input($data['phone']);
       // $slugs = explode(',',$data['slugs']);

        if ($phone === null) {
            return $this->sendError(' phone parameter is missing.', 400);
        }
        //dd($slugs);
        // if ($slugs === null) {
        //     return $this->sendError('Slugs parameter is missing.', 400);
        // }
        $QuarterlyReportOnlineForm = QuarterlyReportOnlineForm::where('phone', $phone)
        //->whereIn('slugs', $slugs)
        ->select('id', 'quartyMonth','officeName','address','phone','email','bilingual','englishlang','hindilang','totalLettersHindi','repliesHindi','repliesEnglish','noReplyExpected','releasedDocuments','numberOfLetter','replies2Hindi','replies2English','noReply2Expected','numberOf2Letter','replies3Hindi','replies3English','noReply3Expected','numberOfaddress','bilingualone','englishlangone','hindilangone','numberOfoneaddress','bilingualtwo','englishlangtwo','hindilangtwo','numberOfthreeaddress','bilingualthree','englishlangthree','hindilangthree','commentsHindi','commentsEnglish','totalComments','workshopDate','numberOfTrainees','meetingDate','agendaInHindi','achievements','headName','position','phoneNumber','faxNumber','emailAddress','officeCode','financialYear','identification','officerName','officerPost','officerOfficeName','submittedDate','OfficerPhone','placeName','officerEmail','formType','user_ip','status')
        ->get();
        
        return $this->sendResponse($QuarterlyReportOnlineForm, 'QuarterlyReportOnlineForm  For Instructor Retrieved Successfully.');
    }
    public function store(Request $request)
    {
       // dd($request);
        $rules = [
            'quartyMonth' => 'required|max:32',
            'officeName' => 'required|max:50',
            'address' => 'required|max:250',
            'phone' => 'required|regex:/^[0-9]*$/|max:12',
            'email' => 'required|email|max:120',
            'bilingual' => 'required|max:25',
            'englishlang' => 'required|max:25',
            'hindilang' => 'required|max:25',
            'totalLettersHindi' => 'required|max:25',
            'repliesHindi' => 'required|max:25',
            'repliesEnglish' => 'required|max:25',
            'noReplyExpected' => 'required|max:25',
            'releasedDocuments' => 'required|max:25',
            'numberOfLetter' => 'required|regex:/^[0-9]*$/|max:10',
            'replies2Hindi' => 'required|max:25',
            'replies2English' => 'required|max:25',
            'noReply2Expected' => 'required|max:25',
            'numberOf2Letter' => 'required|regex:/^[0-9]*$/|max:10',
            'replies3Hindi' => 'required|max:25',
            'replies3English' => 'required|max:25',
            'noReply3Expected' => 'required|max:25',
            'numberOfaddress' => 'required|max:25',
            'bilingualone' => 'required|max:25',
            'englishlangone' => 'required|max:25',
            'hindilangone' => 'required|max:25',
            'numberOfoneaddress' => 'required|max:25',
            'bilingualtwo' => 'required|max:25',
            'englishlangtwo' => 'required|max:25',
            'hindilangtwo' => 'required|max:25',
            'numberOfthreeaddress' => 'required|max:25',
            'bilingualthree' => 'required|max:25',
            'englishlangthree' => 'required|max:25',
            'hindilangthree' => 'required|max:25',
            'commentsHindi' => 'required|max:250',
            'commentsEnglish' => 'required|max:250',
            'totalComments' => 'required|max:25',
            'workshopDate' => 'required|date',
            'numberOfTrainees' => 'required|regex:/^[0-9]*$/|max:10',
            'meetingDate' => 'required|date',
            'agendaInHindi' => 'required',
            'achievements' => 'required|max:250',
            'headName' => 'required',
            'position' => 'required',
            'phoneNumber' => 'required|regex:/^[0-9]*$/|max:12',
            'faxNumber' => 'nullable', // Optional field
            'emailAddress' => 'required|email',
            'officeCode' => 'nullable', // Optional field
            'financialYear' => 'required',
            'identification' => 'required|max:50',
            'officerName' => 'required|max:50',
            'officerPost' => 'required|max:50',
            'officerOfficeName' => 'required|max:150',
            'submittedDate' => 'required|date',
            'OfficerPhone' => 'required|regex:/^[0-9]*$/|max:12',
            'placeName' => 'required|max:25',
            'officerEmail' => 'required|email',
            // 'formType' => 'required',
            // 'user_ip' => 'required',
            // 'status' => 'required'
            
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error.',
                'messages' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        $data['ruleActs'] = clean_single_input(strip_tags($request->ruleActs));
        $data['quartyMonth'] = clean_single_input(strip_tags($request->quartyMonth));
        $data['officeName'] = clean_single_input(strip_tags($request->officeName));
        $data['address'] = clean_single_input(strip_tags($request->address));
        $data['phone'] = clean_single_input(strip_tags($request->phone));
        $data['email'] = clean_single_input(strip_tags($request->email));
        $data['bilingual'] = clean_single_input(strip_tags($request->bilingual));
        $data['englishlang'] = clean_single_input(strip_tags($request->englishlang));
        $data['hindilang'] = clean_single_input(strip_tags($request->hindilang));
        $data['totalLettersHindi'] = clean_single_input(strip_tags($request->totalLettersHindi));
        $data['repliesHindi'] = clean_single_input(strip_tags($request->repliesHindi));
        $data['repliesEnglish'] = clean_single_input(strip_tags($request->repliesEnglish));
        $data['noReplyExpected'] = clean_single_input(strip_tags($request->noReplyExpected));
        $data['releasedDocuments'] = clean_single_input(strip_tags($request->releasedDocuments));
        $data['numberOfLetter'] = clean_single_input(strip_tags($request->numberOfLetter));
        $data['replies2Hindi'] = clean_single_input(strip_tags($request->replies2Hindi));
        $data['replies2English'] = clean_single_input(strip_tags($request->replies2English));
        $data['noReply2Expected'] = clean_single_input(strip_tags($request->noReply2Expected));
        $data['numberOf2Letter'] = clean_single_input(strip_tags($request->numberOf2Letter));
        $data['replies3Hindi'] = clean_single_input(strip_tags($request->replies3Hindi));
        $data['replies3English'] = clean_single_input(strip_tags($request->replies3English));
        $data['noReply3Expected'] = clean_single_input(strip_tags($request->noReply3Expected));
        $data['numberOfaddress'] = clean_single_input(strip_tags($request->numberOfaddress));
        $data['bilingualone'] = clean_single_input(strip_tags($request->bilingualone));
        $data['englishlangone'] = clean_single_input(strip_tags($request->englishlangone));
        $data['hindilangone'] = clean_single_input(strip_tags($request->hindilangone));
        $data['numberOfoneaddress'] = clean_single_input(strip_tags($request->numberOfoneaddress));
        $data['bilingualtwo'] = clean_single_input(strip_tags($request->bilingualtwo));
        $data['englishlangtwo'] = clean_single_input(strip_tags($request->englishlangtwo));
        $data['hindilangtwo'] = clean_single_input(strip_tags($request->hindilangtwo));
        $data['numberOfthreeaddress'] = clean_single_input(strip_tags($request->numberOfthreeaddress));
        $data['bilingualthree'] = clean_single_input(strip_tags($request->bilingualthree));
        $data['englishlangthree'] = clean_single_input(strip_tags($request->englishlangthree));
        $data['hindilangthree'] = clean_single_input(strip_tags($request->hindilangthree));
        $data['commentsHindi'] = clean_single_input(strip_tags($request->commentsHindi));
        $data['commentsEnglish'] = clean_single_input(strip_tags($request->commentsEnglish));
        $data['totalComments'] = clean_single_input(strip_tags($request->totalComments));
        $data['workshopDate'] = clean_single_input(strip_tags($request->workshopDate));
        $data['numberOfTrainees'] = clean_single_input(strip_tags($request->numberOfTrainees));
        $data['meetingDate'] = clean_single_input(strip_tags($request->meetingDate));
        $data['agendaInHindi'] = clean_single_input(strip_tags($request->agendaInHindi));
        $data['achievements'] = clean_single_input(strip_tags($request->achievements));
        $data['headName'] = clean_single_input(strip_tags($request->headName));
        $data['position'] = clean_single_input(strip_tags($request->position));
        $data['phoneNumber'] = clean_single_input(strip_tags($request->phoneNumber));
        $data['faxNumber'] = clean_single_input(strip_tags($request->faxNumber));
        $data['emailAddress'] = clean_single_input(strip_tags($request->emailAddress));
        $data['officeCode'] = clean_single_input(strip_tags($request->officeCode));
        $data['financialYear'] = clean_single_input(strip_tags($request->financialYear));
        $data['identification'] = clean_single_input(strip_tags($request->identification));
        $data['officerName'] = clean_single_input(strip_tags($request->officerName));
        $data['officerPost'] = clean_single_input(strip_tags($request->officerPost));
        $data['officerOfficeName'] = clean_single_input(strip_tags($request->officerOfficeName));
        $data['submittedDate'] = clean_single_input(strip_tags($request->submittedDate));
        $data['OfficerPhone'] = clean_single_input(strip_tags($request->OfficerPhone));
        $data['placeName'] = clean_single_input(strip_tags($request->placeName));
        $data['officerEmail'] = clean_single_input(strip_tags($request->officerEmail));
        $data['user_ip'] = clean_single_input(strip_tags($request->user_ip));
        $data['status'] =  (int)clean_single_input(strip_tags($request->status));
        //$data['formType'] = clean_single_input(strip_tags($request->formType));

        $QuarterlyReportOnlineFormdata = QuarterlyReportOnlineForm::create($data);
        $messages=[];
        if($QuarterlyReportOnlineFormdata){
          $messages['messages']='Thank you';
          $messages['id']=$QuarterlyReportOnlineFormdata->id;
        }
        return response()->json($messages, 201); // 201 Created
    }
    public function cms_data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $QuarterlyReportOnlineForm = QuarterlyReportOnlineForm::select('id', 'quartyMonth','officeName','address','phone','email','bilingual','englishlang','hindilang','totalLettersHindi','repliesHindi','repliesEnglish','noReplyExpected','releasedDocuments','numberOfLetter','replies2Hindi','replies2English','noReply2Expected','numberOf2Letter','replies3Hindi','replies3English','noReply3Expected','numberOfaddress','bilingualone','englishlangone','hindilangone','numberOfoneaddress','bilingualtwo','englishlangtwo','hindilangtwo','numberOfthreeaddress','bilingualthree','englishlangthree','hindilangthree','commentsHindi','commentsEnglish','totalComments','workshopDate','numberOfTrainees','meetingDate','agendaInHindi','achievements','headName','position','phoneNumber','faxNumber','emailAddress','officeCode','financialYear','identification','officerName','officerPost','officerOfficeName','submittedDate','OfficerPhone','placeName','officerEmail','formType','user_ip','status')
        ->orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        $QuarterlyReportOnlineForm->getCollection()->transform(function ($item) {
            $item->submittedDate = date('d-m-Y', strtotime($item->submittedDate));
            return $item;
        });
        
        return response()->json($QuarterlyReportOnlineForm);
    }
    
}
