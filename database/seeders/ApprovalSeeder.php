<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Approval;
use App\Models\Org\User;

class ApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = '01974d1b-f010-716a-8ede-be668ca7d657';
        $users = User::where('id_workplace',$companyId)->get();

        if($users->isEmpty()){
            $this->command->warn("Tidak ada user ditemukan untuk companyv {$companyId}");
            return;
        }
        
        $admins = User::where('id_workplace', $companyId)
                   ->where('is_admin', '1')
                   ->pluck('id');

        if ($admins->isEmpty()) {
            $this->command->warn("Tidak ada admin ditemukan untuk company ID: {$companyId}");
            return;
        }
        
        foreach ($users as $user) {
            $approvalCount = rand(0, 1);

            for ($i = 0; $i < $approvalCount; $i++) {
                $status = fake()->randomElement(['pending', 'approved', 'rejected']);
                // Tanggal request antara 1 bulan ke belakang sampai hari ini
                $startDate = now()->subDays(rand(1, 5))->format('Y-m-d H:i:s');
                $endDate = now()->subDays(rand(0, 5))->gt($startDate)
                    ? now()->subDays(rand(0, 5))->format('Y-m-d H:i:s')
                    : $startDate;
                
                Approval::create([
                    'id_user' => $user->id,
                    'request_type' => fake()->randomElement(['overtime', 'permit', 'sick', 'leave']),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'reason' => fake()->sentence(),
                    'status' => $status,
                    'approved_by' => in_array($status, ['approved', 'rejected']) 
                        ? $admins->random() 
                        : null,
                ]);
            }
        }


    }
}
