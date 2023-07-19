<?php

namespace App\Http\Controllers\User;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
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
        $data = TransactionModel::with("latestHistory", "histories", "ticket", "user")
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
        $data = TransactionModel::with("latestHistory", "histories", "ticket", "user")
            ->orderByDesc("id");

        if (!empty($request->search)) $data = $data->orWhere("invoice_number", "like", "%{$request->search}%")
            ->orWhereRelation("user", "name", "like", "%{$request->search}%")
            ->orWhereRelation("ticket", "name", "like", "%{$request->search}%");

        $data = $data->paginate();

        return ResponseHelper::response($data);
    }

    public function active(Request $request) {
        return ResponseHelper::response($this->getData([
            "detail.status = " . MidtransStatusConstant::SETTLEMENT
        ]));
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            "order_id" => "required|string|exists:$this->transactionTable,invoice_number",
            "transaction_status" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $transaction = TransactionModel::where("invoice_number", $request->order_id)->first();
        TransactionHistoryModel::create([
            "transaction_id" => $transaction->id,
            "status" => MidtransStatusConstant::getValueByName(strtoupper($request->transaction_status))
        ]);

        return ResponseHelper::response();
    }
}
