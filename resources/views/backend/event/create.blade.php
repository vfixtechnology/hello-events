@extends('adminlte::page')

@section('title', 'Catepories')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit category</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('category.create') }}">+ Add New</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('event.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb">
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Event Details</h3>
                    </div>
                    <div class="card-body">
                        <x-form.input name="title" placeholder="Event title" label="Event Title" required="true" />
                        <x-form.input label="Slug" class="bg-light" id="slug" name="slug"
                            placeholder="Seo friendly slug" />
                        <x-form.textarea name="body" id="summernote" placeholder="Event details..." label="Description"
                            rows="10" required="true" />
                    </div>
                </div>

                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">Other Details</h3>
                    </div>
                    <div class="card-body">
                        <x-form.input name="venue" placeholder="Event Venue" helpText="" label="Venue" required="true" />
                        <x-form.input name="location" placeholder="Event location" label="Location" required="true" />
                        <x-form.input name="video" placeholder="https://www.youtube.com/video_link"
                            label="Video link (optional)" />
                        @php
                            $timezoneOptions = [];
                            $identifiers = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

                            foreach ($identifiers as $identifier) {
                                $timezone = new DateTimeZone($identifier);
                                $datetime = new DateTime('now', $timezone);
                                $offsetInSeconds = $timezone->getOffset($datetime);

                                $hours = floor($offsetInSeconds / 3600);
                                $minutes = abs(($offsetInSeconds % 3600) / 60);
                                $formattedOffset = sprintf('%+03d:%02d', $hours, $minutes);

                                $displayText = "(UTC {$formattedOffset}) {$identifier}";

                                $timezoneOptions[$identifier] = $displayText;
                            }
                        @endphp

                         <x-form.select label="Choose Time Zone" name="timezone" :options="$timezoneOptions" :selected="config('app.timezone')">
                             Timezone
                          </x-form.select>

                         <x-form.input name="map_link" placeholder="https://maps.app.goo.gl/..." label="Map Link (Google Maps URL)" />
                         <small class="form-text text-muted">
                             <a href="https://maps.google.com" target="_blank">Open Google Maps</a> → Search location → Share → Copy link
                         </small>
                    </div>
                </div>

                {{-- ORGANIZER DETAILS CARD --}}
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Organizer Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_name" placeholder="Organizer/Host Name" label="Organizer Name" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_email" placeholder="organizer@email.com" label="Organizer Email" type="email" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_phone" placeholder="+91 98765 43210" label="Organizer Phone" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_website" placeholder="https://website.com" label="Website" />
                            </div>
                        </div>
                        <h5 class="mt-3">Social Media Links</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_facebook" placeholder="https://facebook.com/..." label="Facebook" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_instagram" placeholder="https://instagram.com/..." label="Instagram" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_twitter" placeholder="https://twitter.com/..." label="Twitter/X" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_linkedin" placeholder="https://linkedin.com/..." label="LinkedIn" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TICKET TYPES CARD --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ticket Types</h3>
                        <div class="card-tools"><button type="button" id="add-ticket-type"
                                class="btn btn-sm btn-success">Add Ticket Type</button></div>
                    </div>
                    <div class="card-body">
                        <div id="ticket-types-wrapper">
                            {{-- Initial row will be added by jQuery --}}
                        </div>
                    </div>
                </div>

                {{-- ADD-ONS CARD --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add-Ons (Optional)</h3>
                        <div class="card-tools"><button type="button" id="add-add-on" class="btn btn-sm btn-success">Add
                                Add-On</button></div>
                    </div>
                    <div class="card-body">
                        <div id="add-ons-wrapper">
                            {{-- Rows are added dynamically --}}
                        </div>
                    </div>
                </div>

                {{-- seo details --}}
                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">SEO Details</h3>
                    </div>
                    <div class="card-body">
                        <x-form.input name="seo_title" placeholder="Seo title" label="Seo Title" />
                        <x-form.textarea name="seo_description" placeholder="Seo description...." label="Seo description"
                            rows="4" />
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="col-md-4 mb-4">
                <div class="sticky-top">
                    <div class="card sticky-bottom">
                        <div class="card-header">
                            <h3 class="card-title">Publishing</h3>
                        </div>
                        <div class="card-body">
                            <x-form.input name="start_datetime" label="Start Date & Time" type="datetime-local"
                                required="true" />
                            <x-form.input name="end_datetime" label="End Date & Time" type="datetime-local"
                                required="true" />
                            <x-form.checkbox name="published" label="Published"
                                helpText="Only published events will be shown on main website" checked="true" />
                        </div>
                        <div class="card-footer">
                            <x-button label="Save Event" />
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories & Tax</h3>
                        </div>
                        <div class="card-body">
                            <x-form.select name="categories[]" label="Categories" :options="$categories" required="true"
                                />
                            <x-form.select name="tax_rate_id" label="Tax Rate (Optional)" :options="$tax_rates" />
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Featured Image</h3>
                        </div>
                        <div class="card-body">
                            <small class="text-danger">Recommended size: width 1280px height 720px</small>
                            <x-form.input type="file" name="image" id="imgInp" label="Image" />

                            <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                src="{{ asset('no-image.webp') }}" alt="your image">
                        </div>
                    </div>

                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Organizer Image</h3>
                        </div>
                        <div class="card-body pb-4">
                            <small class="text-muted">Square image recommended (500x500px)</small>
                            <x-form.input type="file" name="organizer_image" id="orgImgInp" label="Organizer Image" />
                            <img style="width: 100px; height: 100px; margin-top:10px; border:1px solid #ddd; border-radius:50%; object-fit:cover;"
                                id="orgBlah" src="{{ asset('no-image.webp') }}" alt="organizer preview">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Hidden Templates for jQuery --}}
    <template id="ticket-type-template">
        <div class="item-row card card-body mb-3 ticket-type-row">
            <div class="row">
                <div class="col-md-4"><x-form.input name="ticket_types[__INDEX__][title]" label="Ticket Title"
                        placeholder="e.g., General Admission" required="true" /></div>
                <div class="col-md-3"><x-form.input name="ticket_types[__INDEX__][quantity]" label="Total Tickets"
                        type="number" placeholder="Blank for unlimited" required="true" /></div>
                <div class="col-md-2"><x-form.input name="ticket_types[__INDEX__][min_quantity]" label="Min Purchase"
                        type="number" value="1" required="true" /></div>
                <div class="col-md-3"><x-form.input name="ticket_types[__INDEX__][max_entries]" label="Max Entries"
                        type="number" value="1" required="true" helpText="How many times this ticket can be used" /></div>
            </div>
            <div class="row">
                <div class="col-md-6"><x-form.input name="ticket_types[__INDEX__][price]" label="Price"
                        type="number" step="0.01" placeholder="1200" required="true" /></div>
                <div class="col-md-6"><x-form.input name="ticket_types[__INDEX__][compare_at_price]"
                        label="Compare Price (Optional)" type="number" placeholder="High Price to show"
                        step="0.01" /></div>
            </div>
            <div class="row justify-content-end">
                <div class="col-md-12"><x-form.textarea name="ticket_types[__INDEX__][body]"
                        label="Description (Optional)" rows="3" placeholder="Details about ticket..." /></div>
                <div class="col-md-2 d-flex align-items-end"><button type="button"
                        class="btn btn-danger btn-block remove-item ">Remove</button></div>
            </div>
        </div>
    </template>

    <template id="add-on-template">
        <div class="item-row card card-body mb-3">
            <div class="row">
                <div class="col-md-5"><x-form.input name="add_ons[__INDEX__][title]" label="Add-On Title"
                        placeholder="e.g., Lunch" /></div>
                <div class="col-md-3"><x-form.input name="add_ons[__INDEX__][price]" label="Price" type="number"
                        step="0.01" placeholder="1200" /></div>
                <div class="col-md-4"><x-form.input name="add_ons[__INDEX__][compare_at_price]"
                        placeholder="High Price to show" label="Compare Price (Optional)" type="number"
                        step="0.01" /></div>

            </div>
            <div class="row justify-content-end">
                <div class="col-md-12"><x-form.textarea name="add_ons[__INDEX__][body]" label="Description (Optional)"
                        rows="3" placeholder="Details of add on.." /></div>

                <div class="col-md-2 d-flex align-items-end"><button type="button"
                        class="btn btn-danger btn-block remove-item">Remove</button></div>
            </div>
        </div>
    </template>

