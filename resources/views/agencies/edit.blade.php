<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Agency</title>

    <style>
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

        input:focus,
        select:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
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

        .alert-error {
            background-color: #fdecea;
            color: #a4282a;
            border: 1px solid #f5c6cb;
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
        <h1>Edit Agency</h1>

        @if(session('success'))
        <div class="success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('agencies.update', $agency) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Agency Name --}}
            <div class="field">
                <label for="name">Agency Name <span class="error">*</span></label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $agency->name) }}">

                @error('name')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Agency Subdomain --}}
            <div class="field">
                <label for="subdomain">Agency Subdomain</label>
                <input
                    id="subdomain"
                    type="text"
                    name="subdomain"
                    value="{{ old('subdomain', $agency->subdomain) }}"
                    placeholder="example: myagency.com">

                @error('subdomain')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Agency Prefix --}}
            <div class="field">
                <label for="prefix">Agency Prefix <span class="error">*</span></label>
                <input
                    id="prefix"
                    type="text"
                    name="prefix"
                    value="{{ old('prefix', $agency->prefix) }}">

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
                        {{ old('type', $agency->type) === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                    @endforeach
                </select>

                @error('type')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Logo --}}
            <div class="field">
                <label for="logo">Logo (jpg, jpeg, png) — max 2MB</label>
                <input id="logo" type="file" name="logo" accept=".jpg,.jpeg,.png">
                <div id="logo_error" class="error">@error('logo'){{ $message }}@enderror</div>

                {{-- existing saved preview --}}
                @if($agency->logo_path)
                <div style="margin-top:8px;">
                    <img id="logo_saved_preview" src="{{ asset('storage/'.$agency->logo_path) }}" alt="logo"
                        style="height:64px;border:1px solid #ddd;border-radius:4px;">
                </div>
                @else
                <div style="margin-top:8px;">
                    <img id="logo_saved_preview" src="" alt="logo" style="display:none;height:64px;border:1px solid #ddd;border-radius:4px;">
                </div>
                @endif

                {{-- new-file live preview (hidden initially) --}}
                <div style="margin-top:8px;">
                    <img id="logo_new_preview" src="" alt="new logo preview" style="display:none;height:64px;border:1px solid #4f46e5;border-radius:4px;">
                </div>
            </div>

            {{-- Email Logo --}}
            <div class="field">
                <label for="email_logo">Email Logo (jpg, jpeg, png) — max 2MB</label>
                <input id="email_logo" type="file" name="email_logo" accept=".jpg,.jpeg,.png">
                <div id="email_logo_error" class="error">@error('email_logo'){{ $message }}@enderror</div>

                @if($agency->email_logo_path)
                <div style="margin-top:8px;">
                    <img id="email_logo_saved_preview" src="{{ asset('storage/'.$agency->email_logo_path) }}" alt="email logo"
                        style="height:48px;border:1px solid #ddd;border-radius:4px;">
                </div>
                @else
                <div style="margin-top:8px;">
                    <img id="email_logo_saved_preview" src="" alt="email logo" style="display:none;height:48px;border:1px solid #ddd;border-radius:4px;">
                </div>
                @endif

                <div style="margin-top:8px;">
                    <img id="email_logo_new_preview" src="" alt="new email logo preview" style="display:none;height:48px;border:1px solid #4f46e5;border-radius:4px;">
                </div>
            </div>

            {{-- Background Image --}}
            <div class="field">
                <label for="background_image">Background Image (jpg, jpeg, png) — max 2MB</label>
                <input id="background_image" type="file" name="background_image" accept=".jpg,.jpeg,.png">
                <div id="background_image_error" class="error">@error('background_image'){{ $message }}@enderror</div>

                @if($agency->background_image_path)
                <div style="margin-top:8px;">
                    <img id="background_saved_preview" src="{{ asset('storage/'.$agency->background_image_path) }}" alt="background"
                        style="height:64px;border:1px solid #ddd;border-radius:4px;">
                </div>
                @else
                <div style="margin-top:8px;">
                    <img id="background_saved_preview" src="" alt="background" style="display:none;height:64px;border:1px solid #ddd;border-radius:4px;">
                </div>
                @endif

                <div style="margin-top:8px;">
                    <img id="background_new_preview" src="" alt="new background preview" style="display:none;height:64px;border:1px solid #4f46e5;border-radius:4px;">
                </div>
            </div>



            <div class="field">
                <label>Skin Color</label>

                <div style="display:flex;gap:12px;align-items:center;">
                    <input type="color"
                        id="skin_color"
                        name="skin_color"
                        value="{{ old('skin_color', $agency->skin_color ?? '#000000') }}">

                    <input type="text"
                        readonly
                        id="skin_color_text"
                        value="{{ old('skin_color', $agency->skin_color ?? '#000000') }}"
                        style="width:110px;">
                </div>
            </div>

            <script>
                const picker = document.getElementById('skin_color');
                const text = document.getElementById('skin_color_text');

                picker.addEventListener('input', () => {
                    text.value = picker.value;
                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ALLOWED_EXT = ['jpg', 'jpeg', 'png'];
                    const MAX_BYTES = 2 * 1024 * 1024; // 2MB

                    function extFromName(name) {
                        const p = name.lastIndexOf('.');
                        return p === -1 ? '' : name.slice(p + 1).toLowerCase();
                    }

                    function bytesToSize(bytes) {
                        if (bytes === 0) return '0 B';
                        const sizes = ['B', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(1024));
                        return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
                    }

                    function setupFileInput(inputId, savedImgId, newImgId, errorId) {
                        const input = document.getElementById(inputId);
                        const savedImg = document.getElementById(savedImgId);
                        const newImg = document.getElementById(newImgId);
                        const errorEl = document.getElementById(errorId);

                        input.addEventListener('change', function(evt) {
                            errorEl.textContent = '';


                
                            const file = input.files && input.files[0];
                            if (!file) {
                                // no file selected -> hide new preview, keep showing saved preview (if any)
                                if (newImg) {
                                    // newImg.style.display = 'none';
                                    // newImg.src = '';
                                }
                                if (savedImg && savedImg.getAttribute('src')) {
                                    // savedImg.style.display = '';
                                }
                                return;
                            }

                            // validate size
                            if (file.size > MAX_BYTES) {
                                errorEl.textContent = 'File is too large (' + bytesToSize(file.size) + '). Max is ' + bytesToSize(MAX_BYTES) + '.';
                                input.value = ''; // reset file input
                                return;
                            }

                            // validate extension
                            const ext = extFromName(file.name);
                            if (!ALLOWED_EXT.includes(ext)) {
                                errorEl.textContent = 'Invalid file type. Allowed: ' + ALLOWED_EXT.join(', ');
                                input.value = '';
                                return;
                            }

                            // validate mime optionally
                            if (!file.type.startsWith('image/')) {
                                errorEl.textContent = 'Invalid file type (not image).';
                                input.value = '';
                                return;
                            }

                            // show new preview
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                if (newImg) {
                                    newImg.src = e.target.result;
                                    newImg.style.display = '';
                                }
                                if (savedImg) {
                                    // hide saved preview to avoid confusion
                                    savedImg.style.display = 'none';
                                }
                            };
                            reader.readAsDataURL(file);
                        });
                    }

                    setupFileInput('logo', 'logo_saved_preview', 'logo_new_preview', 'logo_error');
                    setupFileInput('email_logo', 'email_logo_saved_preview', 'email_logo_new_preview', 'email_logo_error');
                    setupFileInput('background_image', 'background_saved_preview', 'background_new_preview', 'background_image_error');
                });
            </script>


            <div style="margin-top: 14px;">
                <button type="submit">Update Agency</button>
            </div>
        </form>

        <div class="back-link">
            <a href="{{ route('agencies.index') }}">← Back to agency list</a>
        </div>
    </div>

</body>

</html>