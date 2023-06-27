<?php

namespace App\Console\Commands;

use App\Constants\MidtransStatusConstant;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class Booking extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Booking';

    /**
     * Execute the console command.
     */
    public function handle() {
        $transactionTable = (new TransactionModel())->getTable();
        $transactionHistoryTable = (new TransactionHistoryModel())->getTable();
        $detailIds = DB::table("$transactionHistoryTable as detail_mx")
            ->selectRaw("max(detail_mx.id) as detail_id, detail_mx.transaction_id")
            ->groupBy("detail_mx.transaction_id")
            ->toSql();
        $detailData = DB::table("$transactionHistoryTable as detail_data")
            ->selectRaw("detail_data.id, detail_data.status")
            ->toSql();
        $data = TransactionModel::with("latestHistory", "histories", "ticketBundle.tickets", "user")
            ->select("$transactionTable.*")
            ->leftJoinSub(
                $detailIds,
                "detail_max",
                "$transactionTable.id",
                "=",
                "detail_max.transaction_id"
            )
            ->leftJoinSub(
                $detailData,
                "detail",
                "detail.id",
                "=",
                "detail_max.detail_id"
            )
            ->whereRaw(implode(" and ", [
                "detail.status = " . MidtransStatusConstant::BOOKING,
                "$transactionTable.datetime <= now()"
            ]))
            ->orderByDesc("$transactionTable.id")
            ->get();
        foreach ($data as $tx) {
            Config::$serverKey = env("MIDTRANS_SERVER_KEY");
            Config::$isProduction = env("MIDTRANS_PRODUCTION");
            Config::$isSanitized = true;
            Config::$is3ds = true;
            Config::$overrideNotifUrl = env("MIDTRANS_OVERRIDE_NOTIFICATION_URL");

            $snapUrl = Snap::getSnapUrl([
                "transaction_details" => [
                    "order_id" => $tx->invoice_number,
                    "gross_amount" => $tx->gross_amount
                ]
            ]);
            $transaction = TransactionModel::find($tx->id);
            $transaction->snap_url = $snapUrl;
            $transaction->save();

            TransactionHistoryModel::create([
                "transaction_id" => $transaction->id,
                "status" => MidtransStatusConstant::PENDING
            ]);
        }
    }
}
