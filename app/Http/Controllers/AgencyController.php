<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AgencyController extends Controller
{
    /**
     * Show the create agency form
     */

    public function index()
    {
        $agencies = Agency::orderBy('id', 'desc')->get();

        return view('agencies.index', compact('agencies'));
    }

    public function create()
    {

        $types = config('agency.types');

        return view('agencies.create', compact('types'));
    }

    /**
     * Store agency in database
     */
    public function store(Request $request)
    {


        // $allowedTypes = config('agency.types');

        // 1️⃣ Manual validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'subdomain' => 'nullable|string|max:100',
            'prefix'    => 'required|string|max:50',
            'type'      => ['required', 'string', 'max:100', Rule::in(config('agency.types'))],
        ]);

        // 2️⃣ If validation fails
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // 3️⃣ Create agency
            Agency::create($validator->validated());

            // 4️⃣ Success redirect
            return redirect()
                ->route('agencies.index')
                ->with('success', 'Agency created successfully.');
        } catch (QueryException $e) {

            // ✅ Log database error
            Log::error('Agency creation failed (DB)', [
                'error'   => $e->getMessage(),
                'sql'     => $e->getSql(),
                'bindings' => $e->getBindings(),
                'request' => $request->all(),
            ]);

            // 5️⃣ Database-related errors
            return redirect()
                ->back()
                ->with('error', 'Database error occurred.')
                ->withInput();
        } catch (\Throwable $e) {
            // ✅ Log ANY unexpected error
            Log::critical('Agency creation failed (Unexpected)', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            // 6️⃣ Any other unknown error
            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.')
                ->withInput();
        }
    }

    public function edit(Agency $agency)
    {
        $types = config('agency.types');

        return view('agencies.edit', compact('agency', 'types'));
    }

    public function update(Request $request, Agency $agency)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'subdomain' => 'nullable|string|max:100',
            'prefix'    => 'required|string|max:50',
            'type'      => ['required', 'string', 'max:100', Rule::in(config('agency.types'))],

            // branding
            'logo'             => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'email_logo'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'background_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',

            // color
            'skin_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $validator->validated();

            // 🔹 Logo
            if ($request->hasFile('logo')) {
                if ($agency->logo_path) {
                    Storage::disk('public')->delete($agency->logo_path);
                }
                $data['logo_path'] = $request->file('logo')
                    ->store('agencies/logos', 'public');
            }

            // 🔹 Email logo
            if ($request->hasFile('email_logo')) {
                if ($agency->email_logo_path) {
                    Storage::disk('public')->delete($agency->email_logo_path);
                }
                $data['email_logo_path'] = $request->file('email_logo')
                    ->store('agencies/email_logos', 'public');  
            }

            // 🔹 Background image
            if ($request->hasFile('background_image')) {
                if ($agency->background_image_path) {
                    Storage::disk('public')->delete($agency->background_image_path);
                }
                $data['background_image_path'] = $request->file('background_image')
                    ->store('agencies/backgrounds', 'public');
            }

            $agency->update($data);


            return redirect()
                ->route('agencies.index')
                ->with('success', 'Agency updated successfully.');
        } catch (QueryException $e) {
            Log::error('Agency update failed (DB)', [
                'id'       => $agency->id,
                'error'    => $e->getMessage(),
                'request'  => $request->all(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Database error occurred.')
                ->withInput();
        } catch (\Throwable $e) {
            Log::critical('Agency update failed (Unexpected)', [
                'id'      => $agency->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Agency $agency)
    {
        try {
            $agency->delete();

            return redirect()
                ->route('agencies.index')
                ->with('success', 'Agency deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Agency delete failed', [
                'id' => $agency->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Unable to delete agency.');
        }
    }
}
