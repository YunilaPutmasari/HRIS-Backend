<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment\Invoice;
use Carbon\Carbon;
use App\Enums\InvoiceStatus;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'invoice:check-overdue';
    protected $description = 'Mengecek overdue invoice jika sudah lewat tenggat';

    public function handle()
    {
        $now = Carbon::now();

        // $invoices = Invoice::where('status','unpaid') //Tidak disarankan ternyata
        $invoices = Invoice::where('status',InvoiceStatus::UNPAID)
            ->where('due_datetime','<',$now)
            ->get();

        foreach($invoices as $invoices){
            // $invoices->update(['status'=>'overdue']); //Tidak diubah di BE karena biar dia logic xendit
            $this->info("Invoice {$invoices->id} unpaid dan lewat due date");
        }
        return Command::SUCCESS;
    }
}
