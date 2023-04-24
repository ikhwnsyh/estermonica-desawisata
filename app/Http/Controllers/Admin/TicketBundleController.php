<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketBundleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketBundleController extends Controller {
    protected $ticketBundleTable;

    public function __construct() {
        $this->ticketBundleTable = (new TicketBundleModel())->getTable();
    }

    public function get(Request $request) {
        $ticketBundles = TicketBundleModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($ticketBundles);
    }

    public function getList(Request $request) {
        $ticketBundles = TicketBundleModel::orderByDesc("id")->get();

        return ResponseHelper::response($ticketBundles);
    }

    public function set(Request $request, TicketBundleModel $ticketBundle) {
        $ticketBundle->name = $request->name;
        $ticketBundle->save();

        return ResponseHelper::response($ticketBundle);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new TicketBundleModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketBundleTable,id",
            "name" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, TicketBundleModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketBundleTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($id) {
            $ticketBundle = TicketBundleModel::find($id);
            if (!empty($ticketBundle->id)) {
                $ticketBundle->tickets()->detach();
                $ticketBundle->delete();
            }

            return ResponseHelper::response();
        });
    }
}
