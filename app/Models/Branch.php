<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionInfo;
use App\Models\Employee;
use App\Models\Company;
use App\Models\RequestType;
use App\Models\Signatory;
use App\Models\User;
use App\Models\EmployeePosition;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name',
        'branch_address',
        'branch_email',
        'branch_code',
        'branch_type',
        'company_id',
        'branch_cell',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->hasMany(Employee::class);
    }

    public function requisitionInfos()
    {
        return $this->hasMany(RequisitionInfo::class, 'from_branch_id');
    }

    public function employeePositions(){
        return $this->hasMany(EmployeePosition::class, 'branch_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function signatories()
    {
        return $this->hasMany(Signatory::class, 'branch_id');
    }

    //has many branch setting configuration
    public function branchSettings()
    {
        return $this->hasMany(BranchSettingConfig::class, 'branch_id');
    }

    public function getBranchSettingConfig($settingName)
    {
        //get program setting id
        $programSetting = ProgramSetting::where('setting_name', $settingName)->first();
        
        if($programSetting) {
            $setting = BranchSettingConfig::where([['setting_id', $programSetting->id],['branch_id',auth()->user()->branch_id]])->first();
            if($setting) {
                return $setting->value;
            }
            return 0;
        }
        
    }

    public function banquetEvents()
    {
        return $this->hasMany(BanquetEvent::class, 'branch_id');
    }
}
