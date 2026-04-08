<?php

namespace Database\Seeders;

use App\Models\FinancialCategory;
use App\Models\FinancialSetting;
use Illuminate\Database\Seeder;

class FinancialCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // ── Revenue Categories ───────────────────────────────────────────────
        $revenues = [
            ['name' => 'Application Fee Revenue', 'description' => 'Fees collected from candidates'],
            ['name' => 'Other Revenue',            'description' => 'Miscellaneous income'],
        ];

        foreach ($revenues as $cat) {
            FinancialCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'revenue'],
                ['description' => $cat['description'], 'is_system' => true]
            );
        }

        // ── Expense Categories ───────────────────────────────────────────────
        $expenses = [
            ['name' => 'Exam Venue Rent',          'description' => 'Test center hall/room rental'],
            ['name' => 'Staff Honorarium',         'description' => 'Invigilators, coordinators, data entry'],
            ['name' => 'Printing & Stationery',    'description' => 'Question papers, forms, answer sheets'],
            ['name' => 'IT & Software',            'description' => 'Software licenses, server costs'],
            ['name' => 'Utility Bills',            'description' => 'Electricity, generator, water'],
            ['name' => 'Transport & Logistics',    'description' => 'Courier, transport, fuel'],
            ['name' => 'Advertisement & PR',       'description' => 'Newspaper, digital ads, banners'],
            ['name' => 'Professional Services',    'description' => 'Legal, audit, consultancy fees'],
            ['name' => 'Office Administration',    'description' => 'General admin and office expenses'],
            ['name' => 'Miscellaneous',            'description' => 'Other uncategorized expenses'],
        ];

        foreach ($expenses as $cat) {
            FinancialCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'expense'],
                ['description' => $cat['description'], 'is_system' => false]
            );
        }

        // ── Default Settings ─────────────────────────────────────────────────
        $settings = [
            ['key' => 'default_gst_rate',    'value' => '15',    'description' => 'Default GST/WHT rate (%) applied to new expenses'],
            ['key' => 'fbr_mode_enabled',    'value' => '0',     'description' => 'Enable FBR Pakistan advanced tax compliance features'],
            ['key' => 'fiscal_year_start',   'value' => '07',    'description' => 'Fiscal year start month (07 = July, Pakistan standard)'],
            ['key' => 'org_ntn',             'value' => '',      'description' => 'Organisation NTN number for FBR filing'],
            ['key' => 'org_strn',            'value' => '',      'description' => 'Organisation Sales Tax Registration Number'],
            ['key' => 'org_name_for_tax',    'value' => 'Prime Assessment & Testing Services', 'description' => 'Legal name used on tax documents'],
            ['key' => 'org_address_for_tax', 'value' => '',      'description' => 'Registered address used on tax documents'],
        ];

        foreach ($settings as $setting) {
            FinancialSetting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'description' => $setting['description']]
            );
        }
    }
}
