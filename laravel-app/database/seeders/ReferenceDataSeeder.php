<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    /**
     * Seed default reference data: departments, asset categories, ticket categories.
     * Uses updateOrCreate to be safely re-runnable.
     */
    public function run(): void
    {
        $this->seedDepartments();
        $this->seedAssetCategories();
        $this->seedTicketCategories();
    }

    private function seedDepartments(): void
    {
        $departments = [
            ['name' => 'Direksi',              'code' => 'DIR',  'description' => 'Jajaran direksi dan pimpinan perusahaan',       'sort_order' => 1],
            ['name' => 'Human Resource',       'code' => 'HRD',  'description' => 'Pengelolaan SDM, rekrutmen, dan administrasi kepegawaian', 'sort_order' => 2],
            ['name' => 'Keuangan & Akuntansi', 'code' => 'FIN',  'description' => 'Pengelolaan keuangan, pembukuan, dan perpajakan', 'sort_order' => 3],
            ['name' => 'Teknologi Informasi',  'code' => 'IT',   'description' => 'Pengembangan sistem, infrastruktur IT, dan dukungan teknis', 'sort_order' => 4],
            ['name' => 'Marketing',            'code' => 'MKT',  'description' => 'Pemasaran, branding, dan komunikasi perusahaan', 'sort_order' => 5],
            ['name' => 'Sales',                'code' => 'SLS',  'description' => 'Penjualan dan pengembangan bisnis',              'sort_order' => 6],
            ['name' => 'Operasional',          'code' => 'OPS',  'description' => 'Operasional harian dan manajemen proses bisnis', 'sort_order' => 7],
            ['name' => 'Produksi',             'code' => 'PRD',  'description' => 'Proses produksi dan quality control',            'sort_order' => 8],
            ['name' => 'Logistik & Gudang',    'code' => 'LOG',  'description' => 'Pengelolaan gudang, pengiriman, dan inventaris', 'sort_order' => 9],
            ['name' => 'Customer Service',     'code' => 'CS',   'description' => 'Layanan pelanggan dan penanganan keluhan',       'sort_order' => 10],
            ['name' => 'Legal & Compliance',   'code' => 'LGL',  'description' => 'Urusan hukum, perizinan, dan kepatuhan regulasi', 'sort_order' => 11],
            ['name' => 'General Affairs',      'code' => 'GA',   'description' => 'Urusan umum, fasilitas kantor, dan keamanan',    'sort_order' => 12],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['code' => $dept['code']],
                array_merge($dept, ['is_active' => true])
            );
        }
    }

    private function seedAssetCategories(): void
    {
        $categories = [
            [
                'name' => 'Laptop & Komputer',
                'code' => 'IT-COMP',
                'description' => 'Laptop, desktop, all-in-one PC',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 4,
            ],
            [
                'name' => 'Monitor & Display',
                'code' => 'IT-MON',
                'description' => 'Monitor, proyektor, TV display',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 5,
            ],
            [
                'name' => 'Perangkat Jaringan',
                'code' => 'IT-NET',
                'description' => 'Router, switch, access point, server',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 5,
            ],
            [
                'name' => 'Printer & Scanner',
                'code' => 'IT-PRT',
                'description' => 'Printer, scanner, mesin fotokopi',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 5,
            ],
            [
                'name' => 'Handphone & Tablet',
                'code' => 'IT-MOB',
                'description' => 'Smartphone, tablet perusahaan',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 3,
            ],
            [
                'name' => 'Meja & Kursi',
                'code' => 'FRN-DSK',
                'description' => 'Meja kerja, kursi kantor, meja meeting',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 8,
            ],
            [
                'name' => 'Lemari & Rak',
                'code' => 'FRN-CAB',
                'description' => 'Lemari arsip, rak buku, loker karyawan',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 10,
            ],
            [
                'name' => 'Kendaraan Operasional',
                'code' => 'VHC',
                'description' => 'Mobil, motor, kendaraan dinas',
                'depreciation_method' => 'declining_balance',
                'useful_life_years' => 8,
            ],
            [
                'name' => 'Peralatan Kantor',
                'code' => 'OFC',
                'description' => 'AC, dispenser, microwave, CCTV, mesin absensi',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 5,
            ],
            [
                'name' => 'Peralatan Produksi',
                'code' => 'PRD',
                'description' => 'Mesin produksi, alat berat, peralatan pabrik',
                'depreciation_method' => 'declining_balance',
                'useful_life_years' => 10,
            ],
            [
                'name' => 'Software & Lisensi',
                'code' => 'SW',
                'description' => 'Lisensi software, subscription tahunan',
                'depreciation_method' => 'straight_line',
                'useful_life_years' => 3,
            ],
            [
                'name' => 'Lainnya',
                'code' => 'OTH',
                'description' => 'Aset yang tidak termasuk kategori di atas',
                'depreciation_method' => 'none',
                'useful_life_years' => null,
            ],
        ];

        foreach ($categories as $cat) {
            AssetCategory::updateOrCreate(
                ['code' => $cat['code']],
                $cat
            );
        }
    }

    private function seedTicketCategories(): void
    {
        $categories = [
            [
                'name' => 'IT Support',
                'code' => 'IT-SUP',
                'description' => 'Masalah komputer, jaringan, email, akses sistem',
                'default_priority' => 'medium',
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 24,
            ],
            [
                'name' => 'HR & Kepegawaian',
                'code' => 'HR',
                'description' => 'Pertanyaan gaji, cuti, surat keterangan, data karyawan',
                'default_priority' => 'medium',
                'sla_response_hours' => 8,
                'sla_resolution_hours' => 48,
            ],
            [
                'name' => 'Keuangan & Reimbursement',
                'code' => 'FIN',
                'description' => 'Klaim reimbursement, pertanyaan slip gaji, pajak',
                'default_priority' => 'medium',
                'sla_response_hours' => 8,
                'sla_resolution_hours' => 72,
            ],
            [
                'name' => 'Fasilitas & Gedung',
                'code' => 'GA',
                'description' => 'AC rusak, lampu mati, kebersihan, parkir, keamanan',
                'default_priority' => 'low',
                'sla_response_hours' => 12,
                'sla_resolution_hours' => 72,
            ],
            [
                'name' => 'Permintaan Akses',
                'code' => 'ACC',
                'description' => 'Request akses sistem, VPN, folder, aplikasi',
                'default_priority' => 'high',
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 24,
            ],
            [
                'name' => 'Pengadaan Barang',
                'code' => 'PROC',
                'description' => 'Request pembelian ATK, peralatan, perlengkapan kerja',
                'default_priority' => 'low',
                'sla_response_hours' => 24,
                'sla_resolution_hours' => 120,
            ],
            [
                'name' => 'Keluhan & Saran',
                'code' => 'FEEDBACK',
                'description' => 'Keluhan umum, saran perbaikan, masukan untuk perusahaan',
                'default_priority' => 'low',
                'sla_response_hours' => 24,
                'sla_resolution_hours' => 168,
            ],
            [
                'name' => 'Darurat / Urgent',
                'code' => 'URGENT',
                'description' => 'Masalah kritis yang membutuhkan penanganan segera',
                'default_priority' => 'critical',
                'sla_response_hours' => 1,
                'sla_resolution_hours' => 8,
            ],
        ];

        foreach ($categories as $cat) {
            TicketCategory::updateOrCreate(
                ['code' => $cat['code']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}
