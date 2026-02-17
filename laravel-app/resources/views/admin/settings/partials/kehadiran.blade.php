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
