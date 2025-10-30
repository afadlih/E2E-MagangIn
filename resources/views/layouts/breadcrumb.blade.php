
 @php
        if (!Auth::check()) {
            header("Location: " . route('login'));
            exit;
        }

        $user = Auth::user();
        $displayName = $user->username;

        if ($user->level && $user->level->level_name === 'mahasiswa' && $user->mahasiswa) {
            $displayName = $user->mahasiswa->full_name;
        } elseif ($user->level && $user->level->level_name === 'dosen' && $user->dosen) {
            $displayName = $user->dosen->nama;
        } elseif ($user->level && $user->level->level_name === 'admin' && $user->admin) {
            $displayName = $user->admin->nama;
        }
    @endphp<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>{{ $breadcrumb->title}}, {{ $displayName }}</h1></div>
        </div>
    </div>
</section>
