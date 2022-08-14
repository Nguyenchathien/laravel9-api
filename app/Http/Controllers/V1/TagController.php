<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Http\Requests\V1\TagRequest;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;

class TagController extends BaseController
{
    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * TagController constructor.
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepositoryInterface $tagRepository) 
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param null
     */
    public function index() 
    {
        try {
            $tags = $this->tagRepository->allBy([
                    'user' => Auth::user()->id,
                    'chg' => CHG_VALID_VALUE
            ]);

            return $this->sendResponse($tags, 'Get tag list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }   
    }

    /**
     * @param Request $request
     */
    public function getTagDetail($id) 
    {
        try {
            $tag = $this->tagRepository->findById($id);

            if($tag) {
                return $this->sendResponse($tag, 'Get tag detail successfully.');
            }
            return $this->sendError("Not found!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param TagRequest $request
     */
    public function store(TagRequest $request)
    {
        try {
            $request->validated();

            $input = $request->all();
            $input['type'] = KEY_WORD_KEY_VALUE ;
            $input['color'] = $request->input('color') ? $request->input('color') : COLOR_DEFAULT_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $tag = $this->tagRepository->create($input);

            if($tag) {
                return $this->sendResponse($tag, 'Create tag successfully.');
            }

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param TagRequest $request
     */
    public function update(TagRequest $request)
    {
        try {
            $tag = $this->tagRepository->findById($request->id);

            if(!$tag) {
                return $this->sendError("Tag not found with ID : $request->id!", 404);
            }
            $request->validated();

            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $tag = $this->tagRepository->update($request->id, $input);

            if($tag) {
                return $this->sendResponse($tag, 'Update tag successfully.');
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
            $tag = $this->tagRepository->findById($request->id);

            if(!$tag) {
                return $this->sendError("Tag not found with ID : $request->id!", 404);
            }

            $this->tagRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete tag successfully.');

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
