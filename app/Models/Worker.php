<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',

        'surname',
        'forename',
        'title',
        'date_of_birth',
        'nationality',

        'email',
        'mobile_phone',
        'home_phone',

        'address1',
        'address2',
        'city',
        'county',
        'postcode',
        'country',

        'ni_number',

        'account_no',
        'sort_code',
        'bank_name',
        'branch',
        'bs_ref',

        'job_title',
        'end_client',
        'start_date',

        'sharecode',
        'external_id',
        'signify',
        'venatu',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
