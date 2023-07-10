<?php

namespace App\Http\Controllers\Admin;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller {
    protected $transactionTable, $transactionHistoryTable;

    public function __construct() {
        $this->transactionTable = (new TransactionModel())->getTable();
        $this->transactionHistoryTable = (new TransactionHistoryModel())->getTable();
    }

    public function getData(array $selects = [], array $conditions = [], $isLastYear = false) {
        $year = $isLastYear ? Carbon::now()->subYears(1)->year : Carbon::now()->year;
        $detailIds = DB::table("$this->transactionHistoryTable as detail_mx")
            ->selectRaw("max(detail_mx.id) as detail_id, detail_mx.transaction_id")
            ->groupBy("detail_mx.transaction_id")
            ->toSql();
        $detailData = DB::table("$this->transactionHistoryTable as detail_data")
            ->selectRaw("detail_data.id, detail_data.status")
            ->toSql();
        return TransactionModel::selectRaw(implode(",", array_merge([
            "to_char(to_timestamp(created_at)::date, 'MON') as label",
            "extract(month from to_timestamp(created_at)::date) as month",
            "extract(year from to_timestamp(created_at)::date) as year"
        ], $selects)))
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
            ->whereRaw(implode(" and ", array_merge([
                "extract(year from to_timestamp(created_at)::date) = $year"
            ], $conditions)))
            ->groupByRaw("1,2,3")
            ->orderBy("month")
            ->get();
    }

    public function get(Request $request) {
        $data = TransactionModel::with("latestHistory", "histories", "ticket")
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($data);
    }

    public function getVisitor(Request $request) {
        $parameters = [
            [
                "sum(total_adult + total_child) as total"
            ], [
                "detail.status in (" . implode(",", [
                    MidtransStatusConstant::CHECK_IN,
                    MidtransStatusConstant::CHECK_OUT
                ]) . ")"
            ]
        ];
        $current = $this->getData($parameters[0], $parameters[1]);

        $labels = [];
        $datasets = [];
        foreach ($current as $transaction) {
            array_push($labels, $transaction->label);
            array_push($datasets, (int) $transaction->total);
        }

        $data = [
            "labels" => $labels,
            "datasets" => [
                [
                    "label" => "Visitors",
                    "backgroundColor" => "#F87979",
                    "data" => $datasets
                ]
            ]
        ];

        return ResponseHelper::response($data);
    }

    public function getIncome(Request $request) {
        $parameters = [
            [
                "sum(gross_amount) as total"
            ], [
                "detail.status in (" . implode(",", [
                    MidtransStatusConstant::SETTLEMENT,
                    MidtransStatusConstant::CHECK_IN,
                    MidtransStatusConstant::CHECK_OUT
                ]) . ")"
            ]
        ];
        $current = $this->getData($parameters[0], $parameters[1]);

        $labels = [];
        $datasets = [];
        foreach ($current as $transaction) {
            array_push($labels, $transaction->label);
            array_push($datasets, (int) $transaction->total);
        }

        $data = [
            "labels" => $labels,
            "datasets" => [
                [
                    "label" => "Income",
                    "backgroundColor" => "#F87979",
                    "data" => $datasets
                ]
            ]
        ];

        return ResponseHelper::response($data);
    }
}
