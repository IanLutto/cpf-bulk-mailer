<?php

namespace App\Console\Commands;

use App\Mail\BulkEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBulkEmails extends Command
{
    protected $signature = 'emails:bulk
                            {--file=recipients.csv}';

    protected $description = 'Send CPF member consultation emails';

    public function handle(): int
    {
        $csvPath = storage_path(
            'app/' . $this->option('file')
        );

        if (!file_exists($csvPath)) {
            $this->error("Recipients file not found: {$csvPath}");

            return self::FAILURE;
        }

        $policyDirectory = storage_path('app/private/policies');

        $policyFiles = [
            'FINAL DRAFT Kasneb DC Communication Policy.pdf',
            'FINAL DRAFT Kasneb DC Complaints Handling Policy.pdf',
            'FINAL DRAFT Kasneb DC Conflict of Interest Policy.pdf',
            'FINAL DRAFT Kasneb DC Death Benefits Policy.pdf',
            'FINAL DRAFT Kasneb DC Risk Management Policy.pdf',
            'FINAL DRAFT Kasneb DC Treating Customers Fairly Policy.pdf',
            'FINAL DRAFT Kasneb DC Trustees’ Appointment, Election and Removal Policy.pdf',
            'FINAL DRAFT Kasneb DC Whistle Blower Protection Policy.pdf',
            'Kasneb DC Data Protection ICT Policy.pdf',
        ];

        $policyPaths = array_map(
            fn ($filename) => $policyDirectory . DIRECTORY_SEPARATOR . $filename,
            $policyFiles
        );

        foreach ($policyPaths as $path) {
            if (!file_exists($path)) {
                $this->error("Policy attachment not found: {$path}");

                return self::FAILURE;
            }
        }

        $file = fopen($csvPath, 'r');

        if (!$file) {
            $this->error('Unable to read recipients file.');

            return self::FAILURE;
        }

        $headers = fgetcsv($file);

        if (!$headers) {
            fclose($file);
            $this->error('CSV file is empty.');

            return self::FAILURE;
        }

        // Handle a possible UTF-8 BOM in the first column.
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $emailIndex = array_search('email', array_map('trim', $headers));

        if ($emailIndex === false) {
            fclose($file);
            $this->error('CSV must contain an email column.');

            return self::FAILURE;
        }

        $sent = 0;
        $failed = 0;

        while (($row = fgetcsv($file)) !== false) {
            $email = trim($row[$emailIndex] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->warn("Invalid email skipped: {$email}");
                $failed++;
                continue;
            }

            $this->info("Sending consultation email to {$email}...");

            try {
                Mail::to($email)->send(
                    new BulkEmail($policyPaths)
                );

                $this->info('  ✓ Sent');
                $sent++;
            } catch (\Throwable $e) {
                $this->error("  ✗ Failed: {$e->getMessage()}");
                $failed++;
            }
        }

        fclose($file);

        $this->newLine();
        $this->info("Emails sent: {$sent}");
        $this->info("Failed or skipped: {$failed}");

        return $failed > 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
