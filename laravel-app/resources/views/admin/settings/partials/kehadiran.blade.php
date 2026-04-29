@if (in_array('kebijakan_absensi', $allowedTabs, true))
    @include('admin.settings.partials.tabs.kebijakan_absensi')
@endif

@if (in_array('shift_kerja', $allowedTabs, true))
    @include('admin.settings.partials.tabs.shift_kerja')
@endif

@if (in_array('hari_libur', $allowedTabs, true))
    @include('admin.settings.partials.tabs.hari_libur')
@endif

@if (in_array('tipe_cuti', $allowedTabs, true))
    @include('admin.settings.partials.tabs.tipe_cuti')
@endif

@if (in_array('poin_pelanggaran', $allowedTabs, true))
    @include('admin.settings.partials.tabs.poin_pelanggaran')
@endif

@if (in_array('anti_cheat', $allowedTabs, true))
    @include('admin.settings.partials.tabs.anti_cheat')
@endif
