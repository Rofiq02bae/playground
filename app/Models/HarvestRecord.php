<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'harvest_date', 'quantity_kg', 'unit_price', 'total_revenue', 'notes', 'created_by'])]
class HarvestRecord extends Model
{
    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'quantity_kg' => 'decimal:2',
            'unit_price' => 'integer',
            'total_revenue' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(AgriculturalProject::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
