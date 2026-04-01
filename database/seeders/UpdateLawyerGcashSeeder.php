<?php
// Usage: php artisan db:seed --class=UpdateLawyerGcashSeeder
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateLawyerGcashSeeder extends Seeder
{
    public function run()
    {
        // Example: update lawyer with user_id = 1
        $userId = 1; // Change to your target lawyer's user_id
        $gcashNumber = '09123456789'; // Change to actual number
        $gcashQr = 'lawyer1_qr.png'; // Change to actual filename

        // Check if QR file exists in storage/app/public
        if (!Storage::disk('public')->exists($gcashQr)) {
            $this->command->error("QR code file not found: $gcashQr");
            return;
        }

        DB::table('lawyer_profiles')
            ->where('user_id', $userId)
            ->update([
                'gcash_number' => $gcashNumber,
                'gcash_qr' => $gcashQr,
            ]);

        $this->command->info("Lawyer profile updated for user_id $userId.");
    }
}
