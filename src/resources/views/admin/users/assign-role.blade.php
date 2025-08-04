@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('admin.assignRoleAndDepots', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <h3>Assign Role</h3>
    <div>
        @foreach ($roles as $role)
            <label>
                <input type="checkbox" name="role" value="{{ $role->name }}"
                    {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                {{ $role->name }}
            </label><br>
        @endforeach
    </div>

    <h3>Assign Depots</h3>
    <div>
        @foreach ($depots as $depot)
            <label>
                <input type="checkbox" name="depots[]" value="{{ $depot->id }}"
                    {{ $user->depots->contains($depot->id) ? 'checked' : '' }}>
                {{ $depot->name }}
            </label><br>
        @endforeach
    </div>

    <button type="submit">Save</button>
</form>