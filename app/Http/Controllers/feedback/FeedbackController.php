<?php

namespace App\Http\Controllers\feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FeedbackController extends Controller
{
    use GeneralResponse;

    /**
     * List all feedback
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $feedbacks = Feedback::orderBy('id', 'desc')
        ->limit(4)
        ->get();
        return $this->successReponseWithData(
            'Get all feedbacks successfully',
            $feedbacks
        );
    }

    /**
     * Create new feedback
     * @param \App\Http\Requests\FeedbackRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(FeedbackRequest $request)
    {
        try {
            $data = $request->validated();

            \App\Models\Feedback::create($data);

            return $this->successResponse(
                'Success create feedback'
            );
        } catch (QueryException $exception) {
            return $this->errorResponse(
                'Error with Database',
                500
            );
        } catch (ValidationException $exception) {
            return $this->errorResponse(
                'Validated Error',
                422
            );
        }
    }
}
