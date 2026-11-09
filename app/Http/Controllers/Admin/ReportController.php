<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Donation;
use App\Models\DonationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->range($request);

        return view('admin.reports', [
            'from' => $from,
            'to' => $to,
            'summary' => $this->summary($from, $to),
            'monthly' => $this->monthly($from, $to),
            'byCategory' => $this->byCategory($from, $to),
            'activity' => $this->activity($from, $to),
            'transactions' => $this->transactions($from, $to)->paginate(25)->withQueryString(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);
        $rows = $this->transactions($from, $to)->get();

        $filename = 'customyzables-transactions-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // Excel opens UTF-8 correctly with a BOM.

            fputcsv($out, ['Date', 'Donation', 'Donor', 'Upcycler', 'Status', 'Scheduled', 'Completed', 'Weight (kg)'], ',', '"', '');
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->created_at?->format('Y-m-d H:i'),
                    $this->csvCell($row->donation_title),
                    $this->csvCell($row->donor_name),
                    $this->csvCell($row->upcycler_name),
                    $row->status->value,
                    $row->scheduled_at?->format('Y-m-d H:i'),
                    $row->completed_at?->format('Y-m-d H:i'),
                    $row->weight_kg,
                ], ',', '"', '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Neutralise text that a spreadsheet would treat as a formula. */
    private function csvCell(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function range(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->subDays(30)->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();

        return $from->lte($to) ? [$from, $to] : [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
    }

    private function summary(Carbon $from, Carbon $to): array
    {
        $donations = Donation::query()->whereBetween('created_at', [$from, $to]);

        $requests = DonationRequest::query()->whereBetween('created_at', [$from, $to]);
        $totalRequests = (clone $requests)->count();
        $completedRequests = (clone $requests)->where('status', 'completed')->count();

        return [
            'posted' => (clone $donations)->count(),
            'kg' => (float) (clone $donations)->sum('weight_kg'),
            'completed' => (clone $donations)->whereIn('status', ['received', 'completed'])->count(),
            'requests' => $totalRequests,
            'completionRate' => $totalRequests ? (int) round(($completedRequests / $totalRequests) * 100) : 0,
        ];
    }

    private function monthly(Carbon $from, Carbon $to)
    {
        return Donation::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total, SUM(weight_kg) as kg")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function byCategory(Carbon $from, Carbon $to)
    {
        return Donation::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('category, COUNT(*) as total, SUM(weight_kg) as kg')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }

    private function activity(Carbon $from, Carbon $to)
    {
        // LEFT JOIN keeps system actions (no actor) in the report.
        return AuditLog::query()
            ->leftJoin('users', 'users.id', '=', 'audit_logs.actor_id')
            ->whereBetween('audit_logs.created_at', [$from, $to])
            ->selectRaw("COALESCE(users.name, 'System') as actor, COUNT(*) as total, MAX(audit_logs.created_at) as last_at")
            ->groupBy('audit_logs.actor_id', 'users.name')
            ->orderByDesc('total')
            ->get();
    }

    private function transactions(Carbon $from, Carbon $to): Builder
    {
        return DonationRequest::query()
            ->join('donations', 'donations.id', '=', 'donation_requests.donation_id')
            ->join('users as donors', 'donors.id', '=', 'donations.user_id')
            ->join('users as upcyclers', 'upcyclers.id', '=', 'donation_requests.upcycler_id')
            ->whereBetween('donation_requests.created_at', [$from, $to])
            ->orderByDesc('donation_requests.created_at')
            ->select([
                'donation_requests.created_at',
                'donation_requests.status',
                'donation_requests.scheduled_at',
                'donation_requests.completed_at',
                'donations.title as donation_title',
                'donations.weight_kg',
                'donors.name as donor_name',
                'upcyclers.name as upcycler_name',
            ]);
    }
}
