<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\MenuItem;

class Order extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function change1ToYes0ToNoServed()
    {
        if ($this->served == 1) {
            return 'ja';
        } else {
            return 'nee';
        }
    }

    public function change1ToYes0ToNoReady()
    {
        if ($this->ready == 1) {
            return 'ja';
        } else {
            return 'nee';
        }
    }

    public function showMenuItemName()
    {
        return $this->menu_item->name;
    }

    public function orderReady()
    {
        return  '<a class="btn btn-sm btn-link" href="' . url('admin/order/chef/' . $this->id) . '/edit' . '"><i class="la la-check"></i> Bestelling klaar?</a>';
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function menu_item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
