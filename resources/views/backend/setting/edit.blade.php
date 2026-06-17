@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1><i class="fas fa-cog mr-2 text-primary"></i>Settings</h1>
    </div>
@stop

@section('content')
    <form id="settings-form" action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-building mr-2"></i>Business Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="bname" label="Business Name" :value="old('bname', $setting->bname)" placeholder="Your business name" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="phone" label="Phone" :value="old('phone', $setting->phone)" placeholder="+1 234 567 8900" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="email" label="Email" type="email" :value="old('email', $setting->email)" placeholder="info@example.com" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="email2" label="Secondary Email" type="email" :value="old('email2', $setting->email2)" placeholder="support@example.com" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="whatsapp" label="WhatsApp" :value="old('whatsapp', $setting->whatsapp)" placeholder="+1 234 567 8900" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="address" label="Address" :value="old('address', $setting->address)" placeholder="123 Main St, City" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-share-alt mr-2"></i>Social Media</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="facebook" label="Facebook" :value="old('facebook', $setting->facebook)" placeholder="https://facebook.com/..." />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="instagram" label="Instagram" :value="old('instagram', $setting->instagram)" placeholder="https://instagram.com/..." />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="twitter" label="Twitter / X" :value="old('twitter', $setting->twitter)" placeholder="https://twitter.com/..." />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="linkedin" label="LinkedIn" :value="old('linkedin', $setting->linkedin)" placeholder="https://linkedin.com/..." />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="youtube" label="YouTube" :value="old('youtube', $setting->youtube)" placeholder="https://youtube.com/..." />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-code mr-2"></i>Google Analytics (gtag)</h3>
                    </div>
                    <div class="card-body">
                        <textarea name="gtag" class="form-control" rows="6" placeholder="&lt;!-- Google tag (gtag.js) --&gt; ...">{{ old('gtag', $setting->gtag) }}</textarea>
                        <small class="form-text text-muted">Paste your full Google Analytics gtag snippet here. It will be injected into the <code>&lt;head&gt;</code> of the frontend.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-image mr-2"></i>Logo</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img id="logo-preview" src="{{ $setting->getFirstMediaUrl('logo', 'webp') ?: asset('no-image.webp') }}"
                                style="max-width: 100%; max-height: 120px; border: 1px solid #dee2e6; border-radius: 4px; padding: 8px;">
                        </div>
                        <div class="custom-file">
                            <input type="file" name="logo" id="logo-input" class="custom-file-input" accept="image/*">
                            <label class="custom-file-label" for="logo-input">Choose file</label>
                        </div>
                        <small class="form-text text-muted mt-1">Recommended: transparent PNG, max 2MB</small>
                    </div>
                </div>

                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-star mr-2"></i>Favicon</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img id="favicon-preview" src="{{ $setting->getFirstMediaUrl('favicon', 'webp') ?: asset('no-image.webp') }}"
                                style="width: 48px; height: 48px; border: 1px solid #dee2e6; border-radius: 4px; padding: 4px; object-fit: contain;">
                        </div>
                        <div class="custom-file">
                            <input type="file" name="favicon" id="favicon-input" class="custom-file-input" accept="image/*">
                            <label class="custom-file-label" for="favicon-input">Choose file</label>
                        </div>
                        <small class="form-text text-muted mt-1">Square image, 32x32 or 48x48, max 1MB</small>
                    </div>
                </div>

                <div class="card card-outline card-danger">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0"><i class="fas fa-images mr-2"></i>Banners</h3>
                        <label for="banners-input" class="btn btn-sm btn-success mb-0" style="cursor:pointer;">
                            <i class="fas fa-plus"></i> Add
                        </label>
                        <input type="file" name="banners[]" id="banners-input" class="d-none" accept="image/*" multiple>
                    </div>
                    <div class="card-body">
                        @if($setting->getMedia('banners')->count())
                            <div id="banner-sortable" class="row">
                                @foreach($setting->getMedia('banners')->sortBy('order_column') as $media)
                                    <div class="col-6 mb-2 banner-item" data-media-id="{{ $media->id }}">
                                        <div class="position-relative" style="cursor: grab;">
                                            <img src="{{ $media->getUrl('webp') }}" class="img-fluid border rounded" style="height: 100px; width: 100%; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute remove-banner" style="top: 2px; right: 2px; padding: 0 4px; line-height: 1;" data-media-id="{{ $media->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <small class="d-block text-muted text-center mt-1"><i class="fas fa-grip-vertical"></i> Drag to reorder</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0 py-3">
                                <i class="fas fa-images fa-2x d-block mb-2"></i>
                                No banners yet. Click "Add" to upload.
                            </p>
                        @endif
                        <small class="form-text text-muted">Recommended: 1920x720px, max 5MB each. Drag to reorder.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block mb-5" id="save-settings-btn">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
@stop

@section('css')
<style>
.card-outline { border-top: 3px solid; }
.banner-item .position-relative:hover .remove-banner { opacity: 1; }
.remove-banner { opacity: 0.7; transition: opacity 0.2s; }
.banner-item.sortable-ghost { opacity: 0.4; }
.banner-item.sortable-chosen .position-relative { cursor: grabbing !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Preview logo
        $('#logo-input').on('change', function() {
            var file = this.files[0];
            if (file) {
                $('#logo-preview').attr('src', URL.createObjectURL(file));
                $(this).next('.custom-file-label').text(file.name);
            }
        });

        // Preview favicon
        $('#favicon-input').on('change', function() {
            var file = this.files[0];
            if (file) {
                $('#favicon-preview').attr('src', URL.createObjectURL(file));
                $(this).next('.custom-file-label').text(file.name);
            }
        });

        // Banner preview on add
        $('#banners-input').on('change', function() {
            if (this.files.length) {
                $('#settings-form').submit();
            }
        });

        // Remove banner via AJAX
        $(document).on('click', '.remove-banner', function() {
            var btn = $(this);
            var mediaId = btn.data('media-id');

            Swal.fire({
                title: 'Remove Banner?',
                text: 'This cannot be undone.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('setting.delete-media') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            media_id: mediaId
                        },
                        success: function() {
                            btn.closest('.banner-item').fadeOut(300, function() {
                                $(this).remove();
                                if ($('#banner-sortable .banner-item').length === 0) {
                                    location.reload();
                                }
                            });
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to remove banner.', 'error');
                        }
                    });
                }
            });
        });

        // Confirm save
        $('#save-settings-btn').on('click', function(e) {
            e.preventDefault();
            var form = $('#settings-form');
            Swal.fire({
                title: 'Save Settings?',
                text: 'Are you sure you want to update the settings?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    form.submit();
                }
            });
        });

        // Drag-drop reorder for banners
        var sortableEl = document.getElementById('banner-sortable');
        if (sortableEl) {
            new Sortable(sortableEl, {
                animation: 150,
                handle: '.position-relative',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function() {
                    var ids = [];
                    $('#banner-sortable .banner-item').each(function() {
                        ids.push($(this).data('media-id'));
                    });
                    $.ajax({
                        url: '{{ route('setting.reorder-banners') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to save order.', 'error');
                        }
                    });
                }
            });
        }
    });
</script>

@if (session('success'))
    <script>
        $(function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                type: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
@endif

@if (session('error'))
    <script>
        $(function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                type: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 5000
            });
        });
    </script>
@endif
@stop
