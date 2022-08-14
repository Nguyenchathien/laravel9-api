<?php

namespace App\Repositories\Eloquent;

use App\Models\Record;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\RecordRepositoryInterface;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordRepository extends BaseRepository implements RecordRepositoryInterface
{
/**
 * @var Model
 */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Record $model)
    {
        $this->model = $model;
    }

    public function Search(Request $request)
    {
        $param = $request->q;
        // DB::enableQueryLog();
        $record = DB::table('records')
            ->join('record_items', 'records.id', '=', 'record_items.record')
            ->orWhere(function ($query) use ($param) {
                $query->where('title', 'like', '%' . $param . '%')
                    ->where(['records.chg' => CHG_VALID_VALUE])
                    ->where(['user' => Auth::id()]);
            })
            ->orWhere(function ($query) use ($param) {
                $query->where('hospital', 'like', '%' . $param . '%')
                    ->where(['records.chg' => CHG_VALID_VALUE])
                    ->where(['user' => Auth::id()]);
            })
            ->orWhere(function ($query) use ($param) {
                $query->where('folder', 'like', '%' . $param . '%')
                    ->where(['records.chg' => CHG_VALID_VALUE])
                    ->where(['user' => Auth::id()]);
            })
            ->orWhere(function ($query) use ($param) {
                $query->where('people', 'like', '%' . $param . '%')
                    ->where(['records.chg' => CHG_VALID_VALUE])
                    ->where(['user' => Auth::id()]);
            })
            ->orWhere(function ($query) use ($param) {
                $query->where('media', 'like', '%' . $param . '%')
                    ->where(['records.chg' => CHG_VALID_VALUE])
                    ->where(['user' => Auth::id()]);
            })
            ->orWhere(function ($query) use ($param) {
                $query->where('record_items.content', 'like', '%' . $param . '%')
                    ->where(['records.chg' => CHG_VALID_VALUE])
                    ->where(['user' => Auth::id()]);
            })
            ->get()->chunk(50);
            // dd(\DB::getQueryLog());
        // dd($record);

        return $record;
    }


    public function searchRecords(Request $request)
    {
        $param = $request->q;
        // DB::enableQueryLog();
        $record = DB::table('records')
        ->leftjoin('orgs', 'records.hospital', '=', 'orgs.id')
        ->leftjoin('peoples', 'records.people', '=', 'peoples.id')
        ->leftjoin('folders', 'records.folder', '=', 'folders.id')
        ->leftjoin('record_items', 'records.id', '=', 'record_items.record')
        ->leftjoin('keywords', 'records.medicine', '=', 'keywords.id')
        ->leftjoin('medias', 'records.media', '=', 'medias.id')
        ->where('records.user', Auth::id())
        ->where('records.chg', CHG_VALID_VALUE)
        ->where(function ($query) use ($param) {
            $query->orwhere('records.title', 'like', '%' . $param . '%');
            $query->orwhere('peoples.name', 'like', '%' . $param . '%');
            $query->orwhere('orgs.name', 'like', '%' . $param . '%');
            $query->orwhere('folders.name', 'like', '%' . $param . '%');
            $query->orwhere('record_items.content', 'like', '%' . $param . '%');
        })
        ->select('records.*')
        ->distinct()
        ->get();
        // dd($record);
        // dd(\DB::getQueryLog());
        return $record;
    }

    public function getRecordVisible($id)
    {
        return Record::where(['id' => $id, 'visible' => VISIBLE_VALID_VALUE, 'chg' => CHG_VALID_VALUE])->get();
    }

    public function getImage($id)
    {
        return Record::where(['id' => $id, 'visible' => VISIBLE_VALID_VALUE, 'chg' => CHG_VALID_VALUE])->first()->media;
    }

    public function updateMedia($recordId, $mediaId)
    {
        return Record::where(['id' => $recordId, 'chg' => CHG_VALID_VALUE])->update(['media' => $mediaId]);
    }

    public function updateRecord($recordId, $mediaId, $endTime)
    {
        return Record::where(['id' => $recordId, 'chg' => CHG_VALID_VALUE])->update(['media' => $mediaId, 'end' => $endTime]);
    }

    public function getDetail($recordId)
    {
        return Record::where(['id' => $recordId, 'chg' => CHG_VALID_VALUE])->first();
    }

    public function getRecord($recordId)
    {
        return Record::where(['id' => $recordId])->first();
    }

    public function listRecords()
    {
        return Record::where(['user' => Auth::user()->id, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->paginate(10);
    }

    public function listRecordsByFolder($folderId)
    {
        return Record::where(['user' => Auth::user()->id, 'folder' => $folderId, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }

    public function listRecordsWithNoPaginate()
    {
        return Record::where(['user' => Auth::user()->id, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }
    public function searchRecordsByFolder(Request $request)
    {
        // DB::enableQueryLog();
        $keyword = $request->input('keyword');
        $id = $request->id;
        $record = DB::table('records')
        ->leftjoin('orgs', 'records.hospital', '=', 'orgs.id')
        ->leftjoin('peoples', 'records.people', '=', 'peoples.id')
        ->leftjoin('folders', 'records.folder', '=', 'folders.id')
        ->leftjoin('record_items', 'records.id', '=', 'record_items.record')
        ->leftjoin('keywords', 'records.medicine', '=', 'keywords.id')
        ->leftjoin('medias', 'records.media', '=', 'medias.id')
        ->where('records.folder', $id)
        ->where('records.chg', CHG_VALID_VALUE)
        ->where(function ($query) use ($keyword) {
            $query->orwhere( 'records.title' , 'like', '%'.$keyword. '%' );
            $query->orwhere( 'peoples.name' , 'like', '%'.$keyword. '%' );
            $query->orwhere( 'orgs.name', 'like', '%'.$keyword. '%' );
            $query->orwhere( 'folders.name', 'like', '%'.$keyword. '%' );
            $query->orwhere( 'record_items.content', 'like', '%'.$keyword. '%' );
        })
        ->select('records.*')
        ->distinct()
        ->get();
        // dd($record);
        // dd(\DB::getQueryLog());
        return $record;
    }

    public function searchRecordsByFamily(Request $request)
    {
        // DB::enableQueryLog();
        $keyword = $request->input('keyword');
        $family = $request->id;
        $record = DB::table('records')
        ->leftjoin('orgs', 'records.hospital', '=', 'orgs.id')
        ->leftjoin('peoples', 'records.people', '=', 'peoples.id')
        ->leftjoin('folders', 'records.folder', '=', 'folders.id')
        ->leftjoin('record_items', 'records.id', '=', 'record_items.record')
        ->leftjoin('keywords', 'records.medicine', '=', 'keywords.id')
        ->leftjoin('medias', 'records.media', '=', 'medias.id')
        ->leftjoin('shares', 'records.id', '=', 'shares.record')
        ->where('records.chg', CHG_VALID_VALUE)
        ->where('shares.user', $family)
        ->where('shares.to', Auth::id())
        ->where(function ($query) use ($keyword) {
            $query->orwhere('records.title', 'like', '%' . $keyword . '%');
            $query->orwhere('peoples.name', 'like', '%' . $keyword . '%');
            $query->orwhere('orgs.name', 'like', '%' . $keyword . '%');
            $query->orwhere('folders.name', 'like', '%' . $keyword . '%');
            $query->orwhere('record_items.content', 'like', '%' . $keyword . '%');
        })
        ->select('records.*')
        ->distinct()
        ->get();
        // dd(\DB::getQueryLog());
        // dd($record);
        return $record;
    }
    public function listRecordsByHospital($hospitalId)
    {
        return Record::where(['user' => Auth::user()->id, 'hospital' => $hospitalId, 'chg' => CHG_VALID_VALUE])
        ->orderBy('id', 'desc')->get();
    }

    public function listRecordsByDoctor($doctorId)
    {
        return Record::where(['user' => Auth::user()->id, 'people' => $doctorId, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }

    public function listRecordsByMedicine($medicineId)
    {
        return Record::where(['user' => Auth::user()->id, 'medicine' => $medicineId, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }

    public function listRecordsByFamily($familyId)
    {
        return Record::where(['user' => Auth::user()->id, 'people' => $familyId, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }


    public function listRecordsByKeyword($keywordId)
    {
        $record = DB::table('records')
        ->leftjoin('record_x_keywords', 'records.id', '=', 'record_x_keywords.record')
        ->where('records.chg', CHG_VALID_VALUE)
        ->where('record_x_keywords.chg', CHG_VALID_VALUE)
        ->where('records.user', Auth::id())
        ->where('record_x_keywords.keyword', $keywordId)
        ->select('records.*')
        ->distinct()
        ->orderBy('id', 'desc')
        ->get();
        return $record;
    }
}
