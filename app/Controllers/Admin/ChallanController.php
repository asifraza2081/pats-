<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Application;
use App\Models\Payment;
use App\Services\SmsService;

class ChallanController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin');
    }

    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();
        
        $challans = $db->fetchAll(
            "SELECT pay.*, 
                    a.challan_ref, a.status as app_status,
                    u.name as candidate_name, u.cnic, u.phone
             FROM payments pay
             JOIN applications a ON a.id = pay.application_id
             JOIN candidates c ON c.id = a.candidate_id
             JOIN users u ON u.id = c.user_id
             ORDER BY pay.status ASC, pay.created_at DESC
             LIMIT 500"
        );

        View::render('admin/challans/index', [
            'pageTitle' => 'Fee Challans & Payments',
            'challans'  => $challans
        ], 'admin');
    }

    public function verify(Request $request, array $params = []): void
    {
        CSRF::check();
        $paymentId = (int) $params['id'];
        
        $payment = Payment::find($paymentId);
        if (!$payment) Response::abort(404);

        $bankRef = trim($request->post('bank_ref', ''));
        $depositDate = $request->post('deposit_date', date('Y-m-d'));

        // Update Payment Log
        Payment::updateWhere([
            'status'         => 'paid',
            'transaction_id' => "BANK-" . ($bankRef ?: 'TELLER-' . time()),
            'deposit_date'   => $depositDate,
            'verified_by'    => Auth::id(),
            'verified_at'    => date('Y-m-d H:i:s')
        ], ['id' => $paymentId]);

        // Update Application Status
        Application::updateWhere(['status' => 'fee_paid'], ['id' => $payment['application_id']]);

        // Trigger SMS notification
        $appData = Application::getDetails((int)$payment['application_id']);
        if ($appData && $appData['phone']) {
            $sms = new SmsService();
            $msg = "PATS: Fee Challan ({$appData['challan_ref']}) verified successfully. Keep visiting portal for Roll No Slip.";
            $sms->send($appData['phone'], $msg, 'payment_verified');
        }

        Session::flash('success', "Payment #{$paymentId} verified and SMS dispatched.");
        Response::redirect('/admin/challans');
    }
}
