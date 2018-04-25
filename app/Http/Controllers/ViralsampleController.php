<?php

namespace App\Http\Controllers;

use App\Viralsample;
use App\Viralpatient;
use App\Viralbatch;
use App\Facility;
use App\Lookup;
use DB;

use Illuminate\Http\Request;

class ViralsampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Lookup::viralsample_form();
        return view('forms.viralsamples', $data)->with('pageTitle', 'Add Samples');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $viralsamples_arrays = Lookup::viralsamples_arrays();
        $submit_type = $request->input('submit_type');

        $batch = session('viral_batch');

        if($submit_type == "cancel"){
            $batch->premature();
            $this->clear_session();
            return redirect()->route('viralsample.create');
        }

        $highpriority = $request->input('highpriority');

        if($highpriority == 1)
        {
            $facility_id = $request->input('facility_id');

            $batch = new Viralbatch;
            $data = $request->only($viralsamples_arrays['batch']);
            $batch->fill($data);
            $batch->user_id = auth()->user()->id;
            $batch->lab_id = auth()->user()->lab_id;

            if(auth()->user()->user_type_id == 1 || auth()->user()->user_type_id == 4){
                $batch->received_by = auth()->user()->id;
                $batch->site_entry = 0;
            }

            if(auth()->user()->user_type_id == 5){
                $batch->site_entry = 1;
            }

            $batch->save();
            $message = 'The high priority sample has been saved in batch no ' . $batch->id . '.';

            session(['toast_message' => $message]);
            return redirect()->route('viralsample.create');
        }


        if(!$batch){
            $facility_id = $request->input('facility_id');
            $facility = Facility::find($facility_id);
            session(['viral_facility_name' => $facility->name, 'viral_batch_total' => 0]);

            $batch = new Viralbatch;
            $data = $request->only($viralsamples_arrays['batch']);
            $batch->fill($data);
            $batch->user_id = auth()->user()->id;
            $batch->lab_id = auth()->user()->lab_id;

            if(auth()->user()->user_type_id == 1 || auth()->user()->user_type_id == 4){
                $batch->received_by = auth()->user()->id;
                $batch->site_entry = 0;
            }

            if(auth()->user()->user_type_id == 5){
                $batch->site_entry = 1;
            }

            $batch->save();
            session(['viral_batch' => $batch]);
        }

        $new_patient = $request->input('new_patient');

        if($new_patient == 0){

            $repeat_test = Viralsample::where(['patient_id' => $request->input('patient_id'),
            'batch_id' => $batch->id])->first();

            if($repeat_test){
                session(['toast_message' => 'The sample already exists in the batch and has therefore not been saved again']);
                return redirect()->route('viralsample.create');
            }

            $data = $request->only($viralsamples_arrays['sample']);
            $viralsample = new Viralsample;
            $viralsample->fill($data);
            $viralsample->batch_id = $batch->id;
            $viralsample->age = Lookup::calculate_viralage($request->input('datecollected'), $request->input('dob'));
            $viralsample->save();
        }

        else{
            $data = $request->only($viralsamples_arrays['patient']);
            $viralpatient = new Viralpatient;
            $viralpatient->fill($data);
            $viralpatient->save();

            $data = $request->only($viralsamples_arrays['sample']);
            $viralsample = new Viralsample;
            $viralsample->fill($data);
            $viralsample->patient_id = $viralpatient->id;
            $viralsample->age = Lookup::calculate_viralage($request->input('datecollected'), $request->input('dob'));
            $viralsample->batch_id = $batch->id;
            $viralsample->save();

        }

        $submit_type = $request->input('submit_type');

        if($submit_type == "release"){
            $this->clear_session();
            $batch->premature();
        }

        $sample_count = session('viral_batch_total') + 1;
        session(['viral_batch_total' => $sample_count]);

        if($sample_count == 10){
            $this->clear_session();
            $batch->full_batch();
        }

        return redirect()->route('viralsample.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function show(Viralsample $viralsample)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function edit(Viralsample $viralsample)
    {
        $viralsample->load(['patient', 'batch']);
        $data = Lookup::viralsample_form();
        $data['viralsample'] = $viralsample;
        return view('forms.viralsamples', $data)->with('pageTitle', 'Edit Sample');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Viralsample $viralsample)
    {
        $viralsamples_arrays = Lookup::viralsamples_arrays();
        $data = $request->only($viralsamples_arrays['sample']);
        $viralsample->fill($data);

        $viralsample->age = Lookup::calculate_viralage($request->input('datecollected'), $request->input('dob'));

        $batch = Viralbatch::find($viralsample->batch_id);
        $data = $request->only($viralsamples_arrays['batch']);
        $batch->fill($data);
        $batch->pre_update();
        $batch->save();

        $data = $request->only($viralsamples_arrays['patient']);

        $new_patient = $request->input('new_patient');

        if($new_patient == 0){            
            $viralpatient = Viralpatient::find($viralsample->patient_id);
        }
        else{
            $viralpatient = new Viralpatient;
        }
        $viralpatient->fill($data);
        $viralpatient->pre_update();
        $viralpatient->save();

        $viralsample->patient_id = $viralpatient->id;
        $viralsample->pre_update();
        $viralsample->save();

        $site_entry_approval = session()->pull('site_entry_approval');

        if($site_entry_approval){
            return redirect('viralbatch/site_approval/' . $batch->id);
        }

        return redirect('viralbatch/' . $batch->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function destroy(Viralsample $viralsample)
    {
        if($viralsample->worksheet_id == NULL && $viralsample->result == NULL){
            $viralsample->delete();
        }        
        return back();
    }

    public function new_patient(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $patient = $request->input('patient');

        $viralpatient = Viralpatient::where(['facility_id' => $facility_id, 'patient' => $patient])->first();
        $data;
        if($viralpatient){
            $data[0] = 0;
            $data[1] = $viralpatient->toArray();

            $viralsample = Viralsample::select('id')->where(['patient_id' => $viralpatient->id])->where('result', '>', 1000)->first();
            if($viralsample){
                $data[2] = ['previous_nonsuppressed' => 1];
            }
            else{
                $data[2] = ['previous_nonsuppressed' => 0];
            }
        }
        else{
            $data[0] = 1;
        }
        return $data;
    }

    public function runs(Viralsample $sample)
    {
        // $samples = $sample->child;
        $samples = Viralsample::runs($sample)->orderBy('run', 'asc')->get();
        $patient = $sample->patient;
        return view('tables.sample_runs', ['patient' => $patient, 'samples' => $samples]);
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function individual(Viralsample $sample)
    {
        $batch = $sample->batch;
        $sample->load(['patient']);
        $samples[0] = $sample;
        $batch->load(['facility', 'lab', 'receiver', 'creator']);
        $data = Lookup::get_viral_lookups();
        $data['batch'] = $batch;
        $data['samples'] = $samples;

        return view('exports.viralsamples', $data)->with('pageTitle', 'Individual Samples');
    }

    public function release_redraw(Viralsample $viralsample)
    {
        $viralsample->repeatt = 0;
        $viralsample->result = "Collect New Sample";
        $viralsample->approvedby = auth()->user()->id;
        $viralsample->approvedby2 = auth()->user()->id;
        $viralsample->dateapproved = date('Y-m-d');
        $viralsample->dateapproved2 = date('Y-m-d');
        $viralsample->save();
        MiscViral::check_batch($sample->batch_id);
        return back();
    }

    public function release_redraws(Request $request)
    {
        $viralsamples = $request->input('samples');
        // DB::table('viralsamples')->whereIn('id', $viralsamples)->update(['repeatt' => 0, 'result' => "Collect New Sample"]);

        $viralsamples = Viralsample::whereIn('id', $viralsamples)->get();

        foreach ($viralsamples as $key => $viralsample) {
            $this->release_redraw($viralsample);
        }

        return back();
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $samples = Viralsample::whereRaw("id like '" . $search . "%'")->paginate(10);
        return $samples;
    }

    private function clear_session(){
        session()->forget('viral_batch');
        session()->forget('viral_facility_name');
        session()->forget('viral_batch_total');

        // session()->forget('viral_batch_no');
        // session()->forget('viral_batch_dispatch');
        // session()->forget('viral_batch_dispatched');
        // session()->forget('viral_batch_received');
        // session()->forget('viral_facility_id');
        // session()->forget('viral_facility_name');
    }
}
