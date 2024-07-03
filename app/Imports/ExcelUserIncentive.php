<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\ExcelUserOps;
use App\Models\UserIncentive;

class ExcelUserIncentive implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        $firstRow = true;
        foreach ($collection as $row) {
            if ($firstRow) {
                $firstRow = false; 
                continue;
            }

            $user = ExcelUserOps::where('bo_email', $row[1])->first();

            if (!$user) {
                throw new \Exception("User not found bo_email: " . $row[1]);
            }

            $userIncentive = UserIncentive::where('user_id', $user->id)->first();
            
            if (!$userIncentive) {
                $userIncentive = new UserIncentive();
            }

            $userIncentive->user_id = $user->id;
            $userIncentive->bo_name = $row[0];
            $userIncentive->bo_email = $row[1];
            $userIncentive->headquarter = $row[2];
            $userIncentive->april_may_june_target = $row[3];
            $userIncentive->july_aug_sept_target = $row[4];
            $userIncentive->oct_nov_dec_target = $row[5];
            $userIncentive->april_may_june_incentive = $row[6];
            $userIncentive->july_aug_sept_incentive = $row[7];
            $userIncentive->oct_nov_dec_incentive = $row[8];

            $userIncentive->save();
        }
    }
}
