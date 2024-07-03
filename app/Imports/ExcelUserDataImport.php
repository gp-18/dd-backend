<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\ExcelUserOps;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelUserDataImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {

        foreach ($collection as $row) {
            $userData = ExcelUserOps::where('bo_email', $row['bo_email'])->first();

            if ($userData) {
                $userData->update([
                    'bo_name' => $row['bo_name'],
                    'abm_name' => $row['abm_name'],
                    'rsm_name' => $row['rsm_name'],
                    'nsm_name' => $row['nsm_name'],
                    'gpm_name' => $row['gpm_name'],
                    'bo_email' => $row['bo_email'],
                    'abm_email' => $row['abm_email'],
                    'rsm_email' => $row['rsm_email'],
                    'nsm_email' => $row['nsm_email'],
                    'gpm_email' => $row['gpm_email']
                ]);
            } else {
                ExcelUserOps::create([
                    'bo_name' => $row['bo_name'],
                    'abm_name' => $row['abm_name'],
                    'rsm_name' => $row['rsm_name'],
                    'nsm_name' => $row['nsm_name'],
                    'gpm_name' => $row['gpm_name'],
                    'bo_email' => $row['bo_email'],
                    'abm_email' => $row['abm_email'],
                    'rsm_email' => $row['rsm_email'],
                    'nsm_email' => $row['nsm_email'],
                    'gpm_email' => $row['gpm_email']
                ]);
            }
        }
    }
}
