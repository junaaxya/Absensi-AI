<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;

class AssetService
{
    public function assignAsset(Asset $asset, User $user, User $assignedBy, ?string $notes = null): AssetAssignment
    {
        $assignment = AssetAssignment::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
            'condition_on_assign' => $asset->condition,
            'notes' => $notes,
        ]);

        $asset->update([
            'assigned_to' => $user->id,
            'assigned_at' => now(),
        ]);

        return $assignment;
    }

    public function returnAsset(Asset $asset, string $conditionOnReturn, ?string $notes = null): AssetAssignment
    {
        $assignment = AssetAssignment::where('asset_id', $asset->id)
            ->whereNull('returned_at')
            ->latest('assigned_at')
            ->firstOrFail();

        $assignment->update([
            'returned_at' => now(),
            'condition_on_return' => $conditionOnReturn,
            'notes' => $notes ? ($assignment->notes ? $assignment->notes . ' | ' . $notes : $notes) : $assignment->notes,
        ]);

        $asset->update([
            'assigned_to' => null,
            'assigned_at' => null,
            'condition' => $conditionOnReturn,
        ]);

        return $assignment;
    }

    public function calculateDepreciation(Asset $asset): float
    {
        $category = $asset->category;

        if (!$category || $category->depreciation_method === 'none' || !$category->useful_life_years) {
            return (float) $asset->purchase_price;
        }

        $ageInYears = $asset->purchase_date->diffInDays(now()) / 365.25;
        $usefulLife = $category->useful_life_years;
        $purchasePrice = (float) $asset->purchase_price;
        $salvageValue = $purchasePrice * 0.05; // 5% salvage value

        if ($category->depreciation_method === 'straight_line') {
            $annualDepreciation = ($purchasePrice - $salvageValue) / $usefulLife;
            $totalDepreciation = $annualDepreciation * min($ageInYears, $usefulLife);
            return max($salvageValue, $purchasePrice - $totalDepreciation);
        }

        if ($category->depreciation_method === 'declining_balance') {
            $rate = 2 / $usefulLife; // Double declining balance
            $currentValue = $purchasePrice;
            $fullYears = (int) floor($ageInYears);

            for ($i = 0; $i < min($fullYears, $usefulLife); $i++) {
                $depreciation = $currentValue * $rate;
                $currentValue -= $depreciation;
                if ($currentValue < $salvageValue) {
                    $currentValue = $salvageValue;
                    break;
                }
            }

            return max($salvageValue, $currentValue);
        }

        return $purchasePrice;
    }

    public function getDepreciationSchedule(Asset $asset): array
    {
        $category = $asset->category;

        if (!$category || $category->depreciation_method === 'none' || !$category->useful_life_years) {
            return [];
        }

        $usefulLife = $category->useful_life_years;
        $purchasePrice = (float) $asset->purchase_price;
        $salvageValue = $purchasePrice * 0.05;
        $schedule = [];

        if ($category->depreciation_method === 'straight_line') {
            $annualDepreciation = ($purchasePrice - $salvageValue) / $usefulLife;
            $bookValue = $purchasePrice;

            for ($year = 0; $year <= $usefulLife; $year++) {
                $schedule[] = [
                    'year' => $year,
                    'date' => $asset->purchase_date->copy()->addYears($year)->format('Y'),
                    'depreciation' => $year === 0 ? 0 : round($annualDepreciation, 2),
                    'accumulated' => $year === 0 ? 0 : round($annualDepreciation * $year, 2),
                    'book_value' => round(max($salvageValue, $bookValue - ($annualDepreciation * $year)), 2),
                ];
            }
        }

        if ($category->depreciation_method === 'declining_balance') {
            $rate = 2 / $usefulLife;
            $bookValue = $purchasePrice;
            $accumulated = 0;

            $schedule[] = [
                'year' => 0,
                'date' => $asset->purchase_date->format('Y'),
                'depreciation' => 0,
                'accumulated' => 0,
                'book_value' => round($bookValue, 2),
            ];

            for ($year = 1; $year <= $usefulLife; $year++) {
                $depreciation = $bookValue * $rate;
                $bookValue -= $depreciation;

                if ($bookValue < $salvageValue) {
                    $depreciation = $bookValue + $depreciation - $salvageValue;
                    $bookValue = $salvageValue;
                }

                $accumulated += $depreciation;

                $schedule[] = [
                    'year' => $year,
                    'date' => $asset->purchase_date->copy()->addYears($year)->format('Y'),
                    'depreciation' => round($depreciation, 2),
                    'accumulated' => round($accumulated, 2),
                    'book_value' => round($bookValue, 2),
                ];
            }
        }

        return $schedule;
    }
}
