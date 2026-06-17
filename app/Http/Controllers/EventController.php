<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:event list')->only(['index', 'trashView']);
        $this->middleware('can:event create')->only(['create', 'store']);
        $this->middleware('can:event edit')->only(['edit', 'update']);
        $this->middleware('can:event delete')->only(['destroy', 'bulkDelete']);
        $this->middleware('can:event restore')->only(['restore']);
        $this->middleware('can:event force-delete')->only(['force_delete']);
        $this->middleware('can:event trash-bulk-delete')->only(['trashBulkDelete']);
    }

    public function index()
    {
        $events = Event::latest()->paginate(10);

        return view('backend.event.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::pluck('title', 'id');
        $tax_rates = TaxRate::where('is_active', true)->pluck('title', 'id');

        return view('backend.event.create', compact('categories', 'tax_rates'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'body' => 'required|string',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'categories' => 'required|array',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',

            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.id' => 'nullable|integer|exists:ticket_types,id',
            'ticket_types.*.title' => 'required|string|max:255',
            'ticket_types.*.body' => 'nullable|string',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.compare_at_price' => 'nullable|numeric|min:0',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.min_quantity' => 'required|integer|min:1',
            'ticket_types.*.max_entries' => 'required|integer|min:1',

            'add_ons' => 'nullable|array',
            'add_ons.*.id' => 'nullable|integer|exists:add_ons,id',
            'add_ons.*.title' => 'required_with:add_ons|string|max:255',
            'add_ons.*.body' => 'nullable|string',
            'add_ons.*.price' => 'required_with:add_ons|numeric|min:0',
            'add_ons.*.compare_at_price' => 'nullable|numeric|min:0',
            'map_link' => 'nullable|url|max:500',
            'host_name' => 'nullable|string|max:255',
            'host_email' => 'nullable|email|max:255',
            'host_phone' => 'nullable|string|max:50',
            'host_website' => 'nullable|url|max:500',
            'host_facebook' => 'nullable|url|max:500',
            'host_instagram' => 'nullable|url|max:500',
            'host_twitter' => 'nullable|url|max:500',
            'host_linkedin' => 'nullable|url|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'full_address' => 'nullable|string|max:500',
            'video' => 'nullable|url|max:500',
            'timezone' => 'nullable|string|max:255',
        ]);

        // Use unset() to remove the keys that are not in the 'events' table
        unset($validatedData['categories'], $validatedData['ticket_types'], $validatedData['add_ons'], $validatedData['seo_title'], $validatedData['seo_description']);

        $validatedData['published'] = $request->has('published') ?? 0;
        $validatedData['featured'] = $request->has('featured') ?? 0;

        // create event
        $event = Event::create($validatedData);

        // add categories relation data
        $event->categories()->sync($request->categories);

        foreach ($request->ticket_types as $ticketTypeData) {
            unset($ticketTypeData['id']);
            $event->ticketTypes()->create($ticketTypeData);
        }

        // add add ons relation data
        if ($request->has('add_ons')) {
            foreach ($request->add_ons as $addOnData) {
                unset($addOnData['id']);
                $event->addOns()->create($addOnData);
            }
        }

        // handle image
        if ($request->hasFile('image')) {
            $event->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        // handle organizer image
        if ($request->hasFile('organizer_image')) {
            $event->addMediaFromRequest('organizer_image')
                ->toMediaCollection('organizer_image');
        }

        // handle seo
        $event->seo->update([
            'title' => $request->seo_title,
            'description' => $request->seo_description,
            'image' => $event->getFirstMediaUrl('image'),
        ]);

        return redirect()->route('event.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $event->load('categories', 'ticketTypes', 'addOns', 'seo');
        $event->addOns->loadCount('tickets');
        $event->ticketTypes->loadCount(['tickets' => fn($q) => $q->whereHas('order', fn($oq) => $oq->whereIn('status', ['pending', 'completed']))]);
        $categories = Category::pluck('title', 'id');
        $tax_rates = TaxRate::where('is_active', true)->pluck('title', 'id');

        return view('backend.event.edit', compact('event', 'categories', 'tax_rates'));
    }

    public function update(Request $request, Event $event)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', Rule::unique('events')->ignore($event->id)],
            'body' => 'required|string',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'categories' => 'required|array',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',

            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.id' => 'nullable|integer|exists:ticket_types,id',
            'ticket_types.*.title' => 'required|string|max:255',
            'ticket_types.*.body' => 'nullable|string',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.compare_at_price' => 'nullable|numeric|min:0',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.min_quantity' => 'required|integer|min:1',
            'ticket_types.*.max_entries' => 'required|integer|min:1',

            'add_ons' => 'nullable|array',
            'add_ons.*.id' => 'nullable|integer|exists:add_ons,id',
            'add_ons.*.title' => 'required_with:add_ons|string|max:255',
            'add_ons.*.body' => 'nullable|string',
            'add_ons.*.price' => 'required_with:add_ons|numeric|min:0',
            'add_ons.*.compare_at_price' => 'nullable|numeric|min:0',
            'map_link' => 'nullable|url|max:500',
            'host_name' => 'nullable|string|max:255',
            'host_email' => 'nullable|email|max:255',
            'host_phone' => 'nullable|string|max:50',
            'host_website' => 'nullable|url|max:500',
            'host_facebook' => 'nullable|url|max:500',
            'host_instagram' => 'nullable|url|max:500',
            'host_twitter' => 'nullable|url|max:500',
            'host_linkedin' => 'nullable|url|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'full_address' => 'nullable|string|max:500',
            'video' => 'nullable|url|max:500',
            'timezone' => 'nullable|string|max:255',
        ]);

        unset($validatedData['categories'], $validatedData['ticket_types'], $validatedData['add_ons'], $validatedData['seo_title'], $validatedData['seo_description']);

        $validatedData['published'] = $request->has('published') ?? 0;
        $validatedData['featured'] = $request->has('featured') ?? 0;

        $event->update($validatedData);

        $event->categories()->sync($request->categories);

        $requestTicketTypeIds = collect($request->ticket_types)->pluck('id')->filter()->values()->toArray();

        $protectedTicketTypes = !empty($requestTicketTypeIds)
            ? $event->ticketTypes()->whereNotIn('id', $requestTicketTypeIds)->whereHas('tickets', fn($q) => $q->whereHas('order', fn($oq) => $oq->whereIn('status', ['pending', 'completed'])))->pluck('title')->toArray()
            : $event->ticketTypes()->whereHas('tickets', fn($q) => $q->whereHas('order', fn($oq) => $oq->whereIn('status', ['pending', 'completed'])))->pluck('title')->toArray();

        if (!empty($requestTicketTypeIds)) {
            $event->ticketTypes()->whereNotIn('id', $requestTicketTypeIds)
                ->whereDoesntHave('tickets', fn($q) => $q->whereHas('order', fn($oq) => $oq->whereIn('status', ['pending', 'completed'])))
                ->delete();
        } else {
            $event->ticketTypes()
                ->whereDoesntHave('tickets', fn($q) => $q->whereHas('order', fn($oq) => $oq->whereIn('status', ['pending', 'completed'])))
                ->delete();
        }
        foreach ($request->ticket_types as $ticketTypeData) {
            $ticketTypeId = $ticketTypeData['id'] ?? null;
            if ($ticketTypeId && $ticketType = $event->ticketTypes()->find($ticketTypeId)) {
                $ticketType->update($ticketTypeData);
            } else {
                unset($ticketTypeData['id']);
                $event->ticketTypes()->create($ticketTypeData);
            }
        }

        $requestAddOnIds = collect($request->add_ons)->pluck('id')->filter()->values()->toArray();

        $protectedAddOns = !empty($requestAddOnIds)
            ? $event->addOns()->whereNotIn('id', $requestAddOnIds)->whereHas('tickets')->pluck('title')->toArray()
            : $event->addOns()->whereHas('tickets')->pluck('title')->toArray();

        if (!empty($requestAddOnIds)) {
            $event->addOns()->whereNotIn('id', $requestAddOnIds)->whereDoesntHave('tickets')->delete();
        } else {
            $event->addOns()->whereDoesntHave('tickets')->delete();
        }
        $warnings = [];
        if (!empty($protectedTicketTypes)) {
            $warnings[] = 'Ticket type(s) "' . implode(', ', $protectedTicketTypes) . '" could not be deleted — they have active bookings.';
        }
        if (!empty($protectedAddOns)) {
            $warnings[] = 'Add-on(s) "' . implode(', ', $protectedAddOns) . '" could not be deleted — they have been purchased.';
        }
        if (!empty($warnings)) {
            session()->flash('warning', implode(' ', $warnings));
        }
        if ($request->has('add_ons')) {
            foreach ($request->add_ons as $addOnData) {
                $addOnId = $addOnData['id'] ?? null;
                if ($addOnId && $addOn = $event->addOns()->find($addOnId)) {
                    $addOn->update($addOnData);
                } else {
                    unset($addOnData['id']);
                    $event->addOns()->create($addOnData);
                }
            }
        }

        if ($request->hasFile('image')) {
            $event->clearMediaCollection('image');
            $event->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        if ($request->hasFile('organizer_image')) {
            $event->clearMediaCollection('organizer_image');
            $event->addMediaFromRequest('organizer_image')
                ->toMediaCollection('organizer_image');
        }

        $event->seo->update([
            'title' => $request->seo_title,
            'description' => $request->seo_description,
            'image' => $event->getFirstMediaUrl('image', 'webp'),
        ]);

        return redirect()->route('event.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('event.index')->with('success', 'Event moved to trash.');
    }

    public function trashView(Request $request)
    {
        $events = Event::onlyTrashed()->latest()->get();

        return view('backend.event.trash', compact('events'));
    }

    // restore data
    public function restore($id)
    {
        $data = Event::withTrashed()->find($id);
        if (! is_null($data)) {
            $data->restore();
        }

        return redirect()->back()->with('success', 'Item restored succesfully');
    }

    public function force_delete($id)
    {
        // Retrieve the trashed tour with its associated images
        $event = Event::withTrashed()->findOrFail($id);

        // Prevent deletion if any orders exist
        if ($event->ticketTypes()->whereHas('tickets.order')->exists()) {
            return back()->withErrors('Cannot delete event — it has associated orders.');
        }

        // Detach categories
        $event->categories()->detach();

        // Delete related ticket types and add-ons
        $event->ticketTypes()->delete();

        $event->addOns()->delete();
        // Permanently delete the data from the database
        $event->forceDelete();

        return back()->withSuccess('Event & related data deleted permanently!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (! empty($ids)) {
            // Detach relationships first
            $events = Event::whereIn('id', $ids)->get();

            foreach ($events as $event) {
                $event->delete(); // Delete tag
            }

            return back()->withSuccess('Selected items deleted successfully!');
        }

        return back()->WithErrors('Something went wrong');
    }

    public function trashBulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (! empty($ids)) {
            $events = Event::withTrashed()->whereIn('id', $ids)->get();

            foreach ($events as $event) {

                // Prevent deletion if any orders exist
                if ($event->ticketTypes()->whereHas('tickets.order')->exists()) {
                    return back()->withErrors("Cannot delete '{$event->title}' — it has associated orders.");
                }

                // Detach categories
                $event->categories()->detach();

                // Delete related ticket types and add-ons
                $event->ticketTypes()->delete();

                $event->addOns()->delete();
                // Permanently delete the data from the database
                $event->forceDelete();
            }

            return back()->withSuccess('Selected items deleted permanently!');
        }

        return back()->withErrors('Something went wrong');
    }
}
