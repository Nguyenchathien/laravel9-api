<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Http\Requests\V1\DoctorRequest;
use App\Http\Requests\V1\DoctorUpdateRequest;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use DB;

class DoctorController extends BaseController
{
    /**
     * @var DoctorRepositoryInterface
     */
    protected $doctorRepository;

    /**
     * DoctorController constructor.
     * @param DoctorRepositoryInterface $doctorRepository
     */
    public function __construct(
        DoctorRepositoryInterface $doctorRepository,
        HospitalRepositoryInterface $hospitalRepository
        )
    {
        $this->doctorRepository = $doctorRepository;
        $this->hospitalRepository = $hospitalRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $doctors = $this->doctorRepository->allBy([
                'type' => HOSPITAL_OR_DOCTOR_KEY_VALUE,
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE
            ]);
            if (!$doctors) {
                return $this->sendError("No data!", 404);
            }
            foreach ($doctors as $key => $doctor) {
                $orgId = $doctor->org;
                if ($orgId) {
                    $hospital = $this->hospitalRepository->findById($orgId);
                    $doctor->orgName = $hospital->name ?? null;
                } else {
                    $doctor->orgName = null;
                }
            }
            return $this->sendResponse($doctors, 'Get doctor list successfully.');
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
            $doctor = $this->doctorRepository->findById($id);

            if($doctor) {
                return $this->sendResponse($doctor, 'Get doctor detail successfully.');
            }

            return $this->sendError("Hospital not found with ID : $id!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param DoctorRequest $request
     */
    public function store(DoctorRequest $request)
    {
        try {
            $request->validated();

            $input = $request->all();
            $input['type'] = HOSPITAL_OR_DOCTOR_KEY_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $doctor = $this->doctorRepository->create($input);

            if($doctor) {
                return $this->sendResponse($doctor, 'Create doctor successfully.');
            }

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param DoctorRequest $request
     */
    public function update(DoctorRequest $request)
    {
        try {
            $doctor = $this->doctorRepository->findById($request->id);

            if(!$doctor) {
                return $this->sendError("Doctor not found with ID : $request->id!", 404);
            }
            $request->validated();

            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $doctor = $this->doctorRepository->update($request->id, $input);

            if($doctor) {
                return $this->sendResponse($doctor, 'Update doctor successfully.');
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
            $doctor = $this->doctorRepository->findById($request->id);

            if(!$doctor) {
                return $this->sendError("Doctor not found with ID : $request->id!", 404);
            }

            $this->doctorRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete Doctor successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function updateDoctor(DoctorUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $doctor = $this->doctorRepository->findBy(['id' => $request->id, 'type' => HOSPITAL_OR_DOCTOR_KEY_VALUE]);
            if (!$doctor) {
                return $this->sendError("Doctor not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $input['org'] = $request->org ?? null;
            $doctor = $this->doctorRepository->update($request->id, $input);
            DB::commit();
            return $this->sendResponse($doctor, 'Update doctor successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

}
