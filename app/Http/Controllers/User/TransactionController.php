<?php

namespace App\Http\Controllers\User;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller {
    protected $transactionTable;

    public function __construct() {
        $this->transactionTable = (new TransactionModel())->getTable();
    }

    public function get(Request $request) {
        $data = TransactionModel::with("latestHistory", "histories", "ticketBundle.tickets")
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($data);
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
