<?php

namespace App\Http\Controllers\advertisement;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvertisementRequest;
use App\Models\Advertisement;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{

    use GeneralResponse;
    /**
     * Display the listing of advertisement by take limit = 4
     * @return void
     */
    public function index()
    {

        $advertisements = Advertisement::orderBy('order', 'desc')
            ->limit(4)
            ->get();

        return $this->successReponseWithData(
            'Get all advertisement successfully',
            $advertisements
        );
    }

    /**
     * Create a new advertisement
     * @param \App\Http\Requests\AdvertisementRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(AdvertisementRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $advertise = 'advertise';

                if (!File::exists(storage_path("app/public/" . $advertise))) {
                    File::makeDirectory(storage_path("app/public/" . $advertise), 0755, true);
                }

                $data['image'] = $request->file('image')->store($advertise, 'public');
            }

            Advertisement::create($data);

            return $this->successResponse(
                'Advertisement Created successfully'
            );
        } catch (QueryException $exeption) {
            return $this->errorResponse(
                'Database error',
                500
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(
                'Internal server error',
                500
            );
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Handle validation exception
            return $this->errorResponse($validationException->errors(), 422);
        }
    }


    /**
     * Get an advertisement
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $advertise = Advertisement::findOrFail($id);

        return $this->successReponseWithData(
            'Advertise found successfully',
            $advertise
        );
    }

    /**
     * Edit an advertisement
     * @param \App\Http\Requests\AdvertisementRequest $request
     * @param mixed $id
     * @return mixed
     */
    public function update(AdvertisementRequest $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($advertisement->image) {
                Storage::delete("public/{$advertisement->image}");
            }

            $advertiseFolder = public_path('advertise');  

            if (!file_exists($advertiseFolder)) {
                mkdir($advertiseFolder, 0777, true); 
            }

            $imagePath = $request->file('image')->move($advertiseFolder, $request->file('image')->getClientOriginalName());

            $data['image'] = 'advertise/' . basename($imagePath);

            $advertisement->update($data);

            return $this->successResponse('Advertisement Updated Successfully');
        }

        // Optional: If no image is uploaded, just update the other fields
        $advertisement->update($data);

        return $this->successResponse('Advertisement Updated Successfully');
    }


    public function destroy($id)
    {
        $advertisement = Advertisement::findOrFail($id);

        if ($advertisement->image) {
            Storage::delete("public/{$advertisement->image}");
        }
        $advertisement->delete();

        return $this->successResponse(
            'Delete advertisement success'
        );
    }
}
