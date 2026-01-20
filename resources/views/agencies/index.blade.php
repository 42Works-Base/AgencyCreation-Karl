<!DOCTYPE html>
<html>

<head>
    <title>Agency Inbox</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f1f1f1;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            border: none;
            outline: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #4caf50;
            color: white;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn-submit {
            background: #4f46e5;
            color: white;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        /* 🔔 Flash message styles */
        .flash-message {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            margin-bottom: 16px;
            border-radius: 6px;
            transition: opacity 0.4s ease;
        }

        .flash-message.success {
            background-color: #e6f7ec;
            color: #1e7f4f;
            border: 1px solid #b7ebcc;
        }

        .flash-message.error {
            background-color: #fdecea;
            color: #a4282a;
            border: 1px solid #f5c6cb;
        }

        .flash-close {
            background: transparent;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: inherit;
            line-height: 1;
        }

        .flash-close:hover {
            opacity: 0.7;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="top-bar">
            <h2>Agency Inbox</h2>

            <div style="display:flex; gap:10px;">
                <!-- 👥 Go to Worker Inbox -->
                <a href="{{ route('workers.index') }}" class="btn" style="background:#059669; color:white;">
                    👥 Worker Inbox
                </a>

                <!-- ➕ Create Agency -->
                <a href="{{ route('agencies.create') }}" class="btn btn-submit">
                    + Create Agency
                </a>
            </div>
        </div>


        {{-- ✅ Success Flash --}}
        @if (session('success'))
        <div class="flash-message success">
            <span>{{ session('success') }}</span>
            <button class="flash-close" onclick="closeFlash(this)">×</button>
        </div>
        @endif

        {{-- ❌ Error Flash --}}
        @if (session('error'))
        <div class="flash-message error">
            <span>{{ session('error') }}</span>
            <button class="flash-close" onclick="closeFlash(this)">×</button>
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Subdomain</th>
                    <th>Prefix</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($agencies as $agency)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $agency->name }}</td>
                    <td>{{ $agency->subdomain ?? '-' }}</td>
                    <td>{{ $agency->prefix }}</td>
                    <td>{{ $agency->type }}</td>
                    <td>
                        <!-- ✅ Edit -->
                        <a href="{{ route('agencies.edit', $agency) }}" class="btn btn-edit">
                            Edit
                        </a>

                        <!-- ❌ Delete -->
                        <form
                            action="{{ route('agencies.destroy', $agency) }}"
                            method="POST"
                            style="display:inline"
                            onsubmit="return confirm('Delete this agency?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">No agencies found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>

    <!-- 🧠 Flash Message JS -->
    <script>
        function closeFlash(button) {
            const flash = button.parentElement;
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }

        // Auto-hide after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.flash-message').forEach(flash => {
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 400);
            });
        }, 5000);
    </script>



</body>

</html>