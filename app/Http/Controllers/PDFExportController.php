<?php

namespace App\Http\Controllers;

use App\Models\Database; // adjust namespace if needed
use Barryvdh\DomPDF\Facade\Pdf;

class PDFExportController extends Controller
{
    public function mysqlStatus()
    {
        $data = Database::select('database_name', 'mysql_status')->get();
        $date = now()->format('M j, Y');
        $filename = "MySQL Status ({$date}).pdf";
        return Pdf::loadView('pdf.mysql_status', ['data' => $data])->download($filename);
    }

    public function mysqlReplicationStatus()
    {
        $data = Database::select('database_name', 'replication_status')->get();
        $date = now()->format('M j, Y');
        $filename = "MySQL Replication Status ({$date}).pdf";
        return Pdf::loadView('pdf.replication_status', ['data' => $data])->download($filename);
    }

    public function homeUtilization()
    {
        $data = Database::select('database_name', 'root_free', 'root_used', 'root_total')->get()->map(function ($item) {
            $used = floatval(preg_replace('/[^0-9.]/', '', $item->root_used));
            $total = floatval(preg_replace('/[^0-9.]/', '', $item->root_total));
            $percentage = $total > 0 ? round(($used / $total) * 100, 2) : 0;
            $item->percentage = $percentage;
            return $item;
        });

        $date = now()->format('M j, Y');
        $filename = "MySQL Disk Storage ({$date}).pdf";
        return Pdf::loadView('pdf.home_utilization', ['data' => $data])->download($filename);
    }

    public function viewDbTeam()
    {
        return view('dbteam');
    }

}

