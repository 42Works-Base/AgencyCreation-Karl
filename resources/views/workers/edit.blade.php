<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Worker</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
        }

        .container {
            width: 720px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #003a8f;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h4 {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 20px;
            color: #374151;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .field {
            margin-bottom: 12px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 9px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .error-text {
            color: #d93025;
            font-size: 13px;
            margin-top: 4px;
        }

        .btn {
            background: #003a8f;
            color: #fff;
            padding: 10px;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 4px;
            font-size: 15px;
        }

        .btn:hover {
            background: #002b6d;
        }

        /* .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #003a8f;
        } */

        .back-link {
            margin-top: 18px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            color: #4f46e5;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>Edit Worker</h2>

        <form method="POST" action="{{ route('workers.update', $worker) }}" novalidate>
            @csrf
            @method('PUT')

            {{-- ================= AGENCY ================= --}}
            <div class="section">
                <h4>Agency</h4>

                <div class="grid">
                    <div class="field">
                        <label>Agency *</label>
                        <select name="agency_id">
                            @foreach ($agencies as $agency)
                            <option value="{{ $agency->id }}"
                                {{ old('agency_id', $worker->agency_id) == $agency->id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>
                            @endforeach
                        </select>

                        @error('agency_id')
                        <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>


            {{-- ================= PERSONAL ================= --}}
            <div class="section">
                <h4>Personal Details</h4>

                <div class="grid">
                    <div class="field">
                        <label>Forename *</label>
                        <input type="text" name="forename" value="{{ old('forename', $worker->forename) }}">
                        @error('forename') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Surname</label>
                        <input type="text" name="surname" value="{{ old('surname', $worker->surname) }}">
                    </div>

                    <div class="field">
                        <label>Title</label>
                        <input type="text" name="title" value="{{ old('title', $worker->title) }}">
                    </div>

                    <div class="field">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth"
                            value="{{ old('date_of_birth', $worker->date_of_birth) }}">
                    </div>

                    <div class="field">
                        <label>Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $worker->nationality) }}">
                    </div>
                </div>
            </div>

            {{-- ================= CONTACT ================= --}}
            <div class="section">
                <h4>Contact</h4>

                <div class="grid">
                    <div class="field">
                        <label>Email *</label>
                        <input type="email" name="email" value="{{ old('email', $worker->email) }}">
                        @error('email') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Mobile Phone *</label>
                        <input type="text" name="mobile_phone" maxlength="20"
                            value="{{ old('mobile_phone', $worker->mobile_phone) }}">
                        @error('mobile_phone') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label>Home Phone</label>
                        <input type="text" name="home_phone" maxlength="20"
                            value="{{ old('home_phone', $worker->home_phone) }}">
                        @error('home_phone') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ================= ADDRESS ================= --}}
            <div class="section">
                <h4>Address</h4>

                <div class="grid">
                    <div class="field"><label>Address 1</label><input type="text" name="address1" value="{{ old('address1', $worker->address1) }}"></div>
                    <div class="field"><label>Address 2</label><input type="text" name="address2" value="{{ old('address2', $worker->address2) }}"></div>
                    <div class="field"><label>City</label><input type="text" name="city" value="{{ old('city', $worker->city) }}"></div>
                    <div class="field"><label>County</label><input type="text" name="county" value="{{ old('county', $worker->county) }}"></div>
                    <div class="field"><label>Postcode</label><input type="text" name="postcode" value="{{ old('postcode', $worker->postcode) }}"></div>
                    <div class="field"><label>Country</label><input type="text" name="country" value="{{ old('country', $worker->country) }}"></div>
                </div>
            </div>

            {{-- ================= COMPLIANCE ================= --}}
            <div class="section">
                <h4>Compliance</h4>

                <div class="grid">
                    <div class="field">
                        <label>NI Number *</label>
                        <input type="text" name="ni_number" value="{{ old('ni_number', $worker->ni_number) }}">
                        @error('ni_number') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ================= EMPLOYMENT ================= --}}
            <div class="section">
                <h4>Employment</h4>

                <div class="grid">
                    <div class="field"><label>Job Title</label><input type="text" name="job_title" value="{{ old('job_title', $worker->job_title) }}"></div>
                    <div class="field"><label>End Client</label><input type="text" name="end_client" value="{{ old('end_client', $worker->end_client) }}"></div>
                    <div class="field"><label>Start Date</label><input type="date" name="start_date" value="{{ old('start_date', $worker->start_date) }}"></div>
                </div>
            </div>

            {{-- ================= BANKING ================= --}}
            <div class="section">
                <h4>Banking</h4>

                <div class="grid">
                    <div class="field"><label>Account No</label><input type="text" name="account_no" value="{{ old('account_no', $worker->account_no) }}"></div>
                    <div class="field"><label>Sort Code</label><input type="text" name="sort_code" value="{{ old('sort_code', $worker->sort_code) }}"></div>
                    <div class="field"><label>Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name', $worker->bank_name) }}"></div>
                    <div class="field"><label>Branch</label><input type="text" name="branch" value="{{ old('branch', $worker->branch) }}"></div>
                    <div class="field"><label>BS Ref</label><input type="text" name="bs_ref" value="{{ old('bs_ref', $worker->bs_ref) }}"></div>
                </div>
            </div>

            {{-- ================= EXTERNAL ================= --}}
            <div class="section">
                <h4>External / Integrations</h4>

                <div class="grid">
                    <div class="field"><label>Sharecode</label><input type="text" name="sharecode" value="{{ old('sharecode', $worker->sharecode) }}"></div>
                    <div class="field"><label>External ID</label><input type="text" name="external_id" value="{{ old('external_id', $worker->external_id) }}"></div>
                    <div class="field"><label>Signify</label><input type="text" name="signify" value="{{ old('signify', $worker->signify) }}"></div>
                    <div class="field"><label>Venatu</label><input type="text" name="venatu" value="{{ old('venatu', $worker->venatu) }}"></div>
                </div>
            </div>

            <button class="btn">Update Worker</button>
        </form>

        <div class="back-link"></div>
        <a href="{{ route('workers.index') }}" class="back-link">
            ← Back to Worker Inbox
        </a>

    </div>

</body>

</html>