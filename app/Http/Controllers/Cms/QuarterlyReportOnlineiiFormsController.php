<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cms\QuarterlyReportOnlineiiForm;
use Illuminate\Support\Facades\Validator;

class QuarterlyReportOnlineiiFormsController extends BaseController
{

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
        $QuarterlyReportOnlineiiForm = QuarterlyReportOnlineiiForm::where('phone', $phone)
            //->whereIn('slugs', $slugs)
            ->select(
                'id',
                'quartyMonth',
                'officeName',
                'address',
                'phone',
                'email',
                'bilingual',
                'englishlang',
                'hindilang',
                'totalLettersHindi',
                'repliesHindi',
                'repliesEnglish',
                'noReplyExpected',
                'releasedDocuments',
                'numberOfLetter',
                'repliesiiHindi',
                'repliesiiEnglish',
                'noReplyiiExpected',
                'numberOfiiLetter',
                'replies3Hindi',
                'replies3English',
                'noReply3Expected',
                'numberOfaddress',
                'bilingualone',
                'englishlangone',
                'hindilangone',
                'numberOfoneaddress',
                'bilingualtwo',
                'englishlangtwo',
                'hindilangtwo',
                'numberOfthreeaddress',
                'bilingualthree',
                'englishlangthree',
                'hindilangthree',
                'commentsHindi',
                'commentsEnglish',
                'totalComments',
                'workshopDate',
                'numberOfTrainees',
                'meetingDate',
                'agendaInHindi',
                'achievements',
                'headName',
                'position',
                'phoneNumber',
                'faxNumber',
                'emailAddress',
                'officeCode',
                'financialYear',
                'identification',
                'officerName',
                'officerPost',
                'officerOfficeName',
                'submittedDate',
                'OfficerPhone',
                'placeName',
                'officerEmail',
                'formType',
                'user_ip',
                'status'
            )
            ->get();

