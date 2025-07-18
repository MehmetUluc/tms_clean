<?php

namespace App\Plugins\Partner\Services;

use App\Models\User;
use App\Plugins\Partner\Models\Partner;
use App\Plugins\Partner\Models\PartnerBankAccount;
use App\Plugins\Partner\Models\PartnerCommission;
use App\Plugins\Partner\Models\PartnerTransaction;
use App\Plugins\Partner\Models\PartnerPaymentRequest;
use App\Plugins\Partner\Models\PartnerPayment;
use App\Plugins\Partner\Models\PartnerDocument;
use App\Plugins\Partner\Models\PartnerMinistryReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class PartnerService
{
    /**
     * Create a new partner.
     */
    public function createPartner(array $data, User $user = null): Partner
    {
        DB::beginTransaction();

        try {
            // Create the partner
            $partner = Partner::create([
                'user_id' => $user ? $user->id : $data['user_id'],
                'company_name' => $data['company_name'],
                'tax_number' => $data['tax_number'] ?? null,
                'tax_office' => $data['tax_office'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'website' => $data['website'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'default_commission_rate' => $data['default_commission_rate'] ?? 10.00,
                'contract_start_date' => $data['contract_start_date'] ?? now(),
                'contract_end_date' => $data['contract_end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // If user is provided, assign partner role
            if ($user) {
                $partnerRole = Role::findByName('partner');
                $user->assignRole($partnerRole);
            }

            // Add bank account if provided
            if (isset($data['bank_account'])) {
                $this->addBankAccount($partner, $data['bank_account']);
            }

            DB::commit();
            return $partner;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create partner: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }

    /**
     * Update a partner.
     */
    public function updatePartner(Partner $partner, array $data): Partner
    {
        DB::beginTransaction();

        try {
            // Update the partner
            $partner->update(array_filter([
                'company_name' => $data['company_name'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'tax_office' => $data['tax_office'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'website' => $data['website'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'status' => $data['status'] ?? null,
                'default_commission_rate' => $data['default_commission_rate'] ?? null,
                'contract_start_date' => $data['contract_start_date'] ?? null,
                'contract_end_date' => $data['contract_end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ], function ($value) {
                return $value !== null;
            }));

            DB::commit();
            return $partner;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update partner: ' . $e->getMessage(), [
                'partner_id' => $partner->id,
                'data' => $data,
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }

    /**
     * Add a bank account to a partner.
     */
    public function addBankAccount(Partner $partner, array $data): PartnerBankAccount
    {
        return $partner->bankAccounts()->create([
            'bank_name' => $data['bank_name'],
            'account_name' => $data['account_name'],
            'iban' => $data['iban'],
            'account_number' => $data['account_number'] ?? null,
            'branch_code' => $data['branch_code'] ?? null,
            'swift_code' => $data['swift_code'] ?? null,
            'currency' => $data['currency'] ?? 'TRY',
            'is_default' => $data['is_default'] ?? false,
        ]);
    }

    /**
     * Set a commission rate for a partner.
     */
    public function setCommissionRate(
        Partner $partner, 
        float $commissionRate, 
        ?int $hotelId = null, 
        ?int $roomTypeId = null, 
        ?int $boardTypeId = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $description = null,
        ?int $createdBy = null
    ): PartnerCommission {
        // If this is a default commission rate and we have a more specific one, make sure to update partner's default rate
        if (!$hotelId && !$roomTypeId && !$boardTypeId) {
            $partner->update(['default_commission_rate' => $commissionRate]);
        }
        
        // Create the commission rate record
        return $partner->commissions()->create([
            'hotel_id' => $hotelId,
            'room_type_id' => $roomTypeId,
            'board_type_id' => $boardTypeId,
            'commission_rate' => $commissionRate,
            'start_date' => $startDate ? date('Y-m-d', strtotime($startDate)) : null,
            'end_date' => $endDate ? date('Y-m-d', strtotime($endDate)) : null,
            'description' => $description,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Create a transaction record for a partner.
     */
    public function createTransaction(
        Partner $partner,
        float $amount,
        ?int $reservationId = null,
        ?int $hotelId = null,
        string $transactionType = 'booking',
        string $currency = 'TRY',
        ?string $notes = null,
        ?int $createdBy = null
    ): PartnerTransaction {
        // Calculate commission amount
        $commissionRate = $this->getApplicableCommissionRate($partner->id, $hotelId);
        $commissionAmount = $amount * ($commissionRate / 100);
        $netAmount = $amount - $commissionAmount;
        
        // Create the transaction
        return $partner->transactions()->create([
            'reservation_id' => $reservationId,
            'hotel_id' => $hotelId,
            'amount' => $amount,
            'commission_amount' => $commissionAmount,
            'net_amount' => $netAmount,
            'currency' => $currency,
            'transaction_date' => now(),
            'transaction_type' => $transactionType,
            'status' => 'pending',
            'reference_number' => PartnerTransaction::generateReferenceNumber(),
            'notes' => $notes,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Get the applicable commission rate for a given partner and hotel.
     */
    private function getApplicableCommissionRate(int $partnerId, ?int $hotelId = null, ?int $roomTypeId = null, ?int $boardTypeId = null): float
    {
        // Get the partner
        $partner = Partner::findOrFail($partnerId);
        
        // If we have hotel details, try to find a specific commission rate
        if ($hotelId) {
            $specificCommission = PartnerCommission::applicable($partnerId, $hotelId, $roomTypeId, $boardTypeId)
                ->first();
                
            if ($specificCommission) {
                return $specificCommission->commission_rate;
            }
        }
        
        // Otherwise, return the partner's default commission rate
        return $partner->default_commission_rate;
    }

    /**
     * Create a payment request for a partner.
     */
    public function createPaymentRequest(
        Partner $partner,
        float $amount,
        ?int $bankAccountId = null,
        string $currency = 'TRY',
        ?string $notes = null,
        ?int $createdBy = null
    ): PartnerPaymentRequest {
        // Get default bank account if none provided
        if (!$bankAccountId) {
            $defaultBankAccount = $partner->bankAccounts()->where('is_default', true)->first();
            $bankAccountId = $defaultBankAccount ? $defaultBankAccount->id : null;
        }
        
        // Create the payment request
        return $partner->paymentRequests()->create([
            'bank_account_id' => $bankAccountId,
            'amount' => $amount,
            'currency' => $currency,
            'requested_date' => now(),
            'status' => 'pending',
            'notes' => $notes,
            'created_by' => $createdBy ?? auth()->id(),
            'reference_number' => PartnerPaymentRequest::generateReferenceNumber(),
        ]);
    }

    /**
     * Process a payment for a partner.
     */
    public function processPayment(
        PartnerPaymentRequest $paymentRequest,
        string $status,
        ?string $paymentMethod = null,
        ?string $paymentReference = null,
        ?string $notes = null,
        ?int $processedBy = null
    ): PartnerPayment {
        $processedBy = $processedBy ?? auth()->id();
        
        DB::beginTransaction();
        
        try {
            // Update the payment request status
            switch ($status) {
                case 'approved':
                    $paymentRequest->approve($processedBy, $notes);
                    break;
                case 'rejected':
                    $paymentRequest->reject($processedBy, $notes ?: 'Payment request rejected', $notes);
                    break;
                case 'paid':
                    $paymentRequest->markAsPaid($processedBy, $paymentReference, $notes);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid payment status: {$status}");
            }
            
            // If the payment is approved or paid, create a payment record
            if (in_array($status, ['approved', 'paid'])) {
                $payment = PartnerPayment::create([
                    'partner_id' => $paymentRequest->partner_id,
                    'payment_request_id' => $paymentRequest->id,
                    'bank_account_id' => $paymentRequest->bank_account_id,
                    'amount' => $paymentRequest->amount,
                    'currency' => $paymentRequest->currency,
                    'payment_date' => now(),
                    'due_date' => now()->addDays(3), // Default due date is 3 days from now
                    'status' => $status === 'paid' ? 'completed' : 'pending',
                    'payment_method' => $paymentMethod ?: 'bank_transfer',
                    'payment_reference' => $paymentReference,
                    'invoice_number' => PartnerPayment::generateInvoiceNumber(),
                    'notes' => $notes,
                    'created_by' => $processedBy,
                    'approved_by' => $status === 'paid' ? $processedBy : null,
                ]);
            }
            
            DB::commit();
            
            return $payment ?? null;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment: ' . $e->getMessage(), [
                'payment_request_id' => $paymentRequest->id,
                'status' => $status,
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }

    /**
     * Upload a document for a partner.
     */
    public function uploadDocument(
        Partner $partner,
        string $documentType,
        string $name,
        $file,
        ?string $comments = null,
        ?string $expiryDate = null,
        ?int $uploadedBy = null
    ): PartnerDocument {
        // Upload the file
        $path = $file->store('partner_documents/' . $partner->id);
        
        // Create the document record
        return $partner->documents()->create([
            'document_type' => $documentType,
            'name' => $name,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
            'comments' => $comments,
            'expiry_date' => $expiryDate ? date('Y-m-d', strtotime($expiryDate)) : null,
            'uploaded_by' => $uploadedBy ?? auth()->id(),
        ]);
    }

    /**
     * Create a ministry report for a partner.
     */
    public function createMinistryReport(
        Partner $partner,
        int $hotelId,
        string $reportType,
        string $reportDate,
        array $reportData,
        ?string $notes = null,
        ?int $submittedBy = null
    ): PartnerMinistryReport {
        return $partner->ministryReports()->create([
            'hotel_id' => $hotelId,
            'report_date' => date('Y-m-d', strtotime($reportDate)),
            'report_type' => $reportType,
            'status' => 'pending',
            'notes' => $notes,
            'report_data' => $reportData,
            'submitted_by' => $submittedBy ?? auth()->id(),
        ]);
    }

    /**
     * Submit a ministry report to the ministry.
     */
    public function submitMinistryReport(
        PartnerMinistryReport $report,
        ?string $notes = null,
        ?int $submittedBy = null
    ): bool {
        // TODO: Implement actual ministry submission logic here
        // This would typically involve API calls to the ministry's systems
        
        // For now, just mark as submitted
        return $report->markAsSubmitted(
            $submittedBy ?? auth()->id(),
            PartnerMinistryReport::generateSubmissionReference(),
            $notes
        );
    }

    /**
     * Get partner for a user.
     */
    public function getPartnerForUser(User $user): ?Partner
    {
        return Partner::where('user_id', $user->id)->first();
    }
    
    /**
     * Get partner financial summary.
     */
    public function getFinancialSummary(Partner $partner, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ? date('Y-m-d 00:00:00', strtotime($startDate)) : now()->startOfMonth()->format('Y-m-d 00:00:00');
        $endDate = $endDate ? date('Y-m-d 23:59:59', strtotime($endDate)) : now()->endOfMonth()->format('Y-m-d 23:59:59');
        
        // Get all transactions
        $transactions = $partner->transactions()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();
            
        // Get all payment requests
        $paymentRequests = $partner->paymentRequests()
            ->whereBetween('requested_date', [$startDate, $endDate])
            ->get();
            
        // Get all payments
        $payments = $partner->payments()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();
            
        // Calculate totals
        $totalTransactions = $transactions->count();
        $totalBookings = $transactions->where('transaction_type', 'booking')->count();
        $totalCancellations = $transactions->where('transaction_type', 'cancellation')->count();
        
        $totalAmount = $transactions->sum('amount');
        $totalCommission = $transactions->sum('commission_amount');
        $totalNetAmount = $transactions->sum('net_amount');
        
        $totalPaymentRequests = $paymentRequests->count();
        $pendingPaymentRequests = $paymentRequests->where('status', 'pending')->count();
        $pendingPaymentAmount = $paymentRequests->where('status', 'pending')->sum('amount');
        
        $totalPayments = $payments->where('status', 'completed')->sum('amount');
        $pendingPayments = $payments->where('status', 'pending')->sum('amount');
        
        // Calculate balance
        $balance = $totalNetAmount - $totalPayments - $pendingPayments;
        
        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'transactions' => [
                'total_count' => $totalTransactions,
                'bookings' => $totalBookings,
                'cancellations' => $totalCancellations,
                'total_amount' => $totalAmount,
                'total_commission' => $totalCommission,
                'total_net_amount' => $totalNetAmount,
            ],
            'payments' => [
                'total_payment_requests' => $totalPaymentRequests,
                'pending_payment_requests' => $pendingPaymentRequests,
                'pending_payment_amount' => $pendingPaymentAmount,
                'total_payments' => $totalPayments,
                'pending_payments' => $pendingPayments,
            ],
            'balance' => $balance,
        ];
    }

    /**
     * Check if a user is a partner.
     */
    public function isPartner(User $user): bool
    {
        return $user->hasRole('partner');
    }
    
    /**
     * Get the vendor for a user (backward compatibility).
     */
    public function getVendorForUser(User $user): ?Partner
    {
        return $this->getPartnerForUser($user);
    }
}