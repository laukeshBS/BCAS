<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuarterlyReportOnlineiiForm extends Model
{
    use SoftDeletes;

    protected $fillable = ['ruleActs','form_1','totalKnow','knowExecutive','knowProficient','trainedExecutive','trainedProficient','remainingTrainedExecutive','remainingTrainedProficient','totalShorthand','trainedShorthand','workShorthand','remainingShorthand','totalClerk','trainedClerk','workingClerk','remainingClerk','totalPostal','trainedPostal','workingPostal','remainingPostal','totalTranslator','authTranslator','empTranslator','totalTrainedTranslator','authTrainedTranslator','empTrainedTranslator','totalYetTranslator','authYetTranslator','empYetTranslator','totalComputerOperator','authComputerOperator','empComputerOperator','totalComputer','totalWorkingComputer','totalManuals','totalManualsBilingual','totalForms','totalFormsBilingual','totalWorkingHind','totalConductedSec','totalinspectedSec','totalJournals','totalJournalsHindi','totalJournalsEnglish','totalExpenditure','totalExpenditureHindi','totalEquivalent','totalEquivalentHindi','totalEquivalentWorkingHindi','totalProficiency','totalProficiencyHindi','totalProficiencyWorkingHindi','totalWebsite','totalWebsiteHindi','totalWebsiteWorkingHindi','totalworkshop','workshopfrom','workshopTo','headName','position','phoneNumber','faxNumber','emailAddress','officeCode','financialYear','identification','officerName','officerPost','officerOfficeName','submittedDate','OfficerPhone','placeName','officerEmail','formType','user_ip','status'
    ];
}
