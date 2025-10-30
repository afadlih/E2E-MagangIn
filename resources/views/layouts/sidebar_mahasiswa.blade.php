<!-- Sidebar -->
      <div class="sidebar sidebar-style-2" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="{{ asset('img/MagangIn.png') }}"
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
                <a href="{{ url('/dashboard-mahasiswa') }}">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('lowongan.rekomendasi') }}">
                  <i class="fas fa-briefcase"></i>
                  <p>Rekomendasi Magang</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('pengajuan-magang-mhs') }}">
                  <i class="fas fa-file-signature"></i>
                  <p>Status Pengajuan Magang</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/message') }}">
                  <i class="fas fa-envelope"></i>
                  <p>Message</p>
                </a>
              </li>
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#charts4">
                  <i class="fas fa-chart-line"></i>
                  <p>Monitoring dan Evaluasi Magang</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="charts4">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="{{ url('log-aktivitas-mhs') }}">
                        <span class="sub-item">Isi log harian</span>
                      </a>
                    </li>
                    <li>
                      <a href="{{ url('feedback-magang') }}">
                        <span class="sub-item">Feedback pengalaman</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->