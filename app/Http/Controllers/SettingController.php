<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:setting update');
    }

    public function index()
    {
        $setting = Setting::first() ?? new Setting();
        return view('backend.setting.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'bname' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'email2' => 'nullable|email|max:255',
            'gtag' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:1024',
            'banners' => 'nullable|array',
            'banners.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $setting = Setting::first() ?? new Setting();

        $setting->fill($request->only([
            'bname', 'email', 'email2', 'gtag', 'phone', 'whatsapp',
            'address', 'facebook', 'instagram', 'twitter', 'linkedin', 'youtube',
        ]));

        $setting->save();

        if ($request->hasFile('logo')) {
            $setting->clearMediaCollection('logo');
            $setting->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        if ($request->hasFile('favicon')) {
            $setting->clearMediaCollection('favicon');
            $setting->addMediaFromRequest('favicon')->toMediaCollection('favicon');
        }

        if ($request->hasFile('banners')) {
            foreach ($request->file('banners') as $banner) {
                $setting->addMedia($banner)->toMediaCollection('banners');
            }
        }

        return redirect()->route('setting')->with('success', 'Settings updated successfully.');
    }

    public function deleteMedia(Request $request)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id',
        ]);

        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($request->media_id);
        $media->delete();

        return response()->json(['success' => true]);
    }

    public function reorderBanners(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:media,id',
        ]);

        foreach ($request->ids as $order => $id) {
            \Spatie\MediaLibrary\MediaCollections\Models\Media::where('id', $id)->update(['order_column' => $order + 1]);
        }

        return response()->json(['success' => true]);
    }
}
