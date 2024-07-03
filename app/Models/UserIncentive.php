<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIncentive extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_incentives';
    protected $fillable = [
        'user_id',
        'bo_name',
        'bo_email',
        'headquarter',
        'april_may_june_target',
        'july_aug_sept_target',
        'oct_nov_dec_target',
        'april_may_june_incentive',
        'july_aug_sept_incentive',
        'oct_nov_dec_incentive'
    ];

    public function user()
    {
        return $this->belongsTo(ExcelUserOps::class, 'user_id');
    }
    
    public function hardDelete()
    {
        return parent::delete();
    }

}
