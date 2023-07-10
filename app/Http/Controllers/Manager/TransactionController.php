<?php

namespace App\Http\Controllers\Manager;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller {
    protected $transactionTable, $transactionHistoryTable;

    public function __construct() {
        $this->transactionTable = (new TransactionModel())->getTable();
        $this->transactionHistoryTable = (new TransactionHistoryModel())->getTable();
    }

    public function getData(array $conditions = [], $id = null) {
        $detailIds = DB::table("$this->transactionHistoryTable as detail_mx")
            ->selectRaw("max(detail_mx.id) as detail_id, detail_mx.transaction_id")
            ->groupBy("detail_mx.transaction_id")
            ->toSql();
        $detailData = DB::table("$this->transactionHistoryTable as detail_data")
            ->selectRaw("detail_data.id, detail_data.status")
            ->toSql();
        $data = TransactionModel::with("latestHistory", "histories", "ticket")
            ->select("$this->transactionTable.*")
            ->leftJoinSub(
                $detailIds,
                "detail_max",
                "$this->transactionTable.id",
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
            ->whereRaw(implode(" and ", $conditions))
            ->orderByDesc("$this->transactionTable.id");

        if (empty($id)) $data = $data->paginate();
        else $data = $data->find($id);

        return $data;
    }

    public function get(Request $request) {
        $filters = [
            "detail.status in (" . implode(",", [
                MidtransStatusConstant::SETTLEMENT,
                MidtransStatusConstant::CHECK_IN
            ]) . ")",
            "{$this->transactionTable}.date = '" . Carbon::now("Asia/Jakarta") . "'"
        ];
        if (!empty($request->input("search"))) {
            array_push($filters, "$this->transactionTable.invoice_number = '$request->search'");
        }

        return ResponseHelper::response($this->getData($filters));
    }

    public function checkIn(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->transactionTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($id) {
            $transaction = $this->getData([
                "detail.status = " . MidtransStatusConstant::SETTLEMENT
            ], $id);
            if (empty($transaction->id)) return ResponseHelper::response(null, "Transaction is invalid.", 400);
            if (!empty($transaction->check_in)) return ResponseHelper::response(null, "Already check in.", 400);
            $transaction->check_in = time();
            $transaction->save();

            TransactionHistoryModel::create([
                "transaction_id" => $transaction->id,
                "status" => MidtransStatusConstant::CHECK_IN
            ]);

            return ResponseHelper::response();
        });
    }

    public function checkOut(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->transactionTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($id) {
            $transaction = $this->getData([
                "detail.status = " . MidtransStatusConstant::CHECK_IN
            ], $id);
            if (empty($transaction->id)) return ResponseHelper::response(null, "Transaction is invalid.", 400);
            if (!empty($transaction->check_out)) return ResponseHelper::response(null, "Already check out.", 400);
            $transaction->check_out = time();
            $transaction->save();

            TransactionHistoryModel::create([
                "transaction_id" => $transaction->id,
                "status" => MidtransStatusConstant::CHECK_OUT
            ]);

            return ResponseHelper::response();
        });
    }
}
