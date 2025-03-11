<?php

namespace App\Http\Controllers\color;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Traits\GeneralResponse;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\QueryException;

/**
 * @group Color
 */
class ColorController extends Controller
{
    use GeneralResponse;
     /**
     * List all category
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $color = \App\Models\Color::all();

        return $this->successReponseWithData(
            'Get color Success',
            $color
        );
    }

     /**
     * Create new category
     * @param \App\Http\Requests\CategoryRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(ColorRequest $request)
    {
        try {
            $data = $request->validated();

            \App\Models\Color::create($data);

            return $this->successResponse(
                'Success create color'
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
     * Get Specific category
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $color = \App\Models\Color::findOrFail($id);

        return $this->successReponseWithData(
            'Get color success',
            $color
        );
    }
    public function update(ColorRequest $request, $id)
    {
        try {
            $color = \App\Models\Color::findOrFail($id);

            $data = $request->validated();

            $color->update($data);

            return $this->successResponse(
                'Updated color success'
            );
        } catch (ValidationException $exeption) {
            return $this->errorResponse(
                'Validated Error',
                422
            );
        }
    }

    public function destroy($id){
        $color = \App\Models\Color::findOrFail($id);

        $color->delete();

        return $this->successResponse(
            'Deleted color Success'
        );
    }




}
