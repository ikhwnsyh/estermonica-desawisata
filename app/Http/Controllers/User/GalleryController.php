<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\GalleryModel;
use Illuminate\Http\Request;

class GalleryController extends Controller {
    public function get(Request $request) {
        $galleries = GalleryModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($galleries);
    }
}
