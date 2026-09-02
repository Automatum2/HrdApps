<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PayrollPeriod;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Http\Controllers\PayrollController;
use Carbon\Carbon;

class GenerateMonthlyPayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly payroll automatically for all active employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated payroll generation...');

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        
        // Define period dates (1st to end of month)
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        $periodName = 'Gaji Bulan ' . $now->translatedFormat('F Y');

        // Check if period already exists
        $period = PayrollPeriod::where('tanggal_mulai', $startDate->toDateString())
            ->where('tanggal_selesai', $endDate->toDateString())
            ->first();

        if (!$period) {
            $period = PayrollPeriod::create([
                'nama_periode' => $periodName,
                'tanggal_mulai' => $startDate->toDateString(),
                'tanggal_selesai' => $endDate->toDateString(),
                'status' => 'proses', // Because we are generating it right away
            ]);
            $this->info("Created new Payroll Period: $periodName");
        } else {
            $period->update(['status' => 'proses']);
            $this->info("Using existing Payroll Period: $periodName");
        }

        $employees = Employee::whereIn('status', ['aktif', 'tetap', 'kontrak'])->get();
        if ($employees->isEmpty()) {
            $employees = Employee::all();
        }

        $bar = $this->output->createProgressBar(count($employees));
        $bar->start();

        foreach ($employees as $employee) {
            $data = PayrollController::calculatePayroll($employee, $month, $year);

            $payroll = Payroll::updateOrCreate(
                ['employee_id' => $employee->id, 'period_id' => $period->id],
                [
                    'gaji_pokok' => $data['gajiPokok'],
                    'total_tunjangan' => $data['totalTunjangan'],
                    'total_potongan' => $data['totalPotongan'],
                    'total_lembur' => 0,
                    'gaji_kotor' => $data['gajiKotor'],
                    'gaji_bersih' => $data['gajiBersih'],
                    'status' => 'draft'
                ]
            );

            // Sync Payroll Details
            PayrollDetail::where('payroll_id', $payroll->id)->delete();
            
            foreach ($data['allowancesList'] as $allowance) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'komponen' => $allowance['nama'],
                    'tipe' => 'pendapatan',
                    'jumlah' => $allowance['jumlah']
                ]);
            }

            foreach ($data['deductionsList'] as $deduction) {
                PayrollDetail::create([
                    'payroll_id' => $payroll->id,
                    'komponen' => $deduction['nama'],
                    'tipe' => 'potongan',
                    'jumlah' => $deduction['jumlah']
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Payroll generation completed successfully!');
    }
}
