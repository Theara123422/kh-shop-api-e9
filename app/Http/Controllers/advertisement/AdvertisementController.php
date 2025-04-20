<?php

namespace App\Http\Controllers\advertisement;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AdvertisementController extends Controller
{

    use GeneralResponse;
    /**
     * Display the listing of advertisement by take limit = 4
     * @return void
     * @group Home Page - Advertisement
     */

    public function index()
    {

        $advertisements = Advertisement::orderBy('id', 'desc')
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
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            // Handle itemImageSrc upload
            if ($request->hasFile('itemImageSrc')) {
                $file = $request->file('itemImageSrc');
                $fileName = time() . '-' . $file->getClientOriginalName(); // Get the original name of the file
                $file->storeAs('advertise', $fileName, 'public'); // Save the file in 'advertise' folder
                $data['itemImageSrc'] = 'http://localhost:8000/storage/advertise/'.$fileName;
            }

            // Handle thumbnailImageSrc upload
            if ($request->hasFile('thumbnailImageSrc')) {
                $file = $request->file('thumbnailImageSrc');
                $fileName = time() . '-' . $file->getClientOriginalName(); // Get the original name of the file
                $file->storeAs('advertise', $fileName, 'public'); // Save the file in 'advertise' folder
                $data['thumbnailImageSrc'] = 'http://localhost:8000/storage/advertise/'.$fileName;
            }

            Advertisement::create($data);

            return $this->successResponse('Advertisement created successfully');
        } catch (QueryException $exception) {
            return $this->errorResponse('Database error', 500);
        } catch (\Exception $exception) {
            return $this->errorResponse('Internal server error', 500);
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
    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $data = $request->all();

        // Handle itemImageSrc upload
        if ($request->hasFile('itemImageSrc')) {
            if ($advertisement->itemImageSrc) {
                Storage::disk('public')->delete("advertise/{$advertisement->itemImageSrc}");
            }

            $file = $request->file('itemImageSrc');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('advertise', $fileName, 'public');
            $data['itemImageSrc'] = $fileName;
        }

        // Handle thumbnailImageSrc upload
        if ($request->hasFile('thumbnailImageSrc')) {
            if ($advertisement->thumbnailImageSrc) {
                Storage::disk('public')->delete("advertise/{$advertisement->thumbnailImageSrc}");
            }

            $file = $request->file('thumbnailImageSrc');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('advertise', $fileName, 'public');
            $data['thumbnailImageSrc'] = $fileName;
        }

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
