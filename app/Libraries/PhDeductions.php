<?php

namespace App\Libraries;

/**
 * Philippine Statutory Deductions Calculator
 *
 * Rates based on:
 *  - SSS        : 2025 schedule – 14% total, employee share = 4.5%, MSC cap ₱35,000
 *  - PhilHealth : 2025 rate – 5% total, employee share = 2.5%, floor ₱250, cap ₱2,500
 *  - Pag-IBIG   : ₱1,500 threshold rule, employee max ₱200/month
 *  - Withholding: TRAIN Law (RA 10963) 2023+ monthly BIR tax table
 */
class PhDeductions
{
    // ─────────────────────────────────────────────
    // SSS (2025) – Employee share 4.5% of MSC
    // MSC floor ₱4,000 | cap ₱35,000 | step ₱500
    // ─────────────────────────────────────────────
    public static function computeSSS(float $monthlySalary): float
    {
        // Salary range brackets → Monthly Salary Credit (MSC)
        // Below ₱4,250 → MSC ₱4,000
        // ₱4,250 to ₱4,749.99 → MSC ₱4,500
        // … each ₱500 range increments MSC by ₱500
        // ₱34,750+ → MSC ₱35,000

        if ($monthlySalary < 4250) {
            $msc = 4000;
        } elseif ($monthlySalary >= 34750) {
            $msc = 35000;
        } else {
            // Round to nearest ₱500 bracket
            // Lower bound of bracket = floor((salary - 250) / 500) * 500 + 4000 + 500
            // Simpler: MSC = ceil((salary - 3999) / 500) * 500 + 3500 — let's compute directly
            $msc = (floor(($monthlySalary - 3750) / 500) * 500) + 4000;
        }

        $employeeShare = $msc * 0.045; // 4.5%
        return round($employeeShare, 2);
    }

    // ─────────────────────────────────────────────
    // PhilHealth (2025) – 5% total, employee 2.5%
    // Floor ₱250 (salary ≤ ₱10,000)
    // Cap ₱2,500 (salary ≥ ₱100,000)
    // ─────────────────────────────────────────────
    public static function computePhilHealth(float $monthlySalary): float
    {
        $contribution = $monthlySalary * 0.025;
        $contribution = max(250, min($contribution, 2500));
        return round($contribution, 2);
    }

    // ─────────────────────────────────────────────
    // Pag-IBIG (HDMF)
    // Salary ≤ ₱1,500 → 1% | > ₱1,500 → 2%
    // Employee maximum = ₱200/month
    // ─────────────────────────────────────────────
    public static function computePagIbig(float $monthlySalary): float
    {
        $rate = ($monthlySalary <= 1500) ? 0.01 : 0.02;
        $contribution = $monthlySalary * $rate;
        return round(min($contribution, 200), 2);
    }

    // ─────────────────────────────────────────────
    // BIR Withholding Tax – TRAIN Law 2023+ Monthly
    // Taxable income = gross - SSS - PhilHealth - Pag-IBIG
    // ─────────────────────────────────────────────
    public static function computeWithholdingTax(
        float $monthlySalary,
        float $sss,
        float $philhealth,
        float $pagibig
    ): float {
        $taxable = $monthlySalary - $sss - $philhealth - $pagibig;

        if ($taxable <= 0) {
            return 0;
        }

        // TRAIN Law 2023+ monthly tax brackets
        if ($taxable <= 20833) {
            $tax = 0;
        } elseif ($taxable <= 33332) {
            $tax = ($taxable - 20833) * 0.20;
        } elseif ($taxable <= 66666) {
            $tax = 2500 + ($taxable - 33333) * 0.25;
        } elseif ($taxable <= 166666) {
            $tax = 10833 + ($taxable - 66667) * 0.30;
        } elseif ($taxable <= 666666) {
            $tax = 40833 + ($taxable - 166667) * 0.32;
        } else {
            $tax = 200833 + ($taxable - 666667) * 0.35;
        }

        return round(max($tax, 0), 2);
    }

    // ─────────────────────────────────────────────
    // Convenience: compute all deductions at once
    // Returns array with all components + totals
    // ─────────────────────────────────────────────
    public static function compute(float $monthlySalary, float $allowances = 0): array
    {
        $gross       = $monthlySalary + $allowances;
        $sss         = self::computeSSS($monthlySalary);
        $philhealth  = self::computePhilHealth($monthlySalary);
        $pagibig     = self::computePagIbig($monthlySalary);
        $withheld    = self::computeWithholdingTax($monthlySalary, $sss, $philhealth, $pagibig);

        $totalDeductions = $sss + $philhealth + $pagibig + $withheld;
        $net             = $gross - $totalDeductions;

        return [
            'gross'               => round($gross, 2),
            'sss'                 => $sss,
            'philhealth'          => $philhealth,
            'pagibig'             => $pagibig,
            'withholding_tax'     => $withheld,
            'total_deductions'    => round($totalDeductions, 2),
            'net_salary'          => round($net, 2),
        ];
    }
}
