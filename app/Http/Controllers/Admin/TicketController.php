<?php

namespace App\Http\Controllers\Admin;

use App\Constants\TicketConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    protected $ticketTable;

    public function __construct()
    {
        $this->ticketTable = (new TicketModel())->getTable();
    }

    public function get(Request $request)
    {
        $tickets = TicketModel::orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($tickets);
    }

    public function set(Request $request, TicketModel $ticket)
    {
        return DB::transaction(function () use ($request, $ticket) {
            $ticket->name = $request->name;
            $ticket->description = $request->description;
            $ticket->adult_price = $request->adult_price;
            $ticket->child_price = $request->child_price;
            $ticket->type = $request->type;
            $ticket->save();

            return ResponseHelper::response($ticket);
        });
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "description" => "required|string",
            "adult_price" => "required|numeric|min:1",
            "child_price" => "required|numeric|min:1",
            "stock" => "required|numeric|min:1",
            "type" => ["required", Rule::in([TicketConstant::SINGLE, TicketConstant::BUNDLE])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new TicketModel());
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketTable,id",
            "name" => "required|string",
            "description" => "required|string",
            "adult_price" => "required|numeric|min:1",
            "child_price" => "required|numeric|min:1",
            "type" => ["required", Rule::in([TicketConstant::SINGLE, TicketConstant::BUNDLE])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, TicketModel::find($request->id));
    }

    public function delete(Request $request, $id)
    {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        TicketModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
