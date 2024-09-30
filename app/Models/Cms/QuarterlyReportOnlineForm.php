<?php

namespace App\Models\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuarterlyReportOnlineForm extends Model
{
    use SoftDeletes;

    protected $fillable = ['quartyMonth','officeName','address','phone','email','bilingual','englishlang','hindilang','totalLettersHindi','repliesHindi','repliesEnglish','noReplyExpected','releasedDocuments','numberOfLetter','replies2Hindi','replies2English','noReply2Expected','numberOf2Letter','replies3Hindi','replies3English','noReply3Expected','numberOfaddress','bilingualone','englishlangone','hindilangone','numberOfoneaddress','bilingualtwo','englishlangtwo','hindilangtwo','numberOfthreeaddress','bilingualthree','englishlangthree','hindilangthree','commentsHindi','commentsEnglish','totalComments','workshopDate','numberOfTrainees','meetingDate','agendaInHindi','achievements','headName','position','phoneNumber','faxNumber','emailAddress','officeCode','financialYear','identification','officerName','officerPost','officerOfficeName','submittedDate','OfficerPhone','placeName','officerEmail','formType','user_ip','status'
    ];
}