        return $this->sendResponse($QuarterlyReportOnlineiiForm, 'QuarterlyReportOnlineiiForm  For Instructor Retrieved Successfully.');
    }
    public function store(Request $request)
    {
        // dd($request);
        $rules = [
            'form_1' => 'nullable|string|max:11',
            'ruleActs' => 'nullable|string|max:11',
            'totalKnow' => 'nullable|integer',
            'knowExecutive' => 'nullable|integer',
            'knowProficient' => 'nullable|integer',
            'trainedExecutive' => 'nullable|integer',
            'trainedProficient' => 'nullable|integer',
            'remainingTrainedExecutive' => 'nullable|integer',
            'remainingTrainedProficient' => 'nullable|integer',
            'totalShorthand' => 'nullable|integer',
            'trainedShorthand' => 'nullable|integer',
            'workShorthand' => 'nullable|integer',
            'remainingShorthand' => 'nullable|integer',
            'totalClerk' => 'nullable|integer',
            'trainedClerk' => 'nullable|integer',
            'workingClerk' => 'nullable|integer',
            'remainingClerk' => 'nullable|integer',
            'totalPostal' => 'nullable|integer',
            'trainedPostal' => 'nullable|integer',
            'workingPostal' => 'nullable|integer',
            'remainingPostal' => 'nullable|integer',
            'totalTranslator' => 'nullable|integer',
            'authTranslator' => 'nullable|integer',
            'empTranslator' => 'nullable|integer',
            'totalTrainedTranslator' => 'nullable|integer',
            'authTrainedTranslator' => 'nullable|integer',
            'empTrainedTranslator' => 'nullable|integer',
            'totalYetTranslator' => 'nullable|integer',
            'authYetTranslator' => 'nullable|integer',
            'empYetTranslator' => 'nullable|integer',
            'totalComputerOperator' => 'nullable|integer',
            'authComputerOperator' => 'nullable|integer',
            'empComputerOperator' => 'nullable|integer',
            'totalComputer' => 'nullable|integer',
            'totalWorkingComputer' => 'nullable|integer',
            'totalManuals' => 'nullable|integer',
            'totalManualsBilingual' => 'nullable|integer',
            'totalForms' => 'nullable|integer',
            'totalFormsBilingual' => 'nullable|integer',
            'totalWorkingHind' => 'nullable|string|max:255',
            'totalConductedSec' => 'nullable|integer',
            'totalinspectedSec' => 'nullable|integer',
            'totalJournals' => 'nullable|integer',
            'totalJournalsHindi' => 'nullable|integer',
            'totalJournalsEnglish' => 'nullable|integer',
            'totalExpenditure' => 'nullable|integer',
            'totalExpenditureHindi' => 'nullable|integer',
            'totalEquivalent' => 'nullable|integer',
            'totalEquivalentHindi' => 'nullable|integer',
            'totalEquivalentWorkingHindi' => 'nullable|string|max:255',
            'totalProficiency' => 'nullable|integer',
            'totalProficiencyHindi' => 'nullable|integer',
            'totalProficiencyWorkingHindi' => 'nullable|string|max:255',
            'totalWebsite' => 'nullable|string|max:255',
            //'totalWebsiteHindi' => 'nullable|integer',
            'totalWebsiteWorkingHindi' => 'nullable|string|max:255',
            'totalworkshop' => 'nullable|string|max:255',
            'workshopfrom' => 'nullable|string|max:255',
            'workshopTo' => 'nullable|string|max:255',
            'headName' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phoneNumber' => 'nullable|string|max:15',
            'faxNumber' => 'nullable|string|max:15',
            'emailAddress' => 'nullable|string|email|max:100',
            'officeCode' => 'nullable|string|max:50',
            'financialYear' => 'nullable|string|max:50',
            'identification' => 'nullable|string|max:50',
            'officerName' => 'nullable|string|max:100',
            'officerPost' => 'nullable|string|max:100',
            'officerOfficeName' => 'nullable|string|max:100',
            'submittedDate' => 'nullable|date',
            'OfficerPhone' => 'nullable|string|max:15',
            'placeName' => 'nullable|string|max:100',
            'officerEmail' => 'nullable|string|email|max:100',
            // 'formType' => 'nullable|string|max:11',
            // 'user_ip' => 'nullable|string|max:111',
            // 'status' => 'nullable|string|max:11',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation Error.',
                'messages' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        // Initialize an array to hold cleaned data
        $data = [];

        // Sanitize inputs for each field 
        $data['form_1'] =  (int)clean_single_input(strip_tags($request->form_1));
        $data['ruleActs'] = clean_single_input(strip_tags($request->ruleActs));
        $data['totalKnow'] =  (int)clean_single_input(strip_tags($request->totalKnow));
        $data['knowExecutive'] =  (int)clean_single_input(strip_tags($request->knowExecutive));
        $data['knowProficient'] =  (int)clean_single_input(strip_tags($request->knowProficient));
        $data['trainedExecutive'] =  (int)clean_single_input(strip_tags($request->trainedExecutive));
        $data['trainedProficient'] =  (int)clean_single_input(strip_tags($request->trainedProficient));
        $data['remainingTrainedExecutive'] =  (int)clean_single_input(strip_tags($request->remainingTrainedExecutive));
        $data['remainingTrainedProficient'] =  (int)clean_single_input(strip_tags($request->remainingTrainedProficient));
        $data['totalShorthand'] =  (int)clean_single_input(strip_tags($request->totalShorthand));
        $data['trainedShorthand'] =  (int)clean_single_input(strip_tags($request->trainedShorthand));
        $data['workShorthand'] =  (int)clean_single_input(strip_tags($request->workShorthand));
        $data['remainingShorthand'] =  (int)clean_single_input(strip_tags($request->remainingShorthand));
        $data['totalClerk'] =  (int)clean_single_input(strip_tags($request->totalClerk));
        $data['trainedClerk'] =  (int)clean_single_input(strip_tags($request->trainedClerk));
        $data['workingClerk'] =  (int)clean_single_input(strip_tags($request->workingClerk));
        $data['remainingClerk'] =  (int)clean_single_input(strip_tags($request->remainingClerk));
        $data['totalPostal'] =  (int)clean_single_input(strip_tags($request->totalPostal));
        $data['trainedPostal'] =  (int)clean_single_input(strip_tags($request->trainedPostal));
        $data['workingPostal'] =  (int)clean_single_input(strip_tags($request->workingPostal));
        $data['remainingPostal'] =  (int)clean_single_input(strip_tags($request->remainingPostal));
        $data['totalTranslator'] =  (int)clean_single_input(strip_tags($request->totalTranslator));
        $data['authTranslator'] =  (int)clean_single_input(strip_tags($request->authTranslator));
        $data['empTranslator'] =  (int)clean_single_input(strip_tags($request->empTranslator));
        $data['totalTrainedTranslator'] =  (int)clean_single_input(strip_tags($request->totalTrainedTranslator));
        $data['authTrainedTranslator'] =  (int)clean_single_input(strip_tags($request->authTrainedTranslator));
        $data['empTrainedTranslator'] =  (int)clean_single_input(strip_tags($request->empTrainedTranslator));
        $data['totalYetTranslator'] =  (int)clean_single_input(strip_tags($request->totalYetTranslator));
        $data['authYetTranslator'] =  (int)clean_single_input(strip_tags($request->authYetTranslator));
        $data['empYetTranslator'] =  (int)clean_single_input(strip_tags($request->empYetTranslator));
        $data['totalComputerOperator'] =  (int)clean_single_input(strip_tags($request->totalComputerOperator));
        $data['authComputerOperator'] =  (int)clean_single_input(strip_tags($request->authComputerOperator));
        $data['empComputerOperator'] =  (int)clean_single_input(strip_tags($request->empComputerOperator));
        $data['totalComputer'] =  (int)clean_single_input(strip_tags($request->totalComputer));
        $data['totalWorkingComputer'] =  (int)clean_single_input(strip_tags($request->totalWorkingComputer));
        $data['totalManuals'] =  (int)clean_single_input(strip_tags($request->totalManuals));
        $data['totalManualsBilingual'] =  (int)clean_single_input(strip_tags($request->totalManualsBilingual));
        $data['totalForms'] =  (int)clean_single_input(strip_tags($request->totalForms));
        $data['totalFormsBilingual'] = (int)clean_single_input(strip_tags($request->totalFormsBilingual));
        $data['totalWorkingHind'] =  (int)clean_single_input(strip_tags($request->totalWorkingHind));
        $data['totalConductedSec'] =  (int)clean_single_input(strip_tags($request->totalConductedSec));
        $data['totalinspectedSec'] =  (int)clean_single_input(strip_tags($request->totalinspectedSec));
        $data['totalJournals'] =  (int)clean_single_input(strip_tags($request->totalJournals));
        $data['totalJournalsHindi'] =  (int)clean_single_input(strip_tags($request->totalJournalsHindi));
        $data['totalJournalsEnglish'] =  (int)clean_single_input(strip_tags($request->totalJournalsEnglish));
        $data['totalExpenditure'] =  (int)clean_single_input(strip_tags($request->totalExpenditure));
        $data['totalExpenditureHindi'] =  (int)clean_single_input(strip_tags($request->totalExpenditureHindi));
        $data['totalEquivalent'] =  (int)clean_single_input(strip_tags($request->totalEquivalent));
        $data['totalEquivalentHindi'] =  (int)clean_single_input(strip_tags($request->totalEquivalentHindi));
        $data['totalEquivalentWorkingHindi'] =  (int)clean_single_input(strip_tags($request->totalEquivalentWorkingHindi));
        $data['totalProficiency'] =  (int)clean_single_input(strip_tags($request->totalProficiency));
        $data['totalProficiencyHindi'] =  (int)clean_single_input(strip_tags($request->totalProficiencyHindi));
        $data['totalProficiencyWorkingHindi'] =  (int)clean_single_input(strip_tags($request->totalProficiencyWorkingHindi));
        $data['totalWebsite'] =  clean_single_input(strip_tags($request->totalWebsite));
        //$data['totalWebsiteHindi'] =  (int)clean_single_input(strip_tags($request->totalWebsiteHindi));
        $data['totalWebsiteWorkingHindi'] = clean_single_input(strip_tags($request->totalWebsiteWorkingHindi));
        $data['totalworkshop'] =  (int)clean_single_input(strip_tags($request->totalworkshop));
        $data['workshopfrom'] = clean_single_input(strip_tags($request->workshopfrom));
        $data['workshopTo'] = clean_single_input(strip_tags($request->workshopTo));
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
        //$data['formType'] = clean_single_input(strip_tags($request->formType));
        $data['user_ip'] = clean_single_input(strip_tags($request->user_ip));
        $data['status'] = (int) clean_single_input(strip_tags($request->status));

        $QuarterlyReportOnlineiiFormdata = QuarterlyReportOnlineiiForm::create($data);
        $messages = [];
        if ($QuarterlyReportOnlineiiFormdata) {
            $messages['messages'] = 'Thank you';
            $messages['id'] = $QuarterlyReportOnlineiiFormdata->id;
        }
        return response()->json($messages, 201); // 201 Created
    }

    // Api

    public function cms_data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $QuarterlyReportOnlineiiForm = QuarterlyReportOnlineiiForm::select('id', 'ruleActs','form_1','totalKnow','knowExecutive','knowProficient','trainedExecutive','trainedProficient','remainingTrainedExecutive','remainingTrainedProficient','totalShorthand','trainedShorthand','workShorthand','remainingShorthand','totalClerk','trainedClerk','workingClerk','remainingClerk','totalPostal','trainedPostal','workingPostal','remainingPostal','totalTranslator','authTranslator','empTranslator','totalTrainedTranslator','authTrainedTranslator','empTrainedTranslator','totalYetTranslator','authYetTranslator','empYetTranslator','totalComputerOperator','authComputerOperator','empComputerOperator','totalComputer','totalWorkingComputer','totalManuals','totalManualsBilingual','totalForms','totalFormsBilingual','totalWorkingHind','totalConductedSec','totalinspectedSec','totalJournals','totalJournalsHindi','totalJournalsEnglish','totalExpenditure','totalExpenditureHindi','totalEquivalent','totalEquivalentHindi','totalEquivalentWorkingHindi','totalProficiency','totalProficiencyHindi','totalProficiencyWorkingHindi','totalWebsite','totalWebsiteHindi','totalWebsiteWorkingHindi','totalworkshop','workshopfrom','workshopTo','headName','position','phoneNumber','faxNumber','emailAddress','officeCode','financialYear','identification','officerName','officerPost','officerOfficeName','submittedDate','OfficerPhone','placeName','officerEmail','formType','user_ip','status')
        ->orderBy('id', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

        $QuarterlyReportOnlineiiForm->getCollection()->transform(function ($item) {
            $item->submittedDate = date('d-m-Y', strtotime($item->submittedDate));
            return $item;
        });

        return response()->json($QuarterlyReportOnlineiiForm);
    }
}
