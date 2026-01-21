<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Create Agency</title>

    <style>
        /* body { font-family: Arial, sans-serif; }
        .error { color: #c00; font-size: 14px; }
        .success { color: #080; font-size: 14px; }
        label { display:block; margin-top:10px; font-weight: bold; }
        input[type="text"], select {
            width: 320px;
            padding: 6px;
            margin-top: 4px;
        }
        .field { margin-bottom: 12px; }
        button { padding: 6px 12px; } */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        h1 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 520px;
            background-color: #fff;
            padding: 28px 32px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        small {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: #777;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #4338ca;
        }

        .success {
            background-color: #e6f7ec;
            color: #1e7f4f;
            border: 1px solid #b7ebcc;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .error {
            color: #c00;
            font-size: 13px;
            margin-top: 4px;
        }

        .alert-error {
            background-color: #fdecea;
            color: #a4282a;
            border: 1px solid #f5c6cb;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .back-link {
            margin-top: 18px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            color: #4f46e5;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Create Agency</h1>

        {{-- Success message --}}
        @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
        @endif

        {{-- General error message (DB / unknown errors) --}}
        @if (session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
        @endif

        <form id="agency-form" method="POST" action="{{ route('agencies.store') }}">
            @csrf

            {{-- Agency Name --}}
            <div class="field">
                <label for="name">Agency Name <span class="error">*</span></label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    >

                @error('name')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Agency Subdomain --}}
            <div class="field">
                <label for="subdomain">Agency Subdomain </label>
                <!-- (optional)</label> -->
                <input
                    id="subdomain"
                    name="subdomain"
                    type="text"
                    value="{{ old('subdomain') }}"
                    placeholder="example: myagency.com">
                <!-- <small>Only the subdomain part (no protocol).</small> -->
    
                @error('subdomain')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Agency Prefix --}}
            <div class="field">
                <label for="prefix">Agency Prefix <span class="error">*</span></label>
                <input
                    id="prefix"
                    name="prefix"
                    type="text"
                    value="{{ old('prefix') }}"
                    >

                @error('prefix')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Agency Type --}}
            <div class="field">
                <label for="type">Agency Type <span class="error">*</span></label>
                <select id="type" name="type">
                    <option value="">-- Select type --</option>

                    @foreach ($types as $type)
                    <option
                        value="{{ $type }}"
                        {{ old('type') === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                    @endforeach
                </select>

                @error('type')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 14px;">
                <button type="submit">Save Agency</button>
            </div>
        </form>

        <div class="back-link">
            <a href="{{ url('/agencies') }}">← Back to agency list</a>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('agency-form');
    if (!form) return;

    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.dataset.oldText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Saving...';
    });

    window.addEventListener('pageshow', function () {
        submitBtn.disabled = false;
        if (submitBtn.dataset.oldText) {
            submitBtn.innerHTML = submitBtn.dataset.oldText;
        }
    });
});
</script>



</body>

</html>