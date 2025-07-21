<?php

namespace Modules\Ecommerce\Http\Controllers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Http\Requests\QuestionCreateRequest;
use Modules\Ecommerce\Http\Requests\QuestionUpdateRequest;
use Modules\Ecommerce\Models\Question;
use Modules\Ecommerce\Repositories\QuestionRepository;
use Modules\Settings\Models\Settings;
use Symfony\Component\HttpKernel\Exception\HttpException;

class QuestionController extends CoreController
{
    public $repository;

    public function __construct(QuestionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection|Question[]
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 15;
        $productId = $request['product_id'];

        if (isset($productId) && ! empty($productId)) {
            if ($request->user() !== null) {
                $request->user()->id;
            }

            return $this->repository->where([
                ['product_id', '=', $productId],
                ['answer', '!=', null],
            ])->paginate($limit);
        }
        if (isset($request['answer']) && $request['answer'] === 'null') {
            return $this->repository->paginate($limit);
        }

        return $this->repository->where('answer', '!=', null)->paginate($limit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function store(QuestionCreateRequest $request): Question
    {
        try {
            $productQuestionCount = $this->repository->where([
                'product_id' => $request['product_id'],
                'user_id' => $request->user()->id,
                'shop_id' => $request['shop_id'],
            ])->count();

            $settings = Settings::getData();
            $maximumQuestionLimit = isset($settings['options']['maximumQuestionLimit']) ? $settings['options']['maximumQuestionLimit'] : 5;

            if ($maximumQuestionLimit <= $productQuestionCount) {
                throw new HttpException(400, MAXIMUM_QUESTION_LIMIT_EXCEEDED);
            }

            return $this->repository->storeQuestion($request);
        } catch (DurrbarException $e) {
            throw new DurrbarException(MAXIMUM_QUESTION_LIMIT_EXCEEDED);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->repository->findOrFail($id);
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    public function update(QuestionUpdateRequest $request, $id)
    {
        $request->id = $id;

        return $this->updateQuestion($request, $id);
    }

    public function updateQuestion(Request $request)
    {
        try {
            if ($this->repository->hasPermission($request->user(), $request->shop_id)) {
                $id = $request->id;

                return $this->repository->updateQuestion($request, $id);
            }
            throw new AuthorizationException(NOT_AUTHORIZED);
        } catch (DurrbarException $th) {
            throw new DurrbarException(COULD_NOT_UPDATE_THE_RESOURCE);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            return $this->repository->findOrFail($id)->delete();
        } catch (DurrbarException $e) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Display a listing of the resource for authenticated user.
     *
     * @return JsonResponse
     */
    public function myQuestions(Request $request)
    {
        $limit = $request->limit ? $request->limit : 15;

        return $this->repository->where('user_id', auth()->user()->id)->with('product')->paginate($limit);
    }
}
