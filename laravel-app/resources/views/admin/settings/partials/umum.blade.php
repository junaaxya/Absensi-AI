@if (in_array('jam_kerja', $allowedTabs, true))
    @include('admin.settings.partials.tabs.jam_kerja')
@endif

@if (in_array('lokasi', $allowedTabs, true))
    @include('admin.settings.partials.tabs.lokasi')
@endif

@if (in_array('profil_perusahaan', $allowedTabs, true))
    @include('admin.settings.partials.tabs.profil_perusahaan')
@endif
