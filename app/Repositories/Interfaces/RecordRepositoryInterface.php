<?php

namespace App\Repositories\Interfaces;
use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Http\Request;

interface RecordRepositoryInterface extends EloquentRepositoryInterface
{
    public function Search(Request $request);
    
    public function getRecordVisible($id);

    public function updateMedia($RecordId, $mediaId);

    public function getDetail($recordId);

    public function listRecords();

    public function listRecordsByFolder($folderId);

    public function updateRecord($RecordId, $mediaId, $endTime);

    public function listRecordsWithNoPaginate();

    public function searchRecordsByFolder(Request $request);

    public function searchRecordsByFamily(Request $request);

    public function listRecordsByHospital($hospitalId);

    public function listRecordsByDoctor($doctorId);

    public function listRecordsByMedicine($medicineId);

    public function listRecordsByFamily($familyId);

    public function searchRecords(Request $request);

    public function getRecord($recordId);

    public function listRecordsByKeyword($keywordId);
}