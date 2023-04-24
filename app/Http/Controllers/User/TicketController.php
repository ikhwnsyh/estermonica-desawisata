<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketBundleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class TicketController extends Controller {
    protected $ticketBundleTable;

    public function __construct() {
        $this->ticketBundleTable = (new TicketBundleModel())->getTable();
    }

    public function get(Request $request) {
        $ticketBundles = TicketBundleModel::with("tickets")
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($ticketBundles);
    }

    public function getDetail(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketBundleTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $ticketBundles = TicketBundleModel::with("tickets")->find($id);

        return ResponseHelper::response($ticketBundles);
    }

    public function buy(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketBundleTable,id",
            "total_adult" => "required|numeric",
            "total_child" => "required|numeric"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $ticketBundles = TicketBundleModel::with("tickets")->find($request->id);
        $summaryAdult = 0;
        $summaryChild = 0;
        foreach ($ticketBundles->tickets as $ticket) {
            if (!empty($request->total_adult)) $summaryAdult += $request->total_adult * $ticket->adult_price;
            if (!empty($request->total_child)) $summaryChild += $request->total_child * $ticket->child_price;
        }

        Config::$serverKey = env("MIDTRANS_SERVER_KEY");
        Config::$isProduction = env("MIDTRANS_PRODUCTION");
        Config::$isSanitized = true;
        Config::$is3ds = true;
        Config::$overrideNotifUrl = env("MIDTRANS_OVERRIDE_NOTIFICATION_URL");

        return ResponseHelper::response(Snap::getSnapUrl([
            "transaction_details" => [
                "order_id" => Str::random(12),
                "gross_amount" => $summaryAdult + $summaryChild
            ]
        ]));
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketBundleTable,id",
            "total_adult" => "required|numeric",
            "total_child" => "required|numeric"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return ResponseHelper::response();
    }
}
