<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Email;
use Carbon\Carbon;

class WorkerImportController extends Controller
{
    /**
     * Expected CSV headers (STRICT ORDER)
     */
    private array $expectedHeaders = [
        'Surname',
        'Forename',
        'Title',
        'Address1',
        'Address2',
        'City',
        'County',
        'PostCode',
        'Country',
        'Date of Birth',
        'Mobile Phone',
        'Home Phone',
        'Email Address',
        'NI Number',
        'Account No',
        'SortCode',
        'BS Ref',
        'Nationality',
        'Job Title',
        'EndClient',
        'Sharecode',
        'ExternalId',
        'Signify',
        'Venatu',
        'Bank Name',
        'Branch',
        'StartDate'
    ];

    /**
     * Show import form
     */
    public function create()
    {
        $agencies = Agency::orderBy('name')->get();
        return view('workers.import', compact('agencies'));
    }

    /**
     * Handle CSV upload
     */
    public function store(Request $request)
    {
        $request->validate([
            'agency_id' => ['required', 'exists:agencies,id'],
            'csv_file'  => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        /* ---------------- HEADER VALIDATION ---------------- */
        $header = fgetcsv($file);

        if ($header !== $this->expectedHeaders) {
            return back()->withErrors([
                'csv_file' => 'CSV format does not match the sample file'
            ]);
        }

        $rows   = [];
        $emails = [];
        $phones = [];
        $nis    = [];

        $rowNumber = 1;

        /* ---------------- READ CSV ---------------- */
        while (($data = fgetcsv($file)) !== false) {
            $rowNumber++;

            $forename = trim($data[1] ?? '');
            $email    = strtolower(trim($data[12] ?? ''));
            $ni       = strtoupper(trim($data[13] ?? ''));

            $rawMobilePhone = trim($data[10] ?? '');
            $rawHomePhone   = trim($data[11] ?? '');

            /* -------- Required fields -------- */
            if ($forename === '') {
                return back()->withErrors([
                    'csv_file' => "Forename is required at row {$rowNumber}"
                ]);
            }

            if ($rawMobilePhone === '') {
                return back()->withErrors([
                    'csv_file' => "Mobile Phone is required at row {$rowNumber}"
                ]);
            }

            if ($ni === '') {
                return back()->withErrors([
                    'csv_file' => "NI Number is required at row {$rowNumber}"
                ]);
            }

            /* -------- Email validation (Laravel 12) -------- */
            $emailValidator = Validator::make(
                ['email' => $email],
                ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
            );

            if ($emailValidator->fails()) {
                return back()->withErrors([
                    'csv_file' => "Invalid email format at row {$rowNumber}"
                ]);
            }

            /* -------- Phone validation -------- */
            if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
                return back()->withErrors([
                    'csv_file' => "Mobile Phone must contain only digits and max 20 digits at row {$rowNumber}"
                ]);
            }

            if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
                return back()->withErrors([
                    'csv_file' => "Home Phone must contain only digits and max 20 digits at row {$rowNumber}"
                ]);
            }

            $phone     = $rawMobilePhone;
            $homePhone = $rawHomePhone ?: null;

            /* -------- Internal CSV duplicate check -------- */
            if (
                in_array($email, $emails, true) ||
                in_array($phone, $phones, true) ||
                in_array($ni, $nis, true)
            ) {
                return back()->withErrors([
                    'csv_file' => "Duplicate Email / Phone / NI found in CSV at row {$rowNumber}"
                ]);
            }

            $emails[] = $email;
            $phones[] = $phone;
            $nis[]    = $ni;

            /* -------- Prepare insert -------- */
            $rows[] = [
                'agency_id'     => $request->agency_id,
                'surname'       => trim($data[0] ?? ''),
                'forename'      => $forename,
                'title'         => $data[2] ?? null,

                'address1'      => $data[3] ?? null,
                'address2'      => $data[4] ?? null,
                'city'          => $data[5] ?? null,
                'county'        => $data[6] ?? null,
                'postcode'      => $data[7] ?? null,
                'country'       => $data[8] ?? null,

                'date_of_birth' => $this->parseDate($data[9] ?? null),
                'mobile_phone'  => $phone,
                'home_phone'    => $homePhone,
                'email'         => $email,

                'ni_number'     => $ni,

                'account_no'    => $data[14] ?? null,
                'sort_code'     => $data[15] ?? null,
                'bs_ref'        => $data[16] ?? null,

                'nationality'   => $data[17] ?? null,
                'job_title'     => $data[18] ?? null,
                'end_client'    => $data[19] ?? null,

                'sharecode'     => $data[20] ?? null,
                'external_id'   => $data[21] ?? null,
                'signify'       => $data[22] ?? null,
                'venatu'        => $data[23] ?? null,

                'bank_name'     => $data[24] ?? null,
                'branch'        => $data[25] ?? null,
                'start_date'    => $this->parseDate($data[26] ?? null),

                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        fclose($file);

        /* -------- Database duplicate check -------- */
        $exists = Worker::whereIn('email', $emails)
            ->orWhereIn('mobile_phone', $phones)
            ->orWhereIn('ni_number', $nis)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'csv_file' => 'One or more workers already exist in database (Email / Phone / NI)'
            ]);
        }

