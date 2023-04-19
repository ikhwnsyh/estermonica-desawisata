<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\GalleryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryController extends Controller {
    protected $galleryTable;

    public function __construct() {
        $this->galleryTable = (new GalleryModel())->getTable();
    }

    public function get(Request $request) {
        $galleries = GalleryModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($galleries);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "image" => "required|file|image"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $image = Carbon::now()->format("Y-m-d-H-i") . "-galleries-" . Str::random(12) . "." . $request->file("image")->getClientOriginalExtension();
        Storage::disk("public")->putFileAs("galleries", $request->file("image"), $image);

        $gallery = GalleryModel::create([
            "image" => $image
        ]);

        return ResponseHelper::response($gallery);
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->galleryTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        GalleryModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
