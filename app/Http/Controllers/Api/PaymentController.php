<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $payments = Payment::with(['user', 'payable'])->paginate(10);
        return $this->success($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payable_type' => 'required|in:App\Models\Membership,App\Models\GymClass',
            'payable_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment = Payment::create($validated);

        return $this->success($payment->load(['user', 'payable']), 'Payment created successfully', 201);
    }

    public function show(Payment $payment)
    {
        return $this->success($payment->load(['user', 'payable']));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string',
        ]);

        $payment->update($validated);

        return $this->success($payment->load(['user', 'payable']), 'Payment updated successfully');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return $this->success(null, 'Payment deleted successfully');
    }
} 