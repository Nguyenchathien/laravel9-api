<?php

namespace App\Repositories\Interfaces;

use App\Repositories\EloquentRepositoryInterface;

interface RecordKeywordRepositoryInterface extends EloquentRepositoryInterface 
{
    public function getRecordKeyWords($recordId);
    public function chgValid($recordKeywordId);
    public function chgInvalid($recordKeywordId);
    public function listRecordsByKeyWord($keywordId);
    public function updateRecordKeyword($record, $keyword, $newKey);

}