<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExcelUserOps extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'excel_user_ops';
    protected $fillable = [
       'bo_name' ,'abm_name', 'rsm_name' ,'nsm_name' ,'gpm_name','bo_email', 'abm_email' , 'rsm_email',
       'nsm_email','gpm_email'
    ];

    public function incentives()
    {
        return $this->hasMany(UserIncentive::class, 'user_id');
    }
    public function delete()
    {
        $this->incentives()->each(function ($incentive) {
            $incentive->delete();
        });

        return parent::delete();
    }
    public function hardDelete()
    {
        return parent::delete();
    }
    
}
