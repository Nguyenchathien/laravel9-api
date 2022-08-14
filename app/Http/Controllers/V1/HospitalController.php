<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Http\Requests\V1\HospitalRequest;
use App\Http\Requests\V1\HospitalUpdateRequest;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use DB;

class HospitalController extends BaseController
{
    /**
     * @var HospitalRepositoryInterface
     */
    private $hospitalRepository;

    /**
     * HospitalController constructor.
     * @param HospitalRepositoryInterface $hospitalRepository
     */
    public function __construct(HospitalRepositoryInterface $hospitalRepository) 
    {
        $this->hospitalRepository = $hospitalRepository;
    }


    public function store(HospitalRequest $request)
    {
        try {
            $request->validated();

            $input = $request->all();
            $input['type'] = HOSPITAL_OR_DOCTOR_KEY_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $hospital = $this->hospitalRepository->create($input);

            if($hospital) {
                return $this->sendResponse($hospital, 'Create hospital successfully.');
            }

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param null
     */
    public function index() 
    {
        try {
            $hospitals = $this->hospitalRepository->allBy([
                'type' => HOSPITAL_OR_DOCTOR_KEY_VALUE, 
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE
            ]);

            return $this->sendResponse($hospitals, 'Get hospital list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function detail($id) 
    {
        try {
            $hospital = $this->hospitalRepository->findById($id);

            if($hospital) {
                return $this->sendResponse($hospital, 'Get hospital detail successfully.');
            }

            return $this->sendError("Hospital not found with ID : $id!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param HospitalRequest $request
     */
    public function update(HospitalRequest $request)
    {
        try {
            $hospital = $this->hospitalRepository->findById($request->id);

            if(!$hospital) {
                return $this->sendError("Hospital not found with ID : $request->id!", 404);
            }
            $request->validated();

            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $hospital = $this->hospitalRepository->update($request->id, $input);

            if($hospital) {
                return $this->sendResponse($hospital, 'Update hospital successfully.');
            }

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $request
     */
    public function delete(Request $request)
    {
        try {
            $hospital = $this->hospitalRepository->findById($request->id);

            if(!$hospital) {
                return $this->sendError("Hospital not found with ID : $request->id!", 404);
            }

            $this->hospitalRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete hospital successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
    public function updateHospital(HospitalUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $hospital = $this->hospitalRepository->findBy(['id' => $request->id, 'type' => HOSPITAL_OR_DOCTOR_KEY_VALUE]);
            if (!$hospital) {
                return $this->sendError("Hospital not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $hospital = $this->hospitalRepository->update($request->id, $input);
            DB::commit();
            return $this->sendResponse($hospital, 'Update hospital successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