        DB::transaction(fn() => Worker::insert($rows));

        return redirect()
            ->route('workers.index')
            ->with('success', 'Workers imported successfully');
    }

    /**
     * Download sample CSV
     */
    // public function downloadSample()
    // {
    //     return response()->download(public_path('sample.csv'), 'sample.csv');
    // }

    public function downloadSample()
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="sample.csv"',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];

    $callback = function () {
        $handle = fopen('php://output', 'w');

        // Write header row
        fputcsv($handle, [
            'Surname','Forename','Title','Address1','Address2','City','County','PostCode','Country',
            'Date of Birth','Mobile Phone','Home Phone','Email Address','NI Number','Account No','SortCode',
            'BS Ref','Nationality','Job Title','EndClient','Sharecode','ExternalId','Signify','Venatu',
            'Bank Name','Branch','StartDate'
        ]);

        // Write example rows
        fputcsv($handle, [
            'Bloggs','Joe','Mr','15 My Street','MyTown','My City','MyCounty','AB12 3CD','UK',
            '01/01/1980','12345678900','','myname@myemail.com','AB123456C','45674567','202621',
            '','Romania','Production Operative','Tesco','','1','GLAA','','Bank Name','',''
        ]);

        fputcsv($handle, [
            'Baggins','Bilbo','Mr','20 My Street','YourTown','My City','MyCounty','EF45 6GH','UK',
            '01/01/1980','12345678901','','myname@mysemail.com','AB123456CB','12341234','202621',
            '','Albania','Warehouse Operative','Tesco','','2','Non-GLAA','','Bank Name','',''
        ]);

        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}


    /**
     * Date parser
     */
    private function parseDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * List workers
     */
    public function index()
    {
        $workers = Worker::with('agency')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('workers.index', compact('workers'));
    }

    /**
     * Edit worker
     */
    public function edit(Worker $worker)
    {
        $agencies = Agency::orderBy('name')->get();
        return view('workers.edit', compact('worker', 'agencies'));
    }

    /**
     * Update worker
     */
public function update(Request $request, Worker $worker)
{
    // First, check that mobile_phone is provided
    if (!$request->filled('mobile_phone')) {
        return back()->withErrors([
            'mobile_phone' => 'Mobile phone is required'
        ])->withInput();
    }

    $mobile = $request->mobile_phone;
    $home   = $request->home_phone;

    // Then validate digits and max length
    if (!ctype_digit($mobile) || strlen($mobile) > 20) {
        return back()->withErrors([
            'mobile_phone' => 'Mobile phone must contain only digits and max 20 digits'
        ])->withInput();
    }

    if ($home && (!ctype_digit($home) || strlen($home) > 20)) {
        return back()->withErrors([
            'home_phone' => 'Home phone must contain only digits and max 20 digits'
        ])->withInput();
    }

    // Then validate other fields
    $request->validate([
        'forename' => ['required'],
        'agency_id' => ['required', 'exists:agencies,id'],

        'email' => [
            'required',
            'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            'unique:workers,email,' . $worker->id,
        ],

        'mobile_phone' => [
            'unique:workers,mobile_phone,' . $worker->id,
        ],

        'ni_number' => [
            'required',
            'unique:workers,ni_number,' . $worker->id,
        ],
    ]);

    $worker->update($request->all());

    return redirect()
        ->route('workers.index')
        ->with('success', 'Worker updated successfully');
}


    /**
     * Delete worker
     */
    public function destroy(Worker $worker)
    {
        $worker->delete();

        return redirect()
            ->route('workers.index')
            ->with('success', 'Worker deleted successfully');
    }
}
