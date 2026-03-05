<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\View;

class DashboardController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin', 'data_entry');
    }

    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();

        // Key Metrics
        $stats = [];
        $stats['candidates'] = $db->fetchOne('SELECT COUNT(id) as c FROM candidates')['c'];
        $stats['projects'] = $db->fetchOne('SELECT COUNT(id) as c FROM projects WHERE status IN ("open", "draft")')['c'];
        $stats['applications'] = $db->fetchOne('SELECT COUNT(id) as c FROM applications')['c'];
        $stats['revenue'] = $db->fetchOne('SELECT SUM(amount) as s FROM payments WHERE status = "paid"')['s'] ?? 0;

        // Recent Activity
        $recentApps = $db->fetchAll(
            'SELECT a.id, a.applied_at, a.status, u.name, j.title 
             FROM applications a
             JOIN candidates c ON a.candidate_id = c.id
             JOIN users u ON u.id = c.user_id
             JOIN jobs j ON a.job_id = j.id
             ORDER BY a.applied_at DESC LIMIT 5'
        );

        $pendingPayments = $db->fetchOne('SELECT COUNT(id) as c FROM payments WHERE status="pending"')['c'];

        View::render('admin/dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'stats'     => $stats,
            'recentApps' => $recentApps,
            'pendingPayments' => $pendingPayments
        ], 'admin');
    }
}
