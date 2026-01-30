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
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'agency_id' => ['required', 'exists:agencies,id'],
    //         'csv_file'  => ['required', 'file', 'mimes:csv,txt'],
    //     ]);

    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    //     /* ---------------- HEADER VALIDATION ---------------- */
    //     $header = fgetcsv($file);

    //     if ($header !== $this->expectedHeaders) {
    //         return back()->withErrors([
    //             'csv_file' => 'CSV format does not match the sample file'
    //         ]);
    //     }

    //     $rows   = [];
    //     $emails = [];
    //     $phones = [];
    //     $nis    = [];

    //     $rowNumber = 1;

    //     /* ---------------- READ CSV ---------------- */
    //     while (($data = fgetcsv($file)) !== false) {
    //         $rowNumber++;

    //         $forename = trim($data[1] ?? '');
    //         $email    = strtolower(trim($data[12] ?? ''));
    //         $ni       = strtoupper(trim($data[13] ?? ''));

    //         $rawMobilePhone = trim($data[10] ?? '');
    //         $rawHomePhone   = trim($data[11] ?? '');

    //         /* -------- Required fields -------- */
    //         if ($forename === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "Forename is required at row {$rowNumber}"
    //             ]);
    //         }

    //         if ($rawMobilePhone === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "Mobile Phone is required at row {$rowNumber}"
    //             ]);
    //         }

    //         if ($ni === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "NI Number is required at row {$rowNumber}"
    //             ]);
    //         }

    //         /* -------- Email validation (Laravel 12) -------- */
    //         $emailValidator = Validator::make(
    //             ['email' => $email],
    //             ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
    //         );

    //         if ($emailValidator->fails()) {
    //             return back()->withErrors([
    //                 'csv_file' => "Invalid email format at row {$rowNumber}"
    //             ]);
    //         }

    //         /* -------- Phone validation -------- */
    //         if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
    //             return back()->withErrors([
    //                 'csv_file' => "Mobile Phone must contain only digits and max 20 digits at row {$rowNumber}"
    //             ]);
    //         }

    //         if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
    //             return back()->withErrors([
    //                 'csv_file' => "Home Phone must contain only digits and max 20 digits at row {$rowNumber}"
    //             ]);
    //         }

    //         $phone     = $rawMobilePhone;
    //         $homePhone = $rawHomePhone ?: null;

    //         /* -------- Internal CSV duplicate check -------- */
    //         // if (
    //         //     in_array($email, $emails, true) ||
    //         //     in_array($phone, $phones, true) ||
    //         //     in_array($ni, $nis, true)
    //         // ) {
    //         //     return back()->withErrors([
    //         //         'csv_file' => "Duplicate Email / Phone / NI found in CSV at row {$rowNumber}"
    //         //     ]);
    //         // }

    //         /* -------- Internal CSV duplicate check -------- */
    //         if (in_array($email, $emails, true)) {
    //             return back()->withErrors([
    //                 'csv_file' => "Duplicate Email found in CSV at row {$rowNumber}"
    //             ]);
    //         }

    //         if (in_array($phone, $phones, true)) {
    //             return back()->withErrors([
    //                 'csv_file' => "Duplicate Phone Number found in CSV at row {$rowNumber}"
    //             ]);
    //         }

    //         if (in_array($ni, $nis, true)) {
    //             return back()->withErrors([
    //                 'csv_file' => "Duplicate NI found in CSV at row {$rowNumber}"
    //             ]);
    //         }


    //         $emails[] = $email;
    //         $phones[] = $phone;
    //         $nis[]    = $ni;

    //         /* -------- Prepare insert -------- */
    //         $rows[] = [
    //             'agency_id'     => $request->agency_id,
    //             'surname'       => trim($data[0] ?? ''),
    //             'forename'      => $forename,
    //             'title'         => $data[2] ?? null,

    //             'address1'      => $data[3] ?? null,
    //             'address2'      => $data[4] ?? null,
    //             'city'          => $data[5] ?? null,
    //             'county'        => $data[6] ?? null,
    //             'postcode'      => $data[7] ?? null,
    //             'country'       => $data[8] ?? null,

    //             'date_of_birth' => $this->parseDate($data[9] ?? null),
    //             'mobile_phone'  => $phone,
    //             'home_phone'    => $homePhone,
    //             'email'         => $email,

    //             'ni_number'     => $ni,

    //             'account_no'    => $data[14] ?? null,
    //             'sort_code'     => $data[15] ?? null,
    //             'bs_ref'        => $data[16] ?? null,

    //             'nationality'   => $data[17] ?? null,
    //             'job_title'     => $data[18] ?? null,
    //             'end_client'    => $data[19] ?? null,

    //             'sharecode'     => $data[20] ?? null,
    //             'external_id'   => $data[21] ?? null,
    //             'signify'       => $data[22] ?? null,
    //             'venatu'        => $data[23] ?? null,

    //             'bank_name'     => $data[24] ?? null,
    //             'branch'        => $data[25] ?? null,
    //             'start_date'    => $this->parseDate($data[26] ?? null),

    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ];
    //     }

    //     fclose($file);

    //     /* -------- Database duplicate check -------- */
    //     $exists = Worker::whereIn('email', $emails)
    //         ->orWhereIn('mobile_phone', $phones)
    //         ->orWhereIn('ni_number', $nis)
    //         ->exists();

    //     if ($exists) {
    //         return back()->withErrors([
    //             'csv_file' => 'One or more workers already exist in database (Email / Phone / NI)'
    //         ]);
    //     }

    //     DB::transaction(fn() => Worker::insert($rows));

    //     return redirect()
    //         ->route('workers.index')
    //         ->with('success', 'Workers imported successfully');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'agency_id' => ['required', 'exists:agencies,id'],
    //         'csv_file'  => ['required', 'file', 'mimes:csv,txt'],
    //     ]);

    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    //     /* ---------------- HEADER VALIDATION ---------------- */
    //     $header = fgetcsv($file);

    //     if ($header !== $this->expectedHeaders) {
    //         return back()->withErrors([
    //             'csv_file' => 'CSV format does not match the sample file'
    //         ]);
    //     }

    //     $rows = [];

    //     // Track first occurrence (CSV)
    //     $emailRows = [];
    //     $phoneRows = [];
    //     $niRows    = [];

    //     // Track for DB check
    //     $emailToRow = [];
    //     $phoneToRow = [];
    //     $niToRow    = [];

    //     $rowNumber = 1;

    //     /* ---------------- READ CSV ---------------- */
    //     while (($data = fgetcsv($file)) !== false) {
    //         $rowNumber++;

    //         $forename = trim($data[1] ?? '');
    //         $email    = strtolower(trim($data[12] ?? ''));
    //         $ni       = strtoupper(trim($data[13] ?? ''));

    //         $rawMobilePhone = trim($data[10] ?? '');
    //         $rawHomePhone   = trim($data[11] ?? '');

    //         /* -------- Required fields -------- */
    //         if ($forename === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "Forename is required"
    //             ]);
    //         }

    //         if ($rawMobilePhone === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "Mobile Phone is required"
    //             ]);
    //         }

    //         if ($ni === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "NI Number is required"
    //             ]);
    //         }

    //         /* -------- Email validation -------- */
    //         $emailValidator = Validator::make(
    //             ['email' => $email],
    //             ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
    //         );

    //         if ($emailValidator->fails()) {
    //             return back()->withErrors([
    //                 'csv_file' => "Invalid email format"
    //             ]);
    //         }

    //         /* -------- Phone validation -------- */
    //         if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
    //             return back()->withErrors([
    //                 'csv_file' => "Mobile Phone must contain only digits and max 20 digits"
    //             ]);
    //         }

    //         if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
    //             return back()->withErrors([
    //                 'csv_file' => "Home Phone must contain only digits and max 20 digits"
    //             ]);
    //         }

    //         $phone     = $rawMobilePhone;
    //         $homePhone = $rawHomePhone ?: null;

    //         /* -------- CSV DUPLICATE CHECK (multi-field) -------- */
    //         $duplicateFields = [];

    //         if (isset($emailRows[$email])) {
    //             $duplicateFields[] = 'Email';
    //         }

    //         if (isset($phoneRows[$phone])) {
    //             $duplicateFields[] = 'Phone Number';
    //         }

    //         if (isset($niRows[$ni])) {
    //             $duplicateFields[] = 'NI';
    //         }

    //         if (!empty($duplicateFields)) {
    //             return back()->withErrors([
    //                 'csv_file' =>
    //                 'Duplicate ' . implode(', ', $duplicateFields) .
    //                     " found in CSV"
    //             ]);
    //         }

    //         // Record CSV occurrence
    //         $emailRows[$email] = $rowNumber;
    //         $phoneRows[$phone] = $rowNumber;
    //         $niRows[$ni]       = $rowNumber;

    //         // Record for DB check
    //         $emailToRow[$email] = $rowNumber;
    //         $phoneToRow[$phone] = $rowNumber;
    //         $niToRow[$ni]       = $rowNumber;

    //         /* -------- Prepare insert -------- */
    //         $rows[] = [
    //             'agency_id'     => $request->agency_id,
    //             'surname'       => trim($data[0] ?? ''),
    //             'forename'      => $forename,
    //             'title'         => $data[2] ?? null,

    //             'address1'      => $data[3] ?? null,
    //             'address2'      => $data[4] ?? null,
    //             'city'          => $data[5] ?? null,
    //             'county'        => $data[6] ?? null,
    //             'postcode'      => $data[7] ?? null,
    //             'country'       => $data[8] ?? null,

    //             'date_of_birth' => $this->parseDate($data[9] ?? null),
    //             'mobile_phone'  => $phone,
    //             'home_phone'    => $homePhone,
    //             'email'         => $email,

    //             'ni_number'     => $ni,

    //             'account_no'    => $data[14] ?? null,
    //             'sort_code'     => $data[15] ?? null,
    //             'bs_ref'        => $data[16] ?? null,

    //             'nationality'   => $data[17] ?? null,
    //             'job_title'     => $data[18] ?? null,
    //             'end_client'    => $data[19] ?? null,

    //             'sharecode'     => $data[20] ?? null,
    //             'external_id'   => $data[21] ?? null,
    //             'signify'       => $data[22] ?? null,
    //             'venatu'        => $data[23] ?? null,

    //             'bank_name'     => $data[24] ?? null,
    //             'branch'        => $data[25] ?? null,
    //             'start_date'    => $this->parseDate($data[26] ?? null),

    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ];
    //     }

    //     fclose($file);

    //     /* -------- DATABASE DUPLICATE CHECK (multi-field + rows) -------- */
    //     // $dbErrors = [];

    //     // $existingEmails = Worker::whereIn('email', array_keys($emailToRow))
    //     //     ->pluck('email')
    //     //     ->toArray();

    //     // foreach ($existingEmails as $email) {
    //     //     $dbErrors[] =
    //     //         "Email already exists in database(CSV row {$emailToRow[$email]})";
    //     // }

    //     // $existingPhones = Worker::whereIn('mobile_phone', array_keys($phoneToRow))
    //     //     ->pluck('mobile_phone')
    //     //     ->toArray();

    //     // foreach ($existingPhones as $phone) {
    //     //     $dbErrors[] =
    //     //         "Phone Number already exists in database(CSV row {$phoneToRow[$phone]})";
    //     // }

    //     // $existingNIs = Worker::whereIn('ni_number', array_keys($niToRow))
    //     //     ->pluck('ni_number')
    //     //     ->toArray();

    //     // foreach ($existingNIs as $ni) {
    //     //     $dbErrors[] =
    //     //         "NI already exists in database(CSV row {$niToRow[$ni]})";
    //     // }

    //     // if (!empty($dbErrors)) {
    //     //     return back()->withErrors([
    //     //         'csv_file' => implode(' | ', $dbErrors)
    //     //     ]);
    //     // }

    //     //     /* -------- DATABASE DUPLICATE CHECK (single error, clean) -------- */

    //     // // Email
    //     // $existingEmail = Worker::whereIn('email', array_keys($emailToRow))
    //     //     ->first(['email']);

    //     // if ($existingEmail) {
    //     //     return back()->withErrors([
    //     //         'csv_file' =>
    //     //             "Email already exists in database at row {$emailToRow[$existingEmail->email]}"
    //     //     ]);
    //     // }

    //     // // Phone
    //     // $existingPhone = Worker::whereIn('mobile_phone', array_keys($phoneToRow))
    //     //     ->first(['mobile_phone']);

    //     // if ($existingPhone) {
    //     //     return back()->withErrors([
    //     //         'csv_file' =>
    //     //             "Phone Number already exists in database at row {$phoneToRow[$existingPhone->mobile_phone]}"
    //     //     ]);
    //     // }

    //     // // NI
    //     // $existingNI = Worker::whereIn('ni_number', array_keys($niToRow))
    //     //     ->first(['ni_number']);

    //     // if ($existingNI) {
    //     //     return back()->withErrors([
    //     //         'csv_file' =>
    //     //             "NI already exists in database at row {$niToRow[$existingNI->ni_number]}"
    //     //     ]);
    //     // }

    //     /* -------- DATABASE DUPLICATE CHECK (value + row) -------- */

    //     // Email
    //     $existingEmail = Worker::whereIn('email', array_keys($emailToRow))
    //         ->first(['email']);

    //     if ($existingEmail) {
    //         $email = $existingEmail->email;

    //         return back()->withErrors([
    //             'csv_file' =>
    //             "Email '{$email}' already exists in database"
    //         ]);
    //     }

    //     // Phone
    //     $existingPhone = Worker::whereIn('mobile_phone', array_keys($phoneToRow))
    //         ->first(['mobile_phone']);

    //     if ($existingPhone) {
    //         $phone = $existingPhone->mobile_phone;

    //         return back()->withErrors([
    //             'csv_file' =>
    //             "Phone Number '{$phone}' already exists in database"
    //         ]);
    //     }

    //     // NI
    //     $existingNI = Worker::whereIn('ni_number', array_keys($niToRow))
    //         ->first(['ni_number']);

    //     if ($existingNI) {
    //         $ni = $existingNI->ni_number;

    //         return back()->withErrors([
    //             'csv_file' =>
    //             "NI Number '{$ni}' already exists in database"
    //         ]);
    //     }



    //     /* -------- Insert -------- */
    //     DB::transaction(fn() => Worker::insert($rows));

    //     return redirect()
    //         ->route('workers.index')
    //         ->with('success', 'Workers imported successfully');
    // }

    //         public function store(Request $request)
    // {
    //     $request->validate([
    //         'agency_id' => ['required', 'exists:agencies,id'],
    //         'csv_file'  => ['required', 'file', 'mimes:csv,txt'],
    //     ]);

    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    //     /* ---------------- HEADER VALIDATION ---------------- */
    //     $header = fgetcsv($file);

    //     if ($header !== $this->expectedHeaders) {
    //         return back()->withErrors([
    //             'csv_file' => 'CSV format does not match the sample file'
    //         ]);
    //     }

    //     $rows = [];

    //     // Track first occurrence in CSV
    //     $emailRows = [];
    //     $phoneRows = [];
    //     $niRows    = [];

    //     // ⭐ CHANGE: collect CSV duplicate messages
    //     $csvErrors = [];

    //     $rowNumber = 1;

    //     /* ---------------- READ CSV ---------------- */
    //     while (($data = fgetcsv($file)) !== false) {
    //         $rowNumber++;

    //         $forename = trim($data[1] ?? '');
    //         $email    = strtolower(trim($data[12] ?? ''));
    //         $ni       = strtoupper(trim($data[13] ?? ''));

    //         $rawMobilePhone = trim($data[10] ?? '');
    //         $rawHomePhone   = trim($data[11] ?? '');

    //         /* -------- Required fields -------- */
    //         if ($forename === '') {
    //             return back()->withErrors(['csv_file' => 'Forename is required']);
    //         }

    //         if ($rawMobilePhone === '') {
    //             return back()->withErrors(['csv_file' => 'Mobile Phone is required']);
    //         }

    //         if ($ni === '') {
    //             return back()->withErrors(['csv_file' => 'NI Number is required']);
    //         }

    //         /* -------- Email validation -------- */
    //         $emailValidator = Validator::make(
    //             ['email' => $email],
    //             ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
    //         );

    //         if ($emailValidator->fails()) {
    //             return back()->withErrors(['csv_file' => 'Invalid email format']);
    //         }

    //         /* -------- Phone validation -------- */
    //         if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
    //             return back()->withErrors([
    //                 'csv_file' => 'Mobile Phone must contain only digits and max 20 digits'
    //             ]);
    //         }

    //         if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
    //             return back()->withErrors([
    //                 'csv_file' => 'Home Phone must contain only digits and max 20 digits'
    //             ]);
    //         }

    //         $phone     = $rawMobilePhone;
    //         $homePhone = $rawHomePhone ?: null;

    //         /* -------- CSV DUPLICATE CHECK (⭐ CHANGE) -------- */
    //         $duplicateParts = [];

    //         if (isset($emailRows[$email])) {
    //             $duplicateParts[] = "Email ({$email})";
    //         }

    //         if (isset($phoneRows[$phone])) {
    //             $duplicateParts[] = "Phone Number ({$phone})";
    //         }

    //         if (isset($niRows[$ni])) {
    //             $duplicateParts[] = "NI Number ({$ni})";
    //         }

    //         if (!empty($duplicateParts)) {
    //             $last = array_pop($duplicateParts);

    //             $message = empty($duplicateParts)
    //                 ? "Duplicate {$last} found in CSV."
    //                 : "Duplicate " . implode(', ', $duplicateParts) . " and {$last} found in CSV.";

    //             $csvErrors[] = $message;
    //         }

    //         // Record first occurrence
    //         $emailRows[$email] = $emailRows[$email] ?? $rowNumber;
    //         $phoneRows[$phone] = $phoneRows[$phone] ?? $rowNumber;
    //         $niRows[$ni]       = $niRows[$ni] ?? $rowNumber;

    //         /* -------- Prepare insert -------- */
    //         $rows[] = [
    //             'agency_id'     => $request->agency_id,
    //             'surname'       => trim($data[0] ?? ''),
    //             'forename'      => $forename,
    //             'title'         => $data[2] ?? null,
    //             'address1'      => $data[3] ?? null,
    //             'address2'      => $data[4] ?? null,
    //             'city'          => $data[5] ?? null,
    //             'county'        => $data[6] ?? null,
    //             'postcode'      => $data[7] ?? null,
    //             'country'       => $data[8] ?? null,
    //             'date_of_birth' => $this->parseDate($data[9] ?? null),
    //             'mobile_phone'  => $phone,
    //             'home_phone'    => $homePhone,
    //             'email'         => $email,
    //             'ni_number'     => $ni,
    //             'account_no'    => $data[14] ?? null,
    //             'sort_code'     => $data[15] ?? null,
    //             'bs_ref'        => $data[16] ?? null,
    //             'nationality'   => $data[17] ?? null,
    //             'job_title'     => $data[18] ?? null,
    //             'end_client'    => $data[19] ?? null,
    //             'sharecode'     => $data[20] ?? null,
    //             'external_id'   => $data[21] ?? null,
    //             'signify'       => $data[22] ?? null,
    //             'venatu'        => $data[23] ?? null,
    //             'bank_name'     => $data[24] ?? null,
    //             'branch'        => $data[25] ?? null,
    //             'start_date'    => $this->parseDate($data[26] ?? null),
    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ];
    //     }

    //     fclose($file);

    //     /* -------- ⭐ CHANGE: return CSV duplicate errors AFTER full scan -------- */
    //     if (!empty($csvErrors)) {
    //         return back()->withErrors([
    //             'csv_file' => implode('<br>', array_unique($csvErrors))
    //         ]);
    //     }

    //     /* -------- Insert -------- */
    //     DB::transaction(fn () => Worker::insert($rows));

    //     return redirect()
    //         ->route('workers.index')
    //         ->with('success', 'Workers imported successfully');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'agency_id' => ['required', 'exists:agencies,id'],
    //         'csv_file'  => ['required', 'file', 'mimes:csv,txt'],
    //     ]);

    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    //     /* ---------------- HEADER VALIDATION ---------------- */
    //     $header = fgetcsv($file);

    //     if ($header !== $this->expectedHeaders) {
    //         return back()->withErrors([
    //             'csv_file' => 'CSV format does not match the sample file'
    //         ]);
    //     }

    //     $rows = [];

    //     // Track first occurrence (CSV)
    //     $emailRows = [];
    //     $phoneRows = [];
    //     $niRows    = [];

    //     // Track all duplicates per field
    //     $emailDuplicates = [];
    //     $phoneDuplicates = [];
    //     $niDuplicates    = [];

    //     // Track for DB check
    //     $emailToRow = [];
    //     $phoneToRow = [];
    //     $niToRow    = [];

    //     /* ---------------- READ CSV ---------------- */
    //     while (($data = fgetcsv($file)) !== false) {

    //         $forename = trim($data[1] ?? '');
    //         $email    = strtolower(trim($data[12] ?? ''));
    //         $ni       = strtoupper(trim($data[13] ?? ''));

    //         $rawMobilePhone = trim($data[10] ?? '');
    //         $rawHomePhone   = trim($data[11] ?? '');

    //         /* -------- Required fields -------- */
    //         if ($forename === '') {
    //             return back()->withErrors(['csv_file' => "Forename is required"]);
    //         }

    //         if ($rawMobilePhone === '') {
    //             return back()->withErrors(['csv_file' => "Mobile Phone is required"]);
    //         }

    //         if ($ni === '') {
    //             return back()->withErrors(['csv_file' => "NI Number is required"]);
    //         }

    //         /* -------- Email validation -------- */
    //         $emailValidator = Validator::make(
    //             ['email' => $email],
    //             ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
    //         );

    //         if ($emailValidator->fails()) {
    //             return back()->withErrors(['csv_file' => "Invalid email format"]);
    //         }

    //         /* -------- Phone validation -------- */
    //         if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
    //             return back()->withErrors(['csv_file' => "Mobile Phone must contain only digits and max 20 digits"]);
    //         }

    //         if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
    //             return back()->withErrors(['csv_file' => "Home Phone must contain only digits and max 20 digits"]);
    //         }

    //         $phone     = $rawMobilePhone;
    //         $homePhone = $rawHomePhone ?: null;

    //         /* -------- CSV DUPLICATE CHECK (multi-field) -------- */
    //         if (isset($emailRows[$email])) {
    //             $emailDuplicates[$email] = true;
    //         }

    //         if (isset($phoneRows[$phone])) {
    //             $phoneDuplicates[$phone] = true;
    //         }

    //         if (isset($niRows[$ni])) {
    //             $niDuplicates[$ni] = true;
    //         }

    //         // Record CSV occurrence
    //         $emailRows[$email] = true;
    //         $phoneRows[$phone] = true;
    //         $niRows[$ni]       = true;

    //         // Record for DB check
    //         $emailToRow[$email] = true;
    //         $phoneToRow[$phone] = true;
    //         $niToRow[$ni]       = true;

    //         /* -------- Prepare insert -------- */
    //         $rows[] = [
    //             'agency_id'     => $request->agency_id,
    //             'surname'       => trim($data[0] ?? ''),
    //             'forename'      => $forename,
    //             'title'         => $data[2] ?? null,

    //             'address1'      => $data[3] ?? null,
    //             'address2'      => $data[4] ?? null,
    //             'city'          => $data[5] ?? null,
    //             'county'        => $data[6] ?? null,
    //             'postcode'      => $data[7] ?? null,
    //             'country'       => $data[8] ?? null,

    //             'date_of_birth' => $this->parseDate($data[9] ?? null),
    //             'mobile_phone'  => $phone,
    //             'home_phone'    => $homePhone,
    //             'email'         => $email,

    //             'ni_number'     => $ni,

    //             'account_no'    => $data[14] ?? null,
    //             'sort_code'     => $data[15] ?? null,
    //             'bs_ref'        => $data[16] ?? null,

    //             'nationality'   => $data[17] ?? null,
    //             'job_title'     => $data[18] ?? null,
    //             'end_client'    => $data[19] ?? null,

    //             'sharecode'     => $data[20] ?? null,
    //             'external_id'   => $data[21] ?? null,
    //             'signify'       => $data[22] ?? null,
    //             'venatu'        => $data[23] ?? null,

    //             'bank_name'     => $data[24] ?? null,
    //             'branch'        => $data[25] ?? null,
    //             'start_date'    => $this->parseDate($data[26] ?? null),

    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ];
    //     }

    //     fclose($file);

    //     /* ---------------- HANDLE CSV DUPLICATES ---------------- */
    //     $duplicateParts = [];

    //     if (!empty($emailDuplicates)) {
    //         $duplicateParts[] = "Email (" . implode(', ', array_keys($emailDuplicates)) . ")";
    //     }
    //     if (!empty($phoneDuplicates)) {
    //         $duplicateParts[] = "Phone Number (" . implode(', ', array_keys($phoneDuplicates)) . ")";
    //     }
    //     if (!empty($niDuplicates)) {
    //         $duplicateParts[] = "NI Number (" . implode(', ', array_keys($niDuplicates)) . ")";
    //     }

    //     if (!empty($duplicateParts)) {
    //         return back()->withErrors([
    //             'csv_file' => "Duplicate " . implode(' and ', $duplicateParts) . " found in CSV."
    //         ]);
    //     }

    //     /* ---------------- HANDLE DATABASE DUPLICATES ---------------- */
    //     $dbDuplicates = [];

    //     $existingEmails = Worker::whereIn('email', array_keys($emailToRow))->pluck('email')->toArray();
    //     if (!empty($existingEmails)) {
    //         $dbDuplicates[] = "Email (" . implode(', ', $existingEmails) . ")";
    //     }

    //     $existingPhones = Worker::whereIn('mobile_phone', array_keys($phoneToRow))->pluck('mobile_phone')->toArray();
    //     if (!empty($existingPhones)) {
    //         $dbDuplicates[] = "Phone Number (" . implode(', ', $existingPhones) . ")";
    //     }

    //     $existingNIs = Worker::whereIn('ni_number', array_keys($niToRow))->pluck('ni_number')->toArray();
    //     if (!empty($existingNIs)) {
    //         $dbDuplicates[] = "NI Number (" . implode(', ', $existingNIs) . ")";
    //     }

    //     if (!empty($dbDuplicates)) {
    //         return back()->withErrors([
    //             'csv_file' => implode(' and ', $dbDuplicates) . " already exists in the database."
    //         ]);
    //     }

    //     /* ---------------- INSERT ---------------- */
    //     DB::transaction(fn() => Worker::insert($rows));

    //     return redirect()
    //         ->route('workers.index')
    //         ->with('success', 'Workers imported successfully');
    // }

    //     public function store(Request $request)
    // {
    //     $request->validate([
    //         'agency_id' => ['required', 'exists:agencies,id'],
    //         'csv_file'  => ['required', 'file', 'mimes:csv,txt'],
    //     ]);

    //     $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    //     /* ---------------- HEADER VALIDATION ---------------- */
    //     $header = fgetcsv($file);

    //     if ($header !== $this->expectedHeaders) {
    //         return back()->withErrors([
    //             'csv_file' => 'CSV format does not match the sample file'
    //         ]);
    //     }

    //     $rows = [];

    //     // Track CSV duplicates
    //     $emailRows = [];
    //     $phoneRows = [];
    //     $niRows    = [];

    //     // Track for DB duplicates
    //     $emailToRow = [];
    //     $phoneToRow = [];
    //     $niToRow    = [];

    //     /* ---------------- READ CSV ---------------- */
    //     while (($data = fgetcsv($file)) !== false) {

    //         $forename = trim($data[1] ?? '');
    //         $email    = strtolower(trim($data[12] ?? ''));
    //         $ni       = strtoupper(trim($data[13] ?? ''));

    //         $rawMobilePhone = trim($data[10] ?? '');
    //         $rawHomePhone   = trim($data[11] ?? '');

    //         /* -------- Required fields -------- */
    //         if ($forename === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "Forename is required"
    //             ]);
    //         }

    //         if ($rawMobilePhone === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "Mobile Phone is required"
    //             ]);
    //         }

    //         if ($ni === '') {
    //             return back()->withErrors([
    //                 'csv_file' => "NI Number is required"
    //             ]);
    //         }

    //         /* -------- Email validation -------- */
    //         $emailValidator = Validator::make(
    //             ['email' => $email],
    //             ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
    //         );

    //         if ($emailValidator->fails()) {
    //             return back()->withErrors([
    //                 'csv_file' => "Invalid email format"
    //             ]);
    //         }

    //         /* -------- Phone validation -------- */
    //         if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
    //             return back()->withErrors([
    //                 'csv_file' => "Mobile Phone must contain only digits and max 20 digits"
    //             ]);
    //         }

    //         if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
    //             return back()->withErrors([
    //                 'csv_file' => "Home Phone must contain only digits and max 20 digits"
    //             ]);
    //         }

    //         $phone     = $rawMobilePhone;
    //         $homePhone = $rawHomePhone ?: null;

    //         /* -------- CSV DUPLICATE CHECK (multi-field) -------- */
    //         $emailRows[$email][] = $email;
    //         $phoneRows[$phone][] = $phone;
    //         $niRows[$ni][]       = $ni;

    //         $duplicateMessages = [];

    //         if (isset($emailRows[$email]) && count($emailRows[$email]) > 1) {
    //             $duplicateMessages['Email'] = array_unique($emailRows[$email]);
    //         }

    //         if (isset($phoneRows[$phone]) && count($phoneRows[$phone]) > 1) {
    //             $duplicateMessages['Phone Number'] = array_unique($phoneRows[$phone]);
    //         }

    //         if (isset($niRows[$ni]) && count($niRows[$ni]) > 1) {
    //             $duplicateMessages['NI Number'] = array_unique($niRows[$ni]);
    //         }

    //         if (!empty($duplicateMessages)) {
    //             $messages = [];
    //             foreach ($duplicateMessages as $field => $values) {
    //                 $messages[] = $field . ' "' . implode('", "', $values) . '"';
    //             }
    //             return back()->withErrors([
    //                 'csv_file' => implode("\n", $messages) . ' found in CSV.'
    //             ]);
    //         }

    //         // Record for DB check
    //         $emailToRow[$email] = $email;
    //         $phoneToRow[$phone] = $phone;
    //         $niToRow[$ni]       = $ni;

    //         /* -------- Prepare insert -------- */
    //         $rows[] = [
    //             'agency_id'     => $request->agency_id,
    //             'surname'       => trim($data[0] ?? ''),
    //             'forename'      => $forename,
    //             'title'         => $data[2] ?? null,
    //             'address1'      => $data[3] ?? null,
    //             'address2'      => $data[4] ?? null,
    //             'city'          => $data[5] ?? null,
    //             'county'        => $data[6] ?? null,
    //             'postcode'      => $data[7] ?? null,
    //             'country'       => $data[8] ?? null,
    //             'date_of_birth' => $this->parseDate($data[9] ?? null),
    //             'mobile_phone'  => $phone,
    //             'home_phone'    => $homePhone,
    //             'email'         => $email,
    //             'ni_number'     => $ni,
    //             'account_no'    => $data[14] ?? null,
    //             'sort_code'     => $data[15] ?? null,
    //             'bs_ref'        => $data[16] ?? null,
    //             'nationality'   => $data[17] ?? null,
    //             'job_title'     => $data[18] ?? null,
    //             'end_client'    => $data[19] ?? null,
    //             'sharecode'     => $data[20] ?? null,
    //             'external_id'   => $data[21] ?? null,
    //             'signify'       => $data[22] ?? null,
    //             'venatu'        => $data[23] ?? null,
    //             'bank_name'     => $data[24] ?? null,
    //             'branch'        => $data[25] ?? null,
    //             'start_date'    => $this->parseDate($data[26] ?? null),
    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ];
    //     }

    //     fclose($file);

    //     /* -------- DATABASE DUPLICATE CHECK (multi-field) -------- */
    //     $dbDuplicateMessages = [];

    //     // Email
    //     $existingEmails = Worker::whereIn('email', array_keys($emailToRow))->pluck('email')->toArray();
    //     if (!empty($existingEmails)) {
    //         $dbDuplicateMessages['Email'] = array_map(fn($v) => $v, $existingEmails);
    //     }

    //     // Phone
    //     $existingPhones = Worker::whereIn('mobile_phone', array_keys($phoneToRow))->pluck('mobile_phone')->toArray();
    //     if (!empty($existingPhones)) {
    //         $dbDuplicateMessages['Phone Number'] = array_map(fn($v) => $v, $existingPhones);
    //     }

    //     // NI
    //     $existingNIs = Worker::whereIn('ni_number', array_keys($niToRow))->pluck('ni_number')->toArray();
    //     if (!empty($existingNIs)) {
    //         $dbDuplicateMessages['NI Number'] = array_map(fn($v) => $v, $existingNIs);
    //     }

    //     if (!empty($dbDuplicateMessages)) {
    //         $messages = [];
    //         foreach ($dbDuplicateMessages as $field => $values) {
    //             $messages[] = $field . ' "' . implode('", "', $values) . '"';
    //         }
    //         return back()->withErrors([
    //             'csv_file' => implode("\n", $messages) . ' already exists in the database.'
    //         ]);
    //     }

    //     /* -------- Insert -------- */
    //     DB::transaction(fn() => Worker::insert($rows));

    //     return redirect()
    //         ->route('workers.index')
    //         ->with('success', 'Workers imported successfully');
    // }

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

        $rows = [];

        // Collect CSV values
        $emailRows = [];
        $phoneRows = [];
        $niRows    = [];

        // Collect values for DB check
        $emailToRow = [];
        $phoneToRow = [];
        $niToRow    = [];

        /* ---------------- READ CSV ---------------- */
        while (($data = fgetcsv($file)) !== false) {

            $forename = trim($data[1] ?? '');
            $email    = strtolower(trim($data[12] ?? ''));
            $ni       = strtoupper(trim($data[13] ?? ''));

            $rawMobilePhone = trim($data[10] ?? '');
            $rawHomePhone   = trim($data[11] ?? '');

            /* -------- Required fields -------- */
            if ($forename === '') {
                return back()->withErrors(['csv_file' => 'Forename is required']);
            }

            if ($rawMobilePhone === '') {
                return back()->withErrors(['csv_file' => 'Mobile Phone is required']);
            }

            if ($ni === '') {
                return back()->withErrors(['csv_file' => 'NI Number is required']);
            }

            /* -------- Email validation -------- */
            $emailValidator = Validator::make(
                ['email' => $email],
                ['email' => ['required', 'email', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/']]
            );

            if ($emailValidator->fails()) {
                return back()->withErrors(['csv_file' => 'Invalid email format']);
            }

            /* -------- Phone validation -------- */
            if (!ctype_digit($rawMobilePhone) || strlen($rawMobilePhone) > 20) {
                return back()->withErrors([
                    'csv_file' => 'Mobile Phone must contain only digits and max 20 digits'
                ]);
            }

            if ($rawHomePhone !== '' && (!ctype_digit($rawHomePhone) || strlen($rawHomePhone) > 20)) {
                return back()->withErrors([
                    'csv_file' => 'Home Phone must contain only digits and max 20 digits'
                ]);
            }

            $phone     = $rawMobilePhone;
            $homePhone = $rawHomePhone ?: null;

            /* -------- Collect for CSV duplicate check -------- */
            $emailRows[$email][] = $email;
            $phoneRows[$phone][] = $phone;
            $niRows[$ni][]       = $ni;

            /* -------- Collect for DB duplicate check -------- */
            $emailToRow[$email] = true;
            $phoneToRow[$phone] = true;
            $niToRow[$ni]       = true;

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

        /* ---------------- CSV DUPLICATE CHECK (GROUPED PER FIELD) ---------------- */

        $csvMessages = [];

        // EMAILS
        $duplicateEmails = [];
        foreach ($emailRows as $email => $values) {
            if (count($values) > 1) {
                $duplicateEmails[] = $email;
            }
        }

        if (!empty($duplicateEmails)) {
            $csvMessages[] =
                'Duplicate Email "' . implode('","', $duplicateEmails) . '"';
        }

        // PHONE NUMBERS
        $duplicatePhones = [];
        foreach ($phoneRows as $phone => $values) {
            if (count($values) > 1) {
                $duplicatePhones[] = $phone;
            }
        }

        if (!empty($duplicatePhones)) {
            $csvMessages[] =
                'Phone Number "' . implode('","', $duplicatePhones) . '"';
        }

        // NI NUMBERS
        $duplicateNIs = [];
        foreach ($niRows as $ni => $values) {
            if (count($values) > 1) {
                $duplicateNIs[] = $ni;
            }
        }

        if (!empty($duplicateNIs)) {
            $csvMessages[] =
                'NI Number "' . implode('","', $duplicateNIs) . '"';
        }

        if (!empty($csvMessages)) {
            return back()->withErrors([
                'csv_file' => implode("\n", $csvMessages) . ' found in CSV.'
            ]);
        }


        /* ---------------- DATABASE DUPLICATE CHECK ---------------- */
        $dbMessages = [];

        $existingEmails = Worker::whereIn('email', array_keys($emailToRow))
            ->pluck('email')
            ->toArray();

        if (!empty($existingEmails)) {
            $dbMessages[] =
                'Email "' . implode('","', $existingEmails) . '"';
        }

        $existingPhones = Worker::whereIn('mobile_phone', array_keys($phoneToRow))
            ->pluck('mobile_phone')
            ->toArray();

        if (!empty($existingPhones)) {
            $dbMessages[] =
                'Phone Number "' . implode('","', $existingPhones) . '"';
        }

        $existingNIs = Worker::whereIn('ni_number', array_keys($niToRow))
            ->pluck('ni_number')
            ->toArray();

        if (!empty($existingNIs)) {
            $dbMessages[] =
                'NI Number "' . implode('","', $existingNIs) . '"';
        }

        if (!empty($dbMessages)) {
            return back()->withErrors([
                'csv_file' => implode("\n", $dbMessages) . ' already exists in the database.'
            ]);
        }

        /* ---------------- INSERT ---------------- */
        DB::transaction(fn() => Worker::insert($rows));

        return redirect()
            ->route('workers.index')
            ->with('success', 'Workers imported successfully');
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
