<?php

namespace App\Http\Controllers\Size;

use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Traits\GeneralResponse;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;


class sizeController extends Controller
{
    use GeneralResponse;
     /**
     * List all category
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $color = \App\Models\Size::all();

        return $this->successReponseWithData(
            'Get sice Success',
            $color
        );
    }
    /**
     * Create new category
     * @param \App\Http\Requests\CategoryRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(SizeRequest $request)
    {
        try {
            $data = $request->validated();

            \App\Models\Size::create($data);

            return $this->successResponse(
                'Success create size'
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
        $color = \App\Models\Size::findOrFail($id);

        return $this->successReponseWithData(
            'Get size success',
            $color
        );
    }
    
    public function update(SizeRequest $request, $id)
    {
        try {
            $color = \App\Models\Size::findOrFail($id);

            $data = $request->validated();

            $color->update($data);

            return $this->successResponse(
                'Updated size success'
            );
        } catch (ValidationException $exeption) {
            return $this->errorResponse(
                'Validated Error',
                422
            );
        }
    }

    public function destroy($id){
        $color = \App\Models\Size::findOrFail($id);

        $color->delete();

        return $this->successResponse(
            'Deleted size Success'
        );
    }



}
