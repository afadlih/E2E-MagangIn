<div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.html" class="logo">
                <img
                  src="assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      @php
                      if (!Auth::check()) {
                        header("Location: " . route('login'));
                        exit;
                    }
                    $user = Auth::user();

                    if ($user->level && $user->level->level_name === 'mahasiswa' && $user->mahasiswa) {
                        $displayName = $user->mahasiswa->full_name;
                        $profilePicture = $user->mahasiswa->profile_picture
                            ? Storage::url($user->mahasiswa->profile_picture)
                            : asset('img/user.png');
                    } elseif ($user->level && $user->level->level_name === 'dosen' && $user->dosen) {
                        $displayName = $user->dosen->nama;
                        $profilePicture = $user->dosen->profile_picture
                            ? Storage::url($user->dosen->profile_picture)
                            : asset('img/user.png');
                    } elseif ($user->level && $user->level->level_name === 'admin' && $user->admin) {
                        $displayName = $user->admin->nama;
                        $profilePicture = $user->admin->profile_picture
                            ? Storage::url($user->admin->profile_picture)
                            : asset('img/user.png');
                    } else {
                        $displayName = $user->username;
                        $profilePicture = asset('img/user.png');
                    }
                    @endphp
                      <img
                        src="{{ asset($profilePicture) }}"
                        alt="..."
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                  <span class="profile-username">
                    <span class="op-7">Hi,</span> {{ $displayName }}
                  </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img
                              src="{{ asset($profilePicture) }}"
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                          <p class="text-sm text-muted">{{ $displayName }}</p>
                           @php
                              $user = Auth::user();
                              $url = '#'; // default

                              if ($user->hasRole('admin')) {
                                  $url = url('/admin/' . $user->admin->admin_id . '/show_admin');
                              } elseif ($user->hasRole('dosen')) {
                                  $url = url('/dosen/' . $user->dosen->dosen_id . '/show_dosen');
                              } elseif ($user->hasRole('mahasiswa')) {
                                  $url = url('/mahasiswa/' . $user->mahasiswa->mhs_nim . '/show_mhs');
                              }
                          @endphp

                          <button onclick="modalAction('{{ $url }}')" class="btn btn-xs btn-secondary btn-sm">
                            View Profile
                          </button>




                          </div>
                        </div>
                      </li>
                     <li>
                        <div class="dropdown-divider"></div>

                        <a href="#" class="dropdown-item" onclick="showLogoutConfirmation(event)">
                          Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                          @csrf
                        </form>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
          function modalAction(url = '') {
            $('#myModal .modal-content').load(url, function () {
              $('#myModal').modal('show');
            });
          }

          function showLogoutConfirmation(event) {
            event.preventDefault();
            Swal.fire({
              title: 'Yakin ingin logout?',
              text: "Anda akan keluar dari sesi ini.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, logout',
              cancelButtonText: 'Batal'
            }).then((result) => {
              if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
              }
            });
          }
        </script>