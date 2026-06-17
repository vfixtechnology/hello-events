<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SummernoteImage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Log;

class SummerNoteController extends Controller
{
    public function summerUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $imageRecord = new SummernoteImage();
        $imageRecord->save();

        $mediaItem = $imageRecord->addMediaFromRequest('image')
                                ->toMediaCollection('summernote-images');

        return response()->json([
            'url' => $mediaItem->getFullUrl('webp'),
            'id' => $mediaItem->id
        ]);
    }

    public function summerDelete(Request $request)
    {
        try {
            Log::info('Summernote delete request:', $request->all());

            $request->validate([
                'imageSrc' => 'required'
            ]);

            $imageSrc = $request->input('imageSrc');

            // Since we're sending the media ID directly (15, 16, etc.)
            $mediaId = $imageSrc;

            Log::info('Looking for media with ID:', ['id' => $mediaId]);

            $media = Media::find($mediaId);

            if (!$media) {
                Log::warning('Media not found with ID:', ['id' => $mediaId]);
                return response()->json(['message' => 'Image not found'], 404);
            }

            Log::info('Found media:', [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'model_type' => $media->model_type,
                'model_id' => $media->model_id
            ]);

            // Delete the associated SummernoteImage model first
            $summernoteImage = SummernoteImage::find($media->model_id);

            if ($summernoteImage) {
                Log::info('Deleting SummernoteImage model:', ['id' => $summernoteImage->id]);
                $summernoteImage->delete();
            }

            // Delete the media
            Log::info('Deleting media file');
            $media->delete();

            Log::info('Image deleted successfully');
            return response()->json(['message' => 'Image deleted successfully']);

        } catch (\Exception $e) {
            Log::error('Summernote delete error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Error deleting image: ' . $e->getMessage()], 500);
        }
    }
}
