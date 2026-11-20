<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'number', 'witness1_name', 'witness2_name', 'agreement_date', 'pdf_path', 'created_by'])]
class Agreement extends Model
{
    protected function casts(): array
    {
        return [
            'agreement_date' => 'date',
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
