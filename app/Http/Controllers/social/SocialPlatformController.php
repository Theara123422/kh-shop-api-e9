<?php

namespace App\Http\Controllers\social;

use App\Http\Controllers\Controller;
use App\Models\SocialPlatform;
use App\Traits\GeneralResponse;


class SocialPlatformController extends Controller
{

    use GeneralResponse;
    /**
     * Display the listing of advertisement by take limit = 4
     * @return void
     * @group Home Page - Advertisement
     */

    public function index()
    {

        $social = SocialPlatform ::orderBy('id', 'desc')
            ->limit(4)
            ->get();

        return $this->successReponseWithData(
            'Get all social platforms successfully',
            $social
        );
    }

    /**
     * Get an social platform
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $social = SocialPlatform::findOrFail($id);

        return $this->successReponseWithData(
            'Social platform found successfully',
            $social
        );
    }
}
