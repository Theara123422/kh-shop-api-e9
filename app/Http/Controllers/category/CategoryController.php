<?php

namespace App\Http\Controllers\category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    use GeneralResponse;

    /**
     * List all category
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = \App\Models\Category::all();

        return $this->successReponseWithData(
            'Get Categories Success',
            $categories
        );
    }

    /**
     * Create new category
     * @param \App\Http\Requests\CategoryRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();

            \App\Models\Category::create($data);

            return $this->successResponse(
                'Success create category'
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
        $category = \App\Models\Category::findOrFail($id);

        return $this->successReponseWithData(
            'Get Category success',
            $category
        );
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = \App\Models\Category::findOrFail($id);

            $data = $request->validated();

            $category->update($data);

            return $this->successResponse(
                'Updated Category success'
            );
        } catch (ValidationException $exeption) {
            return $this->errorResponse(
                'Validated Error',
                422
            );
        }
    }

    public function destroy($id){
        $category = \App\Models\Category::findOrFail($id);

        $category->delete();

        return $this->successResponse(
            'Deleted Category Success'
        );
    }
}
