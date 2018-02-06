<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;
    protected $revisionEnabled = true;
    protected $revisionCleanup = true; 
    protected $historyLimit = 500; 

    protected $guarded = [];
    protected $dates = ['datesynched', 'dob'];

    // protected $dateFormat = 'Y-m-d';

    public function sample()
    {
    	return $this->hasMany('App\Sample');
    }

    public function mother()
    {
    	return $this->belongsTo('App\Mother');
    }
}