@endsection



@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <style>
        /* summer note */
        .modal-header .close,
        .modal-header .mailbox-attachment-close {
            padding: 0rem;
            margin: 0 auto;
        }

        .modal-header {
            display: -ms-flexbox;
            display: block;
            -ms-flex-align: start;
            align-items: flex-start;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
    </style>
@stop

@section('js')
    {{-- create live slug --}}
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();

            // Step 1: Remove the specific characters (), [], {} completely.
            Text = Text.replace(/[\[\]\(\)\{\}]/g, '');

            // Step 2: Replace spaces and any other non-alphanumeric characters with a single hyphen.
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');

            // Step 3 (Optional but recommended): Remove any leading or trailing hyphens.
            Text = Text.replace(/^-+|-+$/g, '');

            $('#slug').val(Text);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('.alert-form').on('submit', function(e) {
                e.preventDefault(); // prevent default form submit
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Not permanent — this post goes to Trash!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        form.submit(); // use native form submission
                    }
                });
            });
        });
    </script>

    @if (session('errors'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error', // SweetAlert2 v8 uses "type" instead of "icon"
                    title: '<strong>Whoops!</strong> There were some problems with your input.',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'success', // for SweetAlert2 v8
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            let ticketTypeIndex = 0;
            let addOnIndex = 0;

            function addTicketTypeRow() {
                let template = $('template#ticket-type-template').html().replace(/__INDEX__/g, ticketTypeIndex);
                $('#ticket-types-wrapper').append(template);
                ticketTypeIndex++;
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                // Count how many ticket type rows exist
                const ticketRows = $('.ticket-type-row');

                // If only one row exists, disable/hide its remove button
                if (ticketRows.length === 1) {
                    ticketRows.find('.remove-item').prop('disabled', true).addClass('disabled').hide();
                } else {
                    // If multiple rows exist, enable/show all remove buttons
                    ticketRows.find('.remove-item').prop('disabled', false).removeClass('disabled').show();
                }
            }

            // Add the first ticket type row automatically on page load
            addTicketTypeRow();

            $('#add-ticket-type').on('click', function() {
                addTicketTypeRow();
            });

            $('#add-add-on').on('click', function() {
                let template = $('template#add-on-template').html().replace(/__INDEX__/g, addOnIndex);
                $('#add-ons-wrapper').append(template);
                addOnIndex++;
            });

            // delete row without alert

            // $(document).on('click', '.remove-item', function() {
            //     $(this).closest('.item-row').remove();
            //     updateRemoveButtons();
            // });


            // delete new row for ticket type or add ons with sweeet alert
            $(document).on('click', '.remove-item', function(e) {
                e.preventDefault();
                const button = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to remove this item?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    // This check works for both new and old versions of SweetAlert
                    if (result.isConfirmed || result.value) {
                        // If confirmed, then remove the row
                        button.closest('.item-row').remove();
                        updateRemoveButtons();
                    }
                });
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    {{-- summer note --}}
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 400,
                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    },
                    onMediaDelete: function(target) {
                        deleteImage(target[0]);
                    }
                }
            });

            function uploadImage(file) {
                let formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: '{{ route('summer.upload.image') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Create image with data-media-id attribute
                        let $img = $('<img>')
                            .attr('src', response.url)
                            .attr('data-media-id', response.id)
                            .css('max-width', '100%');

                        $('#summernote').summernote('insertNode', $img[0]);
                    },
                    error: function(error) {
                        console.error('Upload error:', error);
                    }
                });
            }

            function deleteImage(targetElement) {
                console.log('Target element:', targetElement);

                let mediaId = $(targetElement).data('media-id');
                let imageSrc = targetElement.src;

                console.log('Media ID:', mediaId);
                console.log('Image source:', imageSrc);

                // If no media ID, try to extract from URL or use the full URL
                let deleteData = {
                    imageSrc: mediaId || imageSrc
                };

                $.ajax({
                    url: '{{ route('summer.delete.image') }}',
                    type: 'POST',
                    data: deleteData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Delete success:', response.message);
                        $(targetElement).remove();
                    },
                    error: function(error) {
                        console.error('Delete error:', error);
                        // Remove the element anyway to maintain UX
                        $(targetElement).remove();
                    }
                });
            }
        });
    </script>

    {{-- view image while uploading --}}
    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }

        const orgImgInp = document.getElementById('orgImgInp');
        const orgBlah = document.getElementById('orgBlah');
        if (orgImgInp) {
            orgImgInp.onchange = evt => {
                const [file] = orgImgInp.files
                if (file) {
                    orgBlah.src = URL.createObjectURL(file)
                }
            }
        }
    </script>



@endsection


