<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Http\Requests\V1\FavoriteRequest;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;

class FavoriteController extends BaseController
{
    /**
     * @var FavoriteRepositoryInterface
     */
    private $favoriteRepository;

    /**
     * FavoriteController constructor.
     * @param FavoriteRepositoryInterface $favoriteRepository
     */
    public function __construct(FavoriteRepositoryInterface $favoriteRepository) 
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * @param null
     */
    public function index() 
    {
        try {
            $favorites = $this->favoriteRepository->allBy([
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE
            ]);

            return $this->sendResponse($favorites, 'Get favorite list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }


    /**
     *  @param FavoriteRequest $request
     */
    public function store(FavoriteRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['record'] = $request->record;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $favorite = $this->favoriteRepository->create($input);

            if($favorite) {
                return $this->sendResponse($favorite, 'Create favorite successfully.');
            }

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }


        /**
     *  @param FavoriteRequest $request
     */
    public function clickLiked($recordId)
    {
        try {
            $input['record'] = $recordId;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $favorite = $this->favoriteRepository->create($input);

            if($favorite) {
                return $this->sendResponse($favorite, 'Create favorite successfully.');
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
            $favorite = $this->favoriteRepository->findBy(['record' => $request->id, 'chg' => CHG_VALID_VALUE]);
            if (!isset($favorite)) {
                return $this->sendError("Record favorite not found!", 404);
            }
            $favoriteId = $favorite->id;
            if(!$favoriteId) {
                return $this->sendError("Record favorite not found with ID : $favoriteId!", 404);
            }
            $this->favoriteRepository->deleteById($favoriteId);
            return $this->sendResponse([], 'Delete favorite successfully.');

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $request
     */
    public function liked(Request $request)
    {
        try {
            $favorite = $this->favoriteRepository->findBy(['record' => $request->id]);
            if (!$favorite) {
                $fav = app('App\Http\Controllers\V1\FavoriteController')->clickLiked($request->id);
                return $this->sendResponse($fav, 'Liked successfully.');
            }
            $favoriteId = $favorite->id;
            $like = $this->favoriteRepository->findLike($favoriteId);
            if (isset($like)) {
                if ($like->chg == CHG_DELETE_VALUE) {
                    $this->favoriteRepository->updateLike($favoriteId);
                    return $this->sendResponse([], 'Liked successfully.');

                } else {
                    if ($like->chg == CHG_VALID_VALUE) {
                        $this->favoriteRepository->deleteById($favoriteId);
                    return $this->sendResponse([], 'Unliked successfully.');
                    }  
                }
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
