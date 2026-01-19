<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'agency_id' => 'required|exists:agencies,id',
            'csv_file'  => 'required|file|mimes:csv,txt',
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        /** ---------------------------------
         *  HEADER VALIDATION
         * --------------------------------*/
        $header = fgetcsv($file);

        if ($header !== $this->expectedHeaders) {
            return back()->withErrors([
                'csv_file' => 'CSV format does not match the sample file'
            ]);
        }

        /** ---------------------------------
         *  ARRAYS FOR DUPLICATE CHECK
         * --------------------------------*/
        $rows   = [];
        $emails = [];
        $phones = [];
        $nis    = [];

        $rowNumber = 1; // Header already read

        /** ---------------------------------
         *  READ CSV ROWS
         * --------------------------------*/
        while (($data = fgetcsv($file)) !== false) {

            $rowNumber++;

            /** Normalize & required field validation */

            $rawMobilePhone = trim($data[10] ?? '');
            $rawHomePhone   = trim($data[11] ?? '');

            $forename = trim($data[1] ?? '');
            $email    = strtolower(trim($data[12] ?? ''));
            $ni       = strtoupper(trim($data[13] ?? ''));

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

            // if (!ctype_digit($rawMobilePhone)) {
            //     return back()->withErrors([
            //         'csv_file' => "Mobile Phone must contain only numbers at row {$rowNumber}"
            //     ]);
            // }

            // if (strlen($rawMobilePhone) > 20) {
            //     return back()->withErrors([
            //         'csv_file' => "Mobile Phone must be between 1 and 20 digits at row {$rowNumber}"
            //     ]);
            // }

            // if ($rawHomePhone !== '') {

            //     if (!ctype_digit($rawHomePhone)) {
            //         return back()->withErrors([
            //             'csv_file' => "Home Phone must contain only numbers at row {$rowNumber}"
            //         ]);
            //     }

            //     if (strlen($rawHomePhone) > 20) {
            //         return back()->withErrors([
            //             'csv_file' => "Home Phone must be between 1 and 20 digits at row {$rowNumber}"
            //         ]);
            //     }
            // }

            if ($email === '') {
                return back()->withErrors([
                    'csv_file' => "Email Address is required at row {$rowNumber}"
                ]);
            }

            if ($ni === '') {
                return back()->withErrors([
                    'csv_file' => "NI Number is required at row {$rowNumber}"
                ]);
            }

            /** Digits only + length validation */
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

            /** Final normalized values */
            $phone = $rawMobilePhone;
            $homePhone = $rawHomePhone ?: null;


            /** ---------------------------------
             *  CSV INTERNAL DUPLICATE CHECK
             * --------------------------------*/
            if (
                in_array($email, $emails) ||
                in_array($phone, $phones) ||
                in_array($ni, $nis)
            ) {
                return back()->withErrors([
                    'csv_file' => "Duplicate Email / Phone / NI found in CSV at row {$rowNumber}"
                ]);
            }

            $emails[] = $email;
            $phones[] = $phone;
            $nis[]    = $ni;

            /** ---------------------------------
             *  PREPARE INSERT DATA
             * --------------------------------*/
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

        /** ---------------------------------
         *  DATABASE DUPLICATE CHECK
         * --------------------------------*/
        $exists = Worker::where(function ($q) use ($emails) {
            if (!empty($emails)) {
                $q->whereIn('email', $emails);
            }
        })
            ->orWhere(function ($q) use ($phones) {
                if (!empty($phones)) {
                    $q->whereIn('mobile_phone', $phones);
                }
            })
            ->orWhere(function ($q) use ($nis) {
                if (!empty($nis)) {
                    $q->whereIn('ni_number', $nis);
                }
            })
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'csv_file' => 'One or more workers already exist in database (Email / Phone / NI)'
            ]);
        }

        /** ---------------------------------
         *  INSERT USING TRANSACTION
         * --------------------------------*/
        DB::transaction(function () use ($rows) {
            Worker::insert($rows);
        });

        return back()->with('success', 'Workers imported successfully');
    }

    /**
     * Download sample CSV
     */
    public function downloadSample()
    {
        return response()->download(public_path('sample.csv'), 'sample.csv');
    }

    /**
     * Date parser
     */
    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', trim($value))
                ->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function index()
    {
        $workers = Worker::with('agency')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('workers.index', compact('workers'));
    }

    public function edit(Worker $worker)
    {
        $agencies = Agency::orderBy('name')->get();

        return view('workers.edit', compact('worker', 'agencies'));
    }

    public function update(Request $request, Worker $worker)
    {
        $mobile = $request->mobile_phone;
        $home   = $request->home_phone;

        if (!ctype_digit($mobile)) {
            return back()->withErrors([
                'mobile_phone' => 'Mobile phone must contain only numbers'
            ])
                ->withInput();
        }

        if (strlen($mobile) > 20) {
            return back()->withErrors([
                'mobile_phone' => 'Mobile phone must be between 1 and 20 digits'
            ])
                ->withInput();
        }

        if ($home !== null && $home !== '') {

            if (!ctype_digit($home)) {
                return back()->withErrors([
                    'home_phone' => 'Home phone must contain only numbers'
                ])
                    ->withInput();
            }

            if (strlen($home) > 20) {
                return back()->withErrors([
                    'home_phone' => 'Home phone must be between 1 and 20 digits'
                ])
                    ->withInput();
            }
        }

        $request->validate([
            'forename'      => 'required',
            'mobile_phone'  => 'required|unique:workers,mobile_phone,' . $worker->id,
            'email'         => 'required|email|unique:workers,email,' . $worker->id,
            'ni_number'     => 'required|unique:workers,ni_number,' . $worker->id,
            'agency_id'     => 'required|exists:agencies,id',
        ]);

        $worker->update($request->all());

        return redirect()
            ->route('workers.index')
            ->with('success', 'Worker updated successfully');
    }

    public function destroy(Worker $worker)
    {
        $worker->delete();

        return redirect()
            ->route('workers.index')
            ->with('success', 'Worker deleted successfully');
    }
}
