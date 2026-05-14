<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportImage;
use App\Models\StatusHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            [
                'user_id' => 2,
                'title' => 'تراكم نفايات في شارع التحرير',
                'description' => 'يوجد تراكم كبير للنفايات في شارع التحرير بالقرب من محطة المترو. الرائحة كريهة وتحتاج لإزالة فورية.',
                'severity' => 'high',
                'city_id' => 1,
                'category_id' => 1,
                'latitude' => 30.0458,
                'longitude' => 31.2361,
                'address' => 'شارع التحرير، وسط البلد، القاهرة',
                'status' => 'resolved',
                'tracking_token' => Str::random(16),
                'assigned_company_id' => 1,
                'assigned_at' => now()->subDays(5),
                'started_at' => now()->subDays(4),
                'resolved_at' => now()->subDays(2),
                'upvotes_count' => 12,
            ],
            [
                'user_id' => 3,
                'title' => 'مخلفات بناء في حي الدقي',
                'description' => 'مخلفات بناء متناثرة في حي الدقي منذ أسبوعين. تعيق حركة المشاة والسيارات.',
                'severity' => 'medium',
                'city_id' => 3,
                'category_id' => 2,
                'latitude' => 30.0385,
                'longitude' => 31.2123,
                'address' => 'شارع السودان، الدقي، الجيزة',
                'status' => 'assigned',
                'tracking_token' => Str::random(16),
                'assigned_company_id' => 3,
                'assigned_at' => now()->subDays(1),
                'upvotes_count' => 5,
            ],
            [
                'user_id' => 4,
                'title' => 'نفايات طبية مهملة بالإسكندرية',
                'description' => 'أكياس حمراء (نفايات طبية) ملقاة في شارع فرعي بالإسكندرية. خطر على الصحة العامة.',
                'severity' => 'critical',
                'city_id' => 2,
                'category_id' => 3,
                'latitude' => 31.1985,
                'longitude' => 29.9152,
                'address' => 'شارع فؤاد، سيدي جابر، الإسكندرية',
                'status' => 'in_progress',
                'tracking_token' => Str::random(16),
                'assigned_company_id' => 2,
                'assigned_at' => now()->subDays(2),
                'started_at' => now()->subDay(),
                'upvotes_count' => 23,
            ],
            [
                'user_id' => 2,
                'title' => 'زجاجات بلاستيك في النيل',
                'description' => 'كميات كبيرة من الزجاجات البلاستيكية العائمة في فرع النيل بالقاهرة.',
                'severity' => 'medium',
                'city_id' => 1,
                'category_id' => 4,
                'latitude' => 30.0520,
                'longitude' => 31.2250,
                'address' => 'كورنيش النيل، القاهرة',
                'status' => 'pending',
                'tracking_token' => Str::random(16),
                'upvotes_count' => 8,
            ],
            [
                'user_id' => 5,
                'title' => 'زيت مهمل في مصنع قديم',
                'description' => 'برميل زيت صناعي مهمل في مصنع مهجور. خطر تسرب للتربة.',
                'severity' => 'high',
                'city_id' => 1,
                'category_id' => 5,
                'latitude' => 30.0650,
                'longitude' => 31.2500,
                'address' => 'المنطقة الصناعية، حلوان، القاهرة',
                'status' => 'rejected',
                'tracking_token' => Str::random(16),
                'rejection_reason' => 'الموقع خارج نطاق الخدمة الحالي',
                'upvotes_count' => 2,
            ],
        ];

        foreach ($reports as $reportData) {
            $report = Report::create($reportData);

            // Status History
            StatusHistory::create([
                'report_id' => $report->id,
                'from_status' => 'pending',
                'to_status' => $report->status,
                'changed_by' => 1, // system/admin
                'note' => $report->status === 'pending' ? 'Report submitted' : 'Auto-assigned by system',
            ]);

            // Images (placeholder)
            ReportImage::create([
                'report_id' => $report->id,
                'image_path' => 'reports/' . $report->id . '/placeholder.jpg',
                'type' => 'before',
            ]);
        }
    }
}