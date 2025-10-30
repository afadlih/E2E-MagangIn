
      <!-- Sidebar -->
      <div class="sidebar sidebar-style-2" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="img/MagangIn.png"
                alt="navbar brand"
                class="navbar-brand"
                height="100"
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
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item">
                <a
                  data-bs-toggle="collapse"
                  href="#dashboard"
                  class="collapsed"
                  aria-expanded="false"
                >
                   <a href="{{ url('/dashboard-admin') }}">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#base">
                  <i class="fas fa-user-friends"></i>
                  <p>Manajemen Pengguna</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="base">
                  <ul class="nav nav-collapse">
                    <li>
                       <a href="{{ url('admin') }}">
                        <span class="sub-item">Data Admin</span>
                      </a>
                    </li>
                    <li>
                       <a href="{{ url('dosen') }}">
                        <span class="sub-item">Data Dosen</span>
                      </a>
                    </li>
                    <li>
                    <li>
                      <a href="{{ url('mahasiswa') }}">
                        <span class="sub-item">Data Mahasiswa</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a href="{{ url('perusahaan-mitra') }}">
                  <i class="fas fa-building"></i>
                  <p>Manajemen Perusahaan Mitra</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('periode') }}">
                  <i class="fas fa-hourglass-half"></i>
                  <p>Manajemen Periode Magang</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('prodi') }}">
                  <i class="fas fa-book"></i>
                  <p>Manajemen Program Studi</p>
                </a>
              </li>
              <!-- Manajemen Lowongan Magang -->
              <li class="nav-item">
                <a href="{{ url('lowongan') }}">
                  <i class="fas fa-briefcase"></i>
                  <p>Manajemen Lowongan Magang</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('pengajuan-magang') }}">
                  <i class="fas fa-laptop-code"></i>
                  <p>Manajemen Kegiatan Magang</p>
                </a>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->