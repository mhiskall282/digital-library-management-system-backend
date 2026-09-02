<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->input('action');
        $dateRange = $request->input('date_range', 'ALL');
        $search = $request->input('search');

        $query = ActivityLog::with(['user', 'resource'])->latest();

        if ($action && $action !== 'ALL') {
            $query->where('action', $action);
        }

        if ($dateRange === 'TODAY') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'WEEK') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($dateRange === 'MONTH') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('resource', function ($r) use ($search) {
                      $r->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // High-Level Telemetry Metrics
        $totalLogs = ActivityLog::count();
        $totalDownloadsLogged = ActivityLog::where('action', 'DOWNLOAD')->count();
        $totalSubmissions = ActivityLog::whereIn('action', ['STUDENT_SUBMISSION', 'SUBMISSION_APPROVED'])->count();
        $totalSecurityEvents = ActivityLog::whereIn('action', ['LOGIN', 'LOGOUT', 'PASSWORD_RESET', 'SECURITY_ALERT'])->count();

        $actionTypes = ActivityLog::distinct()->pluck('action')->filter()->values();

        return view('admin.reports.index', compact(
            'logs',
            'totalLogs',
            'totalDownloadsLogged',
            'totalSubmissions',
            'totalSecurityEvents',
            'actionTypes',
            'action',
            'dateRange',
            'search'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $action = $request->input('action');
        $dateRange = $request->input('date_range', 'ALL');

        $query = ActivityLog::with(['user', 'resource'])->latest();

        if ($action && $action !== 'ALL') {
            $query->where('action', $action);
        }

        if ($dateRange === 'TODAY') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'WEEK') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($dateRange === 'MONTH') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="uew_library_audit_report_' . date('Y-m-d_His') . '.csv"',
        ];

        $columns = ['Log ID', 'Timestamp (GMT)', 'Action Event', 'User Name', 'Student / Staff ID', 'User Role', 'Target Resource', 'Client IP', 'Context Details'];

        return response()->streamDownload(function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(500, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    $ip = $log->details['ip'] ?? $log->ip_address ?? 'N/A';
                    $details = json_encode($log->details ?? []);

                    fputcsv($file, [
                        $log->id,
                        $log->created_at->toDateTimeString(),
                        $log->action,
                        $log->user->name ?? 'Guest / System',
                        $log->user->student_id ?? $log->user->email ?? 'N/A',
                        $log->user->role ?? 'N/A',
                        $log->resource->title ?? 'N/A',
                        $ip,
                        $details,
                    ]);
                }
            });

            fclose($file);
        }, 'uew_library_audit_report_' . date('Y-m-d_His') . '.csv', $headers);
    }
}
