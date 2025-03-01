<?php

namespace App\Http\Controllers\feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
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
        $feedbacks = \App\Models\Feedback::all();

        return $this->successReponseWithData(
            'Get Feedbacks Success',
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


    /**
     * Get Specific feedback
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $feedback = \App\Models\Feedback::findOrFail($id);

        return $this->successReponseWithData(
            'Get Feedback Success',
            $feedback
        );
    }


    public function update(FeedbackRequest $request, $id)
    {
        try {
            $feedback = \App\Models\Feedback::findOrFail($id);

            $data = $request->validated();

            $feedback->update($data);

            return $this->successResponse(
                'Updated Feedback Success'
            );
        } catch (ValidationException $exeption) {
            return $this->errorResponse(
                'Validated Error',
                422
            );
        }
    }

    public function destroy($id){
        $feedback = \App\Models\Feedback::findOrFail($id);

        $feedback->delete();

        return $this->successResponse(
            'Deleted Feedback Success'
        );
    }
}
