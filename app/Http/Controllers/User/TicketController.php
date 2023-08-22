<?php

namespace App\Http\Controllers\User;

use App\Constants\MidtransStatusConstant;
use App\Constants\TicketConstant;
use App\Constants\TransactionTypeConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketModel;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class TicketController extends Controller
{
    protected $ticketTable;

    public function __construct()
    {
        $this->ticketTable = (new TicketModel())->getTable();
    }

    public function get(Request $request)
    {
        $tickets = TicketModel::with('imageTicket')->orderByDesc("id")->paginate();
        return ResponseHelper::response($tickets);
    }

    public function getBundle(Request $request)
    {
        $tickets = TicketModel::with('imageTicket')->where("type", TicketConstant::BUNDLE)->orderByDesc("id")->paginate();
        return ResponseHelper::response($tickets);
    }

    public function getNonBundle(Request $request)
    {
        $tickets = TicketModel::with('imageTicket')->where("type", TicketConstant::SINGLE)->orderByDesc("id")->paginate();
        return ResponseHelper::response($tickets);
    }

    public function getDetail(Request $request, $id)
    {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $ticket = TicketModel::find($id);

        return ResponseHelper::response($ticket);
    }

    public function buy(Request $request)
    {
        $ticket = TicketModel::find($request->id);
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketTable,id",
            "total_adult" => "required|numeric|min:{$ticket->minimum_adult}",
            "total_child" => "required|numeric|min:{$ticket->minimum_child}",
            "date" => "nullable|date"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $ticket = TicketModel::find($request->id);
            $summaryAdult = 0;
            $summaryChild = 0;
            if (!empty($request->total_adult)) $summaryAdult += $request->total_adult * $ticket->adult_price;
            if (!empty($request->total_child)) $summaryChild += $request->total_child * $ticket->child_price;
            $grossAmount = $summaryAdult + $summaryChild;
            $totalTiket = $request->total_adult + $request->total_child;
            $now = Carbon::now("Asia/Jakarta");
            $invoiceNumber = $now->format("Y-") . Str::random(4) . $now->format("-m-") . Str::random(4) . $now->format("-d-") . Str::random(12);

            Config::$serverKey = env("MIDTRANS_SERVER_KEY");
            Config::$isProduction = env("MIDTRANS_PRODUCTION");
            Config::$isSanitized = true;
            Config::$is3ds = true;
            Config::$overrideNotifUrl = env("MIDTRANS_OVERRIDE_NOTIFICATION_URL");

            $snapUrl = null;
            if (!empty("date")) {
                $snapUrl = Snap::getSnapUrl([
                    "transaction_details" => [
                        "order_id" => $invoiceNumber,
                        "gross_amount" => $grossAmount
                    ]
                ]);
            }

            $transaction = TransactionModel::create([
                "invoice_number" => $invoiceNumber,
                "ticket_id" => $ticket->id,
                "user_id" => auth()->id(),
                "gross_amount" => $grossAmount,
                "total_adult" => $request->total_adult,
                "total_child" => $request->total_child,
                "snap_url" => $snapUrl,
                "date" => empty($request->date) ? $now : $request->date
            ]);
            if ($transaction) {
                TicketModel::find($ticket)->update([
                    'stock' => $ticket->stock - $totalTiket,
                ]);
            }
            TransactionHistoryModel::create([
                "transaction_id" => $transaction->id,
                "status" => $request->has("date") ? MidtransStatusConstant::BOOKING : MidtransStatusConstant::PENDING
            ]);

            return ResponseHelper::response($snapUrl);
        });
    }
}
