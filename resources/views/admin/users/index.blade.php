<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>

    <h1>Manage Users</h1>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Suspended?</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->is_suspended ? 'YES' : 'NO' }}</td>

                    <td>
                        @if (!$user->is_suspended)
                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit">Suspend</button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.unsuspend', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit">Un-Suspend</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</body>
</html>
