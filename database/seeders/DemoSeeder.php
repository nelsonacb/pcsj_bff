<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Citizens\Domain\CitizenStatus;
use App\Modules\Citizens\Infrastructure\Models\Citizen;
use App\Modules\Requests\Domain\DraftType;
use App\Modules\Requests\Infrastructure\Models\PortalAttachment;
use App\Modules\Requests\Infrastructure\Models\PortalDraft;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    private const PASSWORD = 'Demo12345678!';

    public function run(): void
    {
        $this->command->info('🌱 Creando datos de demo...');

        // ============================================================
        // 1. CIUDADANOS (CIs y emails nuevos, sin colisión)
        // ============================================================
        $juan  = $this->createCitizen('55031212345', 'juan@demo.pcsj.cu',  'Juan Pérez García',      '+5355551111', true);
        $maria = $this->createCitizen('60071812345', 'maria@demo.pcsj.cu', 'María López Fernández',  '+5355552222', true);
        $pedro = $this->createCitizen('58092512345', 'pedro@demo.pcsj.cu', 'Pedro Rodríguez Díaz',   '+5355553333', false);

        $this->command->info('  ✅ 3 ciudadanos listos');

        // ============================================================
        // 2. BORRADOR DE JUBILACIÓN AVANZADO (Juan, pasos 1-3)
        // ============================================================
        $juanDraft = PortalDraft::firstOrCreate(
            ['citizen_id' => $juan->id, 'type' => DraftType::RetirementRequest],
            [
                'payload' => [
                    // Paso 1: datos del solicitante
                    'applicant_first_name'      => 'Juan',
                    'applicant_middle_name'     => null,
                    'applicant_first_surname'   => 'Pérez',
                    'applicant_second_surname'  => 'García',
                    'applicant_sex'             => 'M',
                    'applicant_birth_date'      => '1955-03-12',
                    'applicant_race_id'         => null,
                    'applicant_father_name'     => 'Carlos Pérez',
                    'applicant_mother_name'     => 'Ana García',

                    // Paso 2: datos laborales
                    'employer_entity_id'        => 1,
                    'position_id'               => 1,
                    'occupational_category_id'  => 1,
                    'educational_level_id'      => 3,
                    'scientific_category_id'    => null,
                    'last_salary'               => 2500.00,
                    'pension_type_id'           => 1,
                    'pension_regime_id'         => 1,
                    'rebel_army_member'         => false,
                    'rebel_army_join_date'      => null,
                    'internationalist'          => false,
                    'termination_date'          => null,

                    // Paso 3: historial salarial
                    'salary_history' => [
                        ['year' => 2021, 'earned_salary' => 1800.00],
                        ['year' => 2022, 'earned_salary' => 2000.00],
                        ['year' => 2023, 'earned_salary' => 2200.00],
                        ['year' => 2024, 'earned_salary' => 2400.00],
                        ['year' => 2025, 'earned_salary' => 2500.00],
                    ],
                ],
                'last_saved_at' => now()->subHours(2),
            ]
        );

        $this->command->info('  ✅ Borrador de jubilación de Juan (pasos 1-3)');

        // ============================================================
        // 3. BORRADOR DE JUBILACIÓN INICIAL (María, solo paso 1)
        // ============================================================
        $mariaDraft = PortalDraft::firstOrCreate(
            ['citizen_id' => $maria->id, 'type' => DraftType::RetirementRequest],
            [
                'payload' => [
                    'applicant_first_name'      => 'María',
                    'applicant_first_surname'   => 'López',
                    'applicant_second_surname'  => 'Fernández',
                    'applicant_sex'             => 'F',
                    'applicant_birth_date'      => '1960-07-18',
                ],
                'last_saved_at' => now()->subDays(1),
            ]
        );

        $this->command->info('  ✅ Borrador de jubilación de María (paso 1)');

        // ============================================================
        // 4. BORRADOR DE ENTIDAD (Pedro)
        // ============================================================
        $pedroEntityDraft = PortalDraft::firstOrCreate(
            ['citizen_id' => $pedro->id, 'type' => DraftType::EntityRequest],
            [
                'payload' => [
                    'code'              => 'EMP-DEMO-001',
                    'name'              => 'Empresa de Demostración S.A.',
                    'tax_id_number'     => '12345678901',
                    'organization_id'   => 1,
                    'province_id'       => 1,
                    'municipality_id'   => 1,
                    'entity_type_id'    => 1,
                    'address'           => 'Calle 23 #456, Vedado',
                    'phone'             => '+5377771111',
                    'fax'               => null,
                    'email'             => 'contacto@empresa-demo.cu',
                    'parent_entity_id'  => null,
                    'social_purpose'    => 'Servicios de demostración',
                    'director' => [
                        'identity_number' => '65010112345',
                        'full_name'       => 'Roberto Martínez Sánchez',
                        'email'           => 'roberto@empresa-demo.cu',
                        'phone'           => '+5355559999',
                    ],
                    'economic_director' => null,
                    'authorized_signatures' => [],
                ],
                'last_saved_at' => now()->subHours(5),
            ]
        );

        $this->command->info('  ✅ Borrador de entidad de Pedro');

        // ============================================================
        // 5. ADJUNTOS DE DEMO
        // ============================================================
        $this->createDemoAttachment($juanDraft, $juan, 'carnet-juan.pdf', 'Carnet de Identidad - Juan Pérez');
        $this->createDemoAttachment($juanDraft, $juan, 'comprobante-salario-2025.pdf', 'Comprobante de salario 2025 - Juan Pérez');

        $this->command->info('  ✅ 2 adjuntos creados');

        // ============================================================
        // RESUMEN
        // ============================================================
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('🎉 DemoSeeder completado');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('Credenciales de demo:');
        $this->command->newLine();
        $this->command->info('📧 juan@demo.pcsj.cu    / Demo12345678!');
        $this->command->info('   └─ Borrador de jubilación con pasos 1-3 + 2 adjuntos');
        $this->command->newLine();
        $this->command->info('📧 maria@demo.pcsj.cu   / Demo12345678!');
        $this->command->info('   └─ Borrador de jubilación con solo paso 1');
        $this->command->newLine();
        $this->command->info('📧 pedro@demo.pcsj.cu   / Demo12345678!');
        $this->command->info('   └─ Borrador de entidad (pasos 1-2)');
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
    }

    /**
     * Crea un ciudadano si no existe (busca por email O por CI).
     * Idempotente: si ya existe, no lo duplica.
     */
    private function createCitizen(
        string $identityNumber,
        string $email,
        string $fullName,
        string $phone,
        bool $verified
    ): Citizen {
        $citizen = Citizen::where('email', $email)
            ->orWhere('identity_number', $identityNumber)
            ->first();

        if ($citizen) {
            return $citizen;
        }

        return Citizen::create([
            'identity_number'       => $identityNumber,
            'email'                 => $email,
            'password_hash'         => Hash::make(self::PASSWORD),
            'full_name'             => $fullName,
            'phone'                 => $phone,
            'person_id_sgp'         => null,
            'status'                => CitizenStatus::Active,
            'email_verified_at'     => $verified ? now() : null,
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);
    }

    /**
     * Crea un archivo PDF de demo en disco y su registro en DB.
     * Idempotente: si ya existe, no lo duplica.
     */
    private function createDemoAttachment(
        PortalDraft $draft,
        Citizen $citizen,
        string $originalName,
        string $description
    ): void {
        $exists = PortalAttachment::where('draft_id', $draft->id)
            ->where('original_name', $originalName)
            ->exists();

        if ($exists) {
            return;
        }

        $pdfContent = $this->generateMinimalPdf($description);
        $storedName = Str::uuid() . '.pdf';
        $path       = "attachments/{$citizen->id}/{$storedName}";

        Storage::disk('local')->put($path, $pdfContent);

        PortalAttachment::create([
            'draft_id'      => $draft->id,
            'request_id'    => null,
            'filename'      => $storedName,
            'original_name' => $originalName,
            'mime_type'     => 'application/pdf',
            'size'          => strlen($pdfContent),
            'storage_path'  => $path,
            'checksum'      => hash('sha256', $pdfContent),
        ]);
    }

    /**
     * Genera un PDF mínimo válido con un texto simple dentro.
     */
    private function generateMinimalPdf(string $text): string
    {
        $safeText = preg_replace('/[^\x20-\x7E]/', '', $text);

        return <<<PDF
%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>
endobj
4 0 obj
<< /Length 60 >>
stream
BT
/F1 12 Tf
72 720 Td
({$safeText}) Tj
ET
endstream
endobj
5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000300 00000 n 
0000000410 00000 n 
trailer
<< /Size 6 /Root 1 0 R >>
startxref
480
%%EOF
PDF;
    }
}