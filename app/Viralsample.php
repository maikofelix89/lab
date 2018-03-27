<?php

namespace App;

use App\BaseModel;

class Viralsample extends BaseModel
{
    // protected $dates = ['datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'dateinitiatedontreatment', 'datesynched'];

    public function getDayCollectedAttribute()
    {
        return $this->date_modifier($this->datecollected);
    }

    public function getDayTestedAttribute()
    {
        return $this->date_modifier($this->datetested);
    }

    public function getDayModifiedAttribute()
    {
        return $this->date_modifier($this->datemodified);
    }

    public function getDayApprovedAttribute()
    {
        return $this->date_modifier($this->dateapproved);
    }


    public function patient()
    {
    	return $this->belongsTo('App\Viralpatient', 'patient_id');
    }

    public function batch()
    {
        return $this->belongsTo('App\Viralbatch', 'batch_id');
    }

    public function worksheet()
    {
        return $this->belongsTo('App\Viralworksheet', 'worksheet_id');
    }


    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\Viralsample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\Viralsample', 'parentid');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'createdby');
    }

    public function canceller()
    {
        return $this->belongsTo('App\User', 'cancelledby');
    }

    public function reviewer()
    {
        return $this->belongsTo('App\User', 'reviewedby');
    }

    public function approver()
    {
        return $this->belongsTo('App\User', 'approvedby');
    }
}
