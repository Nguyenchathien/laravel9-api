<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\FamilyRepositoryInterface;
use App\Http\Requests\V1\ScheduleRequest;
use App\Http\Requests\V1\ScheduleUpdateRequest;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;
// use DateTime;

class ScheduleController extends BaseController
{
    /**
     * @var ScheduleRepositoryInterface
     */
    protected $scheduleRepository;
    protected $hospitalRepository;
    protected $doctorRepository;
    protected $userRepository;
    protected $familyRepository;
    /**
     * ScheduleController constructor.
     * @param ScheduleRepository $scheduleRepository
     */
    public function __construct(
        ScheduleRepositoryInterface $scheduleRepository,
        HospitalRepositoryInterface $hospitalRepository,
        DoctorRepositoryInterface $doctorRepository,
        UserRepositoryInterface $userRepository,
        FamilyRepositoryInterface $familyRepository
        )
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->doctorRepository = $doctorRepository;
        $this->userRepository = $userRepository;
        $this->familyRepository = $familyRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $schedules = $this->scheduleRepository->allBy([
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE
            ], ['*'], ['hospital', 'people']);
            if (!$schedules) {
                return $this->sendResponse($schedules, 'No data.');
            }
            $schedules->makeHidden(['chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'type']);
            return $this->sendResponse($schedules, 'Get schedule list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
    /**
     * @param Request $request
     */
    public function getSchedule(Request $request)
    {
        try {
            $time = strtotime($request->date);
            $date= date('Y-m-d', $time);
            $userId = Auth::user()->id;
            $schedules = $this->scheduleRepository->getSchedule($date, $userId);
            foreach ($schedules as $key => $value) {
                $hospitalID = $value->hospital;
                if (isset($hospitalID)) {
                    $value->hospital = $this->hospitalRepository->findById($hospitalID)->name ?? null;
                }
                $peopleID = $value->people;
                if (isset($peopleID)) {
                    $value->people = $this->doctorRepository->findById($peopleID)->name ?? null;
                }
                $value->makeHidden(['chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            }
            if ($schedules) {
                return $this->sendResponse($schedules, 'Get schedule on day successfully.');
            }
            return $this->sendError("Not found!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }


    /**
     * @param Request $request
     */
    public function getScheduleDetail(Request $request)
    {
        try {
            $schedule = $this->scheduleRepository->findById($request->id);
            $hospitalID = $schedule->hospital;
            if (isset($hospitalID)) {
                $schedule->hospital = $this->hospitalRepository->findById($hospitalID)->name ?? '';
                $schedule->hospitalId = $hospitalID ?? '';
            }
            $peopleID = $schedule->people;
            if (isset($peopleID)) {
                $people = $this->doctorRepository->findById($peopleID);
                $schedule->people = $this->doctorRepository->findById($peopleID)->name ?? '';
                $schedule->peopleId = $peopleID ?? '';

            }
            $schedule->makeHidden(['chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            if ($schedule) {
                return $this->sendResponse($schedule, 'Get schedule detail successfully.');
            }
            return $this->sendError("Not found!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param ScheduleRequest $request
     */
    public function store(ScheduleRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['type'] = SCHEDULE_KEY_VALUE;
            $input['date'] = Carbon::createFromFormat('Y-m-d H:i', $request->date);
            $input['color'] = $request->input('color') ? $request->input('color') : COLOR_DEFAULT_VALUE;
            $input['hospital'] = $request->input('hospital') ? (int) $request->input('hospital') : null;
            $input['people'] = (int) $request->input('people');
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $schedule = $this->scheduleRepository->create($input);
            if ($schedule) {
                return $this->sendResponse($schedule, 'Create schedule successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param ScheduleRequest $request
     */
    public function update(ScheduleRequest $request)
    {
        try {
            $schedule = $this->scheduleRepository->findById($request->id);
            if (!$schedule) {
                return $this->sendError("Schedule not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $schedule = $this->scheduleRepository->update($request->id, $input);
            if ($schedule) {
                return $this->sendResponse($schedule, 'Update schedule successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param ScheduleUpdateRequest $request
     */
    public function updateSchedule(ScheduleUpdateRequest $request)
    {
        try {
            $schedule = $this->scheduleRepository->findById($request->id);
            if (!$schedule) {
                return $this->sendError("Schedule not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();

            if (!empty($request->title)) {
                $input['title'] = $request->title;
            } else{
                $input['title'] = $schedule->title;
            }
            if (!empty($request->date)) {
                $input['date'] = $request->date;
            } else{
                $input['date'] = $schedule->date;
            }
            if (!empty($request->hospital)) {
                $input['hospital'] = $request->hospital;
            } else{
                $input['hospital'] = null;
            }
            if (!empty($request->people)) {
                $input['people'] = $request->people;
            } else{
                $input['people'] = $schedule->people;
            }
            if (!empty($request->remark)) {
                $input['remark'] = $request->remark;
            } else{
                $input['remark'] = $schedule->remark;
            }
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $schedule = $this->scheduleRepository->update($request->id, $input);
            if ($schedule) {
                return $this->sendResponse($schedule, 'Update schedule successfully.');
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
            $schedule = $this->scheduleRepository->findById($request->id);
            if (!$schedule) {
                return $this->sendError("Schedule not found with ID : $request->id!", 404);
            }
            $this->scheduleRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete schedule successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

      /**
     * @param null
     */
    public function listScheduleFamily(Request $request)
    {
        try {
            $user = $this->userRepository->findById($request->id);
            if (!$user) {
                return $this->sendError("User not found!", 404);
            }
            $email = $user->email;
            $family = $this->familyRepository->findFamilyValid(Auth::id(), $email);
            if (!$family) {
                return $this->sendError("This people is not in family!", 404);
            }
            $schedules = $this->scheduleRepository->allBy([
                'user' => $request->id,
                'chg' => CHG_VALID_VALUE
            ], ['*'], ['hospital', 'people']);

            return $this->sendResponse($schedules, 'Get schedule list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
