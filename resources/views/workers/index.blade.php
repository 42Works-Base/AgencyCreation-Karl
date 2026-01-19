<!DOCTYPE html>
<html>

<head>
    <title>Worker Inbox</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f5f7fb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #4f46e5;
            color: #fff;
        }

        .actions a,
        .actions button {
            margin-right: 10px;
            color: #003a8f;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
        }

        .flash-success {
            background: #e6fffa;
            color: #065f46;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">

        <h2 style="margin:0;">Worker Inbox</h2>

        <a href="{{ route('workers.import.form') }}"
            style="
        display:inline-block;
        margin-bottom:20px;
        padding:10px 16px;
        background:#4f46e5;
        color:#fff;
        text-decoration:none;
        border-radius:4px;
        font-size:14px;
   "
            onmouseover="this.style.backgroundColor='#4338ca'"
            onmouseout="this.style.backgroundColor='#4f46e5'">
            + Bulk Import Workers
        </a>
    </div>

    @if (session('success'))
    <div class="flash-success">
        {{ session('success') }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Worker Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Agency</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workers as $worker)
            <tr>
                <td>
                    {{ $worker->forename }}
                    {{ $worker->surname ? ' ' . $worker->surname : '' }}
                </td>
                <td>{{ $worker->mobile_phone }}</td>
                <td>{{ $worker->email }}</td>
                <td>{{ $worker->agency->name ?? '-' }}</td>
                <td class="actions">
                    <a href="{{ route('workers.edit', $worker) }}">Edit</a>

                    <form action="{{ route('workers.destroy', $worker) }}"
                        method="POST"
                        style="display:inline;"
                        onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No workers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <br>

    {{ $workers->links() }}

</body>

</html>