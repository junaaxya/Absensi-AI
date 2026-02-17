@if (in_array('face_recognition', $allowedTabs, true))
    @include('admin.settings.partials.tabs.face_recognition')
@endif

@if (in_array('notifikasi', $allowedTabs, true))
    @include('admin.settings.partials.tabs.notifikasi')
@endif

@if (in_array('export', $allowedTabs, true))
    @include('admin.settings.partials.tabs.export')
@endif

@if (in_array('backup', $allowedTabs, true))
    @include('admin.settings.partials.tabs.backup')
@endif
