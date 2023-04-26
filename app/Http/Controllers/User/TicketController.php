<?php

namespace App\Http\Controllers\User;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketBundleModel;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use App\Models\TransactionTicketBundleModel;
use App\Models\TransactionTicketModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        return DB::transaction(function () use ($request) {
            $ticketBundle = TicketBundleModel::with("tickets")->find($request->id);
            $summaryAdult = 0;
            $summaryChild = 0;
            foreach ($ticketBundle->tickets as $ticket) {
                if (!empty($request->total_adult)) $summaryAdult += $request->total_adult * $ticket->adult_price;
                if (!empty($request->total_child)) $summaryChild += $request->total_child * $ticket->child_price;
            }
            $grossAmount = $summaryAdult + $summaryChild;

            $now = Carbon::now();
            $invoiceNumber = $now->format("Y-") . Str::random(4) . $now->format("-m-") . Str::random(4) . $now->format("-d-") . Str::random(12);

            Config::$serverKey = env("MIDTRANS_SERVER_KEY");
            Config::$isProduction = env("MIDTRANS_PRODUCTION");
            Config::$isSanitized = true;
            Config::$is3ds = true;
            Config::$overrideNotifUrl = env("MIDTRANS_OVERRIDE_NOTIFICATION_URL");

            $snapUrl = Snap::getSnapUrl([
                "transaction_details" => [
                    "order_id" => $invoiceNumber,
                    "gross_amount" => $grossAmount
                ]
            ]);

            $transaction = TransactionModel::create([
                "invoice_number" => $invoiceNumber,
                "user_id" => auth()->id(),
                "gross_amount" => $grossAmount,
                "total_adult" => $request->total_adult,
                "total_child" => $request->total_child,
                "snap_url" => $snapUrl
            ]);
            TransactionHistoryModel::create([
                "transaction_id" => $transaction->id,
                "status" => MidtransStatusConstant::PENDING
            ]);
            $transactionTicketBundle = TransactionTicketBundleModel::create([
                "transaction_id" => $transaction->id,
                "name" => $ticketBundle->name
            ]);
            $ticketIds = [];
            foreach ($ticketBundle->tickets as $ticket) {
                $newTicket = TransactionTicketModel::create([
                    "name" => $ticket->name,
                    "adult_price" => $ticket->adult_price,
                    "child_price" => $ticket->child_price
                ]);
                array_push($ticketIds, $newTicket->id);
            }
            $transactionTicketBundle->tickets()->sync($ticketIds);

            return ResponseHelper::response($snapUrl);
        });
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
