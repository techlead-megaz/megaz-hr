<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Inventory;
use App\Models\Role;
use App\Models\Staff;

class Department extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'name','inventory_id','slug','is_active'
    ];

    protected $hidden=[
        'created_at','updated_at'
    ];

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class)->where('is_available',1);
    }

    // public function itemUsageForecasts()
    // {
    //     return $this->hasMany(ItemUsageForecast::class);
    // }

    // public function inventory()
    // {
    //     return $this->morphOne(Inventory::class, 'inventoryable');
    // }

    // public function inventory()
    // {
    //     return $this->belongsTo(Inventory::class);
    // }

    // public function inventories()
    // {
    //     return $this->morphMany(Inventoryable::class, 'inventoryable');
    // }

    public function inventory()
    {
        return $this->morphOne(Inventoryable::class, 'inventoryable');
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class,'department_feature');
    }

    public static function getBySlugOrFail($slug)
    {
        $department = self::where('slug', $slug)->first();
        
        if (!$department) {
            ResponseMessage('Department not found',404);
        }

        return $department;
    }

    // public function areas()
    // {
    //     return $this->hasMany(Area::class);
    // }
}
