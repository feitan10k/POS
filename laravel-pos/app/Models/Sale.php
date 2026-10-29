<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model{protected $guarded=[];public function items(){return $this->hasMany(SaleItem::class);}public function cashier(){return $this->belongsTo(User::class,'user_id');}}
