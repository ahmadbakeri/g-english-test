<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'nisn',
        'competition_code',
        'student_type',
        'school_user_id',
        'name',
        'email',
        'phone',
        'level',
        'birth_date',
        'address',
        'previous_school',
        'mother_name',
        'mother_phone',
        'mother_email',
        'mother_job',
        'father_name',
        'father_phone',
        'father_email',
        'father_job',
        'status',
        'tuition_fee',
        'registration_fee',
        'has_discount',
        'total_fee',
        'payment_method',
        'amount_paid',
        'notes',
        'parent_name',
        'parent_phone',
        'birth_place',
        'gender',
        'religion',
        'social_media',
        'education_level',
        'course_level',
        'class_name',
        'school_name',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->registration_number)) {
                $latest = static::latest('id')->first();
                $nextId = $latest ? $latest->id + 1 : 1;
                $model->registration_number = 'REG-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
