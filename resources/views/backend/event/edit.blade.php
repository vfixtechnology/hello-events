@extends('adminlte::page')

@section('title', 'Edit Event')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Event: {{ $event->title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('event.create') }}">+ Add New</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-dismissable alert-danger mt-3">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Whoops!</strong> There were some problems with your input.<br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form class="alert-form" action="{{ route('event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row">
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Event Details</h3>
                    </div>
                    <div class="card-body">
                        <x-form.input name="title" placeholder="Event title" label="Event Title" :value="$event->title"
                            required="true" />
                        <x-form.input label="Slug" class="bg-light" id="slug" name="slug"
                            value="{{ $event->slug }}" placeholder="Seo friendly slug" />
                        <x-form.textarea name="body" id="summernote" placeholder="Event details..."
                            value="{!! $event->body !!}" label="Description" rows="10"
                            required="true">{!! $event->body !!}</x-form.textarea>
                    </div>
                </div>

                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">Other Details</h3>
                    </div>
                    <div class="card-body">
                        <x-form.input name="venue" placeholder="Event Venue" label="Venue" :value="$event->venue"
                            required="true" />
                        <x-form.input name="location" placeholder="Event location" label="Location" :value="$event->location"
                            required="true" />
                        <x-form.input name="video" placeholder="https://www.youtube.com/video_link"
                            label="Video link (optional)" :value="$event->video" />
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

                          <x-form.select label="Choose Time Zone" name="timezone" :options="$timezoneOptions" :selected="$event->timezone ?? config('app.timezone')" />

                         <x-form.input name="map_link" placeholder="https://maps.app.goo.gl/..." label="Map Link (Google Maps URL)" :value="$event->map_link ?? ''" />
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
                                <x-form.input name="host_name" placeholder="Organizer/Host Name" label="Organizer Name" :value="$event->host_name ?? ''" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_email" placeholder="organizer@email.com" label="Organizer Email" :value="$event->host_email ?? ''" type="email" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_phone" placeholder="+91 98765 43210" label="Organizer Phone" :value="$event->host_phone ?? ''" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_website" placeholder="https://website.com" label="Website" :value="$event->host_website ?? ''" />
                            </div>
                        </div>
                        <h5 class="mt-3">Social Media Links</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_facebook" placeholder="https://facebook.com/..." label="Facebook" :value="$event->host_facebook ?? ''" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_instagram" placeholder="https://instagram.com/..." label="Instagram" :value="$event->host_instagram ?? ''" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="host_twitter" placeholder="https://twitter.com/..." label="Twitter/X" :value="$event->host_twitter ?? ''" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="host_linkedin" placeholder="https://linkedin.com/..." label="LinkedIn" :value="$event->host_linkedin ?? ''" />
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
                            @forelse($event->ticketTypes as $index => $ticketType)
                                <div class="item-row card card-body mb-3 ticket-type-row" data-protected="{{ $ticketType->tickets_count > 0 ? 'true' : 'false' }}">
                                    <input type="hidden" name="ticket_types[{{ $index }}][id]" value="{{ $ticketType->id }}">
                                    <div class="row">
                                        <div class="col-md-4"><x-form.input name="ticket_types[{{ $index }}][title]"
                                                label="Ticket Title" :value="$ticketType->title" required="true" /></div>
                                        <div class="col-md-3"><x-form.input
                                                name="ticket_types[{{ $index }}][quantity]" label="Total Tickets"
                                                :value="$ticketType->quantity" type="number" required="true" /></div>
                                        <div class="col-md-2"><x-form.input
                                                name="ticket_types[{{ $index }}][min_quantity]" label="Min Purchase"
                                                :value="$ticketType->min_quantity" type="number" required="true" />
                                        </div>
                                        <div class="col-md-3"><x-form.input
                                                name="ticket_types[{{ $index }}][max_entries]" label="Max Entries"
                                                :value="$ticketType->max_entries ?? 1" type="number" required="true"
                                                helpText="How many times this ticket can be used" />
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6"><x-form.input
                                                name="ticket_types[{{ $index }}][compare_at_price]"
                                                label="Compare Price (Optional)" :value="$ticketType->compare_at_price" type="number"
                                                step="0.01" /></div>
                                                 <div class="col-md-6"><x-form.input name="ticket_types[{{ $index }}][price]"
                                                label="Price" :value="$ticketType->price" type="number" step="0.01"
                                                required="true" /></div>
                                    </div>
                                    <div class="row justify-content-end">
                                        <div class="col-md-12"><x-form.textarea
                                                name="ticket_types[{{ $index }}][body]"
                                                value="{{ $ticketType->body }}" label="Description (Optional)"
                                                rows="3">{{ $ticketType->body }}</x-form.textarea></div>
                                        <div class="col-md-2 d-flex align-items-end"><button type="button"
                                                class="alert-form btn btn-danger btn-block remove-item">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                {{-- If no tickets exist, the JS will add the first row --}}
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ADD-ONS CARD --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add-Ons (Optional)</h3>
                        <div class="card-tools"><button type="button" id="add-add-on"
                                class="btn btn-sm btn-success">Add
                                Add-On</button></div>
                    </div>
                    <div class="card-body">
                        <div id="add-ons-wrapper">
                            @foreach ($event->addOns as $index => $addOn)
                                <div class="item-row card card-body mb-3" data-protected="{{ $addOn->tickets_count > 0 ? 'true' : 'false' }}">
                                    <input type="hidden" name="add_ons[{{ $index }}][id]" value="{{ $addOn->id }}">
                                    <div class="row">
                                        <div class="col-md-5"><x-form.input name="add_ons[{{ $index }}][title]"
                                                label="Add-On Title" :value="$addOn->title" /></div>
                                        <div class="col-md-3"><x-form.input name="add_ons[{{ $index }}][price]"
                                                label="Price" type="number" step="0.01" :value="$addOn->price" />
                                        </div>
                                        <div class="col-md-4"><x-form.input
                                                name="add_ons[{{ $index }}][compare_at_price]"
                                                label="Compare Price (Optional)" type="number" step="0.01"
                                                :value="$addOn->compare_at_price" /></div>
                                    </div>
                                    <div class="row justify-content-end">
                                        <div class="col-md-12"><x-form.textarea name="add_ons[{{ $index }}][body]"
                                                label="Description (Optional)" value="{{ $addOn->body }}"
                                                rows="3">{{ $addOn->body }}</x-form.textarea></div>
                                        <div class="col-md-2 d-flex align-items-end"><button type="button"
                                                class="btn btn-danger btn-block remove-item">Remove</button></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- seo details --}}
                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title">SEO Details</h3>
                    </div>
                    <div class="card-body">
                        <x-form.input name="seo_title" placeholder="Seo title" label="Seo Title"
                            value="{{ $event->seo->title }}" />

                        <x-form.textarea name="seo_description" value="{{ $event->seo->description }}"
                            placeholder="Seo description...." label="Seo description"
                            rows="4">{{ $event->seo->description }}</x-form.textarea>

                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="col-md-4">
                <div class="sticky-top">
                    <div class="card sticky-bottom">
                        <div class="card-header">
                            <h3 class="card-title">Publishing</h3>
                        </div>
                        <div class="card-body">
                            <x-form.input name="start_datetime" label="Start Date & Time" type="datetime-local"
                                :value="$event->start_datetime->format('Y-m-d\TH:i')" required="true" />
                            <x-form.input name="end_datetime" label="End Date & Time" type="datetime-local"
                                :value="$event->end_datetime->format('Y-m-d\TH:i')" required="true" />
                            <x-form.checkbox name="published" label="Published"
                                helpText="Only published events will be shown on main website" :checked="$event->published" />
                        </div>
                        <div class="card-footer">
                            <x-button variant="danger" label="Update" />
                              <a href="{{ route('event.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories & Tax</h3>
                        </div>
                        <div class="card-body">
                            <x-form.select name="categories[]" label="Categories" :options="$categories" :selected="$event->categories->first()->id ?? null"
                                required="true" />
                            <x-form.select name="tax_rate_id" label="Tax Rate (Optional)" :options="$tax_rates"
                                :selected="$event->tax_rate_id" />
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Featured Image</h3>
                        </div>
                        <div class="card-body">
                            <small class="text-danger">Recommended size: 1280x720px</small>
                            <x-form.input type="file" name="image" id="imgInp"
                                label="Image" />
                            <img style="width: 175px; margin-top:10px; border:1px solid black;" id="blah"
                                src="{{ $event->getFirstMediaUrl('image') ?: asset('no-image.webp') }}" alt="your image">
                        </div>
                    </div>

                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Organizer Image</h3>
                        </div>
                        <div class="card-body pb-2">
                            <small class="text-muted">Square image recommended (500x500px)</small>
                            <x-form.input type="file" name="organizer_image" id="orgImgInp"
                                label="Organizer Image" />
                            <img style="width: 100px; height: 100px; margin-top:10px; border:1px solid #ddd; border-radius:50%; object-fit:cover;"
                                id="orgBlah"
                                src="{{ $event->getFirstMediaUrl('organizer_image', 'thumb') ?: asset('no-image.webp') }}"
                                alt="organizer preview">
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

                <div class="col-md-6"><x-form.input name="ticket_types[__INDEX__][compare_at_price]"
                        label="Compare Price (Optional)" type="number" placeholder="High Price to show"
                        step="0.01" /></div>
                         <div class="col-md-6"><x-form.input name="ticket_types[__INDEX__][price]" label="Price"
                        type="number" step="0.01" placeholder="1200" required="true" /></div>
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

    {{-- Summernote JS --}}
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('#summernote').summernote({
                height: 400,
                // ... your other callbacks for image upload/delete
            });

            // Initialize Image Preview
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

            // --- DYNAMIC ROWS SCRIPT ---
            let ticketTypeIndex = {{ $event->ticketTypes->count() }};
            let addOnIndex = {{ $event->addOns->count() }};

            function addTicketTypeRow() {
                let template = $('template#ticket-type-template').html().replace(/__INDEX__/g, ticketTypeIndex);
                $('#ticket-types-wrapper').append(template);
                ticketTypeIndex++;
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                const ticketRows = $('.ticket-type-row');
                if (ticketRows.length <= 1) {
                    ticketRows.find('.remove-item').hide();
                } else {
                    ticketRows.find('.remove-item').show();
                }
            }

            // Add a ticket row on page load ONLY if there are none saved
            if (ticketTypeIndex === 0) {
                addTicketTypeRow();
            } else {
                updateRemoveButtons();
            }

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
                const row = button.closest('.item-row');
                const isProtected = row.attr('data-protected') === 'true';

                if (isProtected) {
                    Swal.fire({
                        title: 'Cannot Remove',
                        text: "This item has been purchased and cannot be deleted.",
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Got it'
                    });
                    return;
                }

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


    <script>
        $(document).ready(function() {
            $('.alert-form').on('submit', function(e) {
                e.preventDefault(); // prevent default form submit
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Item details will be updated. You can edit later!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!',
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
@endsection
