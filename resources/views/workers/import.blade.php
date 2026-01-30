<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bulk Import</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
        }

        .container {
            width: 420px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            background: #4f46e5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 15px;
        }

        .btn:hover {
            background: #4338ca;
        }

        .field {
            text-align: left;
            margin-bottom: 20px;
        }

        .error-text {
            color: #d93025;
            font-size: 13px;
            margin-top: 6px;
        }

        .flash-success {
            background: #e6fffa;
            color: #065f46;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: left;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: left;
        }

        .sample-link {
            margin-top: 20px;
            display: block;
            color: #003a8f;
            text-decoration: none;
        }

        .sample-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>Bulk Import</h2>

        {{-- Flash Success --}}
        @if (session('success'))
        <div class="flash-success">
            {{ session('success') }}
        </div>
        @endif

        {{-- Flash Error --}}
        @if ($errors->any() && !$errors->has('agency_id') && !$errors->has('csv_file'))
        <div class="flash-error">
            Something went wrong. Please fix the errors below.
        </div>
        @endif

        <form method="POST" action="{{ route('workers.import') }}" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- Agency --}}
            <div class="field">
                <label>Select Agency to import to</label>
                <select name="agency_id">
                    <option value="">Select Agency</option>
                    @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}"
                        {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                        {{ $agency->name }}
                    </option>
                    @endforeach
                </select>

                @error('agency_id')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- CSV Upload --}}
            <div class="field">
                <label>Select CSV file</label>
                <input type="file" name="csv_file" accept=".csv,.txt">

                @error('csv_file')
                <div class="error-text" style="white-space: pre-line;">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <button type="submit" class="btn">Import</button>
        </form>

        <a href="{{ route('workers.sample.csv') }}" class="sample-link">
            Download Sample CSV
        </a>

        <br>

        <a href="{{ route('workers.index') }}"
            style="
        display:inline-block;
        margin-top:15px;
        color:#003a8f;
        text-decoration:none;
        font-size:14px;
   ">
            ← Back to Worker Inbox
        </a>


    </div>

</body>

</html>