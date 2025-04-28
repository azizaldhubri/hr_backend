<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\financial_transaction_model;
use App\Models\User ;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class financial_transactions extends Controller
{
    public function index()
    {
        return financial_transaction_model::with('employee')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'type' => 'required|in:salary,advance_payment,deduction,bonus,receipt,payment',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        $transaction = financial_transaction_model::create($request->all());

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        return financial_transaction_model::with('employee')->findOrFail($id);
    }
}
