<?php

namespace App\Imports;

use App\Models\Partner;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PartnerImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    protected $category_id;

    public function __construct($category_id)
    {
        $this->category_id = $category_id;
    }

    public function model(array $row)
    {

       if (!isset($row['pro_name']) || trim($row['pro_name']) === '' || !isset($row['phone_number']) || trim($row['phone_number']) === '') {
            return null;
        }

        if (Partner::where('phone_number', $row['phone_number'])->exists()) {
            return null; // Skip this row
        }

        return new Partner([
            'category_id' => $this->category_id,
            'pro_name' => $row['pro_name'],
            'phone_number' => $row['phone_number'],
            'city' => $row['city'] ?? null,
            'amount_paid' => $row['amount_paid'] ?? null,
            'category' => $row['category'] ?? null,
            'pending_amount' => $row['pending_amount'] ?? null,
            'payment_gateway' => $row['payment_gateway'] ?? null,
            'hub' => $row['hub'] ?? null,
            'tshirt_cap' => $row['tshirt_cap'] ?? null,
            'source' => $row['source'] ?? null,
            'status' => $row['status'] ?? null,
        ]);
    }
}
