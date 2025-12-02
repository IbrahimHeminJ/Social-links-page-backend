<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\CreatePaymentRequest;
use App\Models\Payment;
use App\Services\FibPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $fibService;

    public function __construct(FibPaymentService $fibService)
    {
        $this->fibService = $fibService;
    }
    /**
     * Create a new payment
     */
    public function create(CreatePaymentRequest $request)
    {
        try {
            $user = $request->user();
            $amount = $request->input('amount');

            // Create payment via FIB (using service with SSL verification disabled)
            $response = $this->fibService->create($amount);

            // Store payment in database
            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_id' => $response['paymentId'],
                'readable_code' => $response['readableCode'] ?? null,
                'qr_code' => $response['qrCode'] ?? null,
                'valid_until' => isset($response['validUntil']) ? Carbon::parse($response['validUntil']) : null,
                'personal_app_link' => $response['personalAppLink'] ?? null,
                'business_app_link' => $response['businessAppLink'] ?? null,
                'corporate_app_link' => $response['corporateAppLink'] ?? null,
                'amount' => $amount,
                'currency' => 'IQD', // Default currency, adjust if needed
                'status' => 'UNPAID',
            ]);

            return $this->success(
                'Payment created successfully',
                [
                    'payment_id' => $payment->payment_id,
                    'readable_code' => $payment->readable_code,
                    'qr_code' => $payment->qr_code,
                    'valid_until' => $payment->valid_until ? $payment->valid_until->format('c') : null,
                    'personal_app_link' => $payment->personal_app_link,
                    'business_app_link' => $payment->business_app_link,
                    'corporate_app_link' => $payment->corporate_app_link,
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency,
                    'status' => $payment->status,
                ],
                201
            );
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'user_id' => $request->user()->id,
                'amount' => $request->input('amount'),
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                'Failed to create payment: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get payment status
     */
    public function status($paymentId)
    {
        try {
            $payment = Payment::where('payment_id', $paymentId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Get status from FIB (using service with SSL verification disabled)
            $response = $this->fibService->status($paymentId);

            // Update payment in database
            $payment->update([
                'status' => $response['status'],
                'valid_until' => isset($response['validUntil']) ? Carbon::parse($response['validUntil']) : null,
                'amount' => $response['amount']['amount'] ?? $payment->amount,
                'currency' => $response['amount']['currency'] ?? $payment->currency,
                'declining_reason' => $response['decliningReason'] ?? null,
                'declined_at' => isset($response['declinedAt']) ? Carbon::parse($response['declinedAt']) : null,
                'paid_by_name' => $response['paidBy']['name'] ?? null,
                'paid_by_iban' => $response['paidBy']['iban'] ?? null,
            ]);

            return $this->success(
                'Payment status fetched successfully',
                [
                    'payment_id' => $payment->payment_id,
                    'status' => $payment->status,
                    'valid_until' => $payment->valid_until ? $payment->valid_until->format('c') : null,
                    'amount' => [
                        'amount' => (float) $payment->amount,
                        'currency' => $payment->currency,
                    ],
                    'declining_reason' => $payment->declining_reason,
                    'declined_at' => $payment->declined_at ? $payment->declined_at->format('c') : null,
                    'paid_by' => $payment->paid_by_name ? [
                        'name' => $payment->paid_by_name,
                        'iban' => $payment->paid_by_iban,
                    ] : null,
                ],
                200
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Payment not found', 404);
        } catch (\Exception $e) {
            Log::error('Payment status check failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                'Failed to fetch payment status: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Cancel a payment
     */
    public function cancel($paymentId)
    {
        try {
            $payment = Payment::where('payment_id', $paymentId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if ($payment->status === 'PAID') {
                return $this->error('Cannot cancel a paid payment', 422);
            }

            if ($payment->status === 'DECLINED') {
                return $this->error('Payment is already declined', 422);
            }

            // Cancel payment via FIB (using service with SSL verification disabled)
            $this->fibService->cancel($paymentId);

            // Update payment status
            $payment->update([
                'status' => 'DECLINED',
                'declining_reason' => 'PAYMENT_CANCELLATION',
                'declined_at' => now(),
            ]);

            return $this->success(
                'Payment cancelled successfully',
                null,
                200
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Payment not found', 404);
        } catch (\Exception $e) {
            Log::error('Payment cancellation failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                'Failed to cancel payment: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get all payments for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $payments = Payment::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return $this->success(
                'Payments fetched successfully',
                $payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_id' => $payment->payment_id,
                        'amount' => (float) $payment->amount,
                        'currency' => $payment->currency,
                        'status' => $payment->status,
                        'valid_until' => $payment->valid_until ? $payment->valid_until->format('c') : null,
                        'created_at' => $payment->created_at->format('c'),
                        'updated_at' => $payment->updated_at->format('c'),
                    ];
                })
            );
        } catch (\Exception $e) {
            Log::error('Payment list fetch failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                'Failed to fetch payments: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get a specific payment by ID
     */
    public function show($id)
    {
        try {
            $payment = Payment::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            return $this->success(
                'Payment fetched successfully',
                [
                    'id' => $payment->id,
                    'payment_id' => $payment->payment_id,
                    'readable_code' => $payment->readable_code,
                    'qr_code' => $payment->qr_code,
                    'valid_until' => $payment->valid_until ? $payment->valid_until->format('c') : null,
                    'personal_app_link' => $payment->personal_app_link,
                    'business_app_link' => $payment->business_app_link,
                    'corporate_app_link' => $payment->corporate_app_link,
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency,
                    'status' => $payment->status,
                    'declining_reason' => $payment->declining_reason,
                    'declined_at' => $payment->declined_at ? $payment->declined_at->format('c') : null,
                    'paid_by' => $payment->paid_by_name ? [
                        'name' => $payment->paid_by_name,
                        'iban' => $payment->paid_by_iban,
                    ] : null,
                    'created_at' => $payment->created_at->format('c'),
                    'updated_at' => $payment->updated_at->format('c'),
                ],
                200
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Payment not found', 404);
        } catch (\Exception $e) {
            Log::error('Payment fetch failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                'Failed to fetch payment: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Handle FIB payment callback
     * This is a public route that FIB will call when payment status changes
     */
    public function callback(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string',
                'status' => 'required|string|in:PAID,UNPAID,DECLINED',
            ]);

            $paymentId = $request->input('id');
            $status = $request->input('status');

            $payment = Payment::where('payment_id', $paymentId)->first();

            if (!$payment) {
                Log::warning('FIB callback: Payment not found', [
                    'payment_id' => $paymentId,
                    'status' => $status,
                ]);
                return response()->json([
                    'message' => 'Payment not found',
                ], 404);
            }

            // Get full payment details from FIB (using service with SSL verification disabled)
            $response = $this->fibService->status($paymentId);

            // Update payment in database
            $payment->update([
                'status' => $response['status'],
                'valid_until' => isset($response['validUntil']) ? Carbon::parse($response['validUntil']) : null,
                'amount' => $response['amount']['amount'] ?? $payment->amount,
                'currency' => $response['amount']['currency'] ?? $payment->currency,
                'declining_reason' => $response['decliningReason'] ?? null,
                'declined_at' => isset($response['declinedAt']) ? Carbon::parse($response['declinedAt']) : null,
                'paid_by_name' => $response['paidBy']['name'] ?? null,
                'paid_by_iban' => $response['paidBy']['iban'] ?? null,
            ]);

            Log::info('FIB callback processed successfully', [
                'payment_id' => $paymentId,
                'status' => $payment->status,
            ]);

            return response()->json([
                'message' => 'Payment status updated successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('FIB callback error', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error processing callback',
            ], 200); // Return 200 to prevent FIB from retrying
        }
    }
}

