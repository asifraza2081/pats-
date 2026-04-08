<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    protected $fillable = ['name', 'type', 'description', 'is_system'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function scopeRevenue($query)  { return $query->where('type', 'revenue'); }
    public function scopeExpense($query)  { return $query->where('type', 'expense'); }
    public function scopeDeletable($query) { return $query->where('is_system', false); }
}
