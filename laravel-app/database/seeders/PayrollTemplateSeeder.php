<?php

namespace Database\Seeders;

use App\Models\PayrollTemplate;
use App\Models\PayrollTemplateItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PayrollTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $configPath = config_path('payroll-templates');
        $files = File::glob($configPath . '/*.php');

        foreach ($files as $file) {
            $config = require $file;

            $template = PayrollTemplate::updateOrCreate(
                ['slug' => $config['slug']],
                [
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'industry_type' => $config['industry_type'],
                    'company_size' => $config['company_size'],
                    'icon' => $config['icon'],
                    'sort_order' => $config['sort_order'],
                    'includes_bpjs' => $config['includes_bpjs'],
                    'includes_pph21' => $config['includes_pph21'],
                    'includes_overtime' => $config['includes_overtime'],
                    'component_count' => count($config['items']),
                    'is_active' => true,
                    'is_system' => true,
                ]
            );

            foreach ($config['items'] as $itemData) {
                PayrollTemplateItem::updateOrCreate(
                    [
                        'payroll_template_id' => $template->id,
                        'code' => $itemData['code'],
                    ],
                    [
                        'name' => $itemData['name'],
                        'type' => $itemData['type'],
                        'category' => $itemData['category'],
                        'value_type' => $itemData['value_type'],
                        'formula' => $itemData['formula'],
                        'default_amount' => $itemData['default_amount'],
                        'is_taxable' => $itemData['is_taxable'],
                        'is_fixed' => $itemData['is_fixed'],
                        'is_prorated' => $itemData['is_prorated'],
                        'prorate_basis' => $itemData['prorate_basis'],
                        'execution_order' => $itemData['execution_order'],
                        'depends_on' => $itemData['depends_on'],
                        'min_value' => $itemData['min_value'] ?? null,
                        'max_value' => $itemData['max_value'] ?? null,
                        'description' => $itemData['description'] ?? null,
                        'help_text' => $itemData['help_text'] ?? null,
                        'is_required' => $itemData['is_required'],
                        'is_optional' => $itemData['is_optional'],
                        'group_label' => $itemData['group_label'],
                        'sort_within_group' => $itemData['sort_within_group'],
                    ]
                );
            }
        }
    }
}
