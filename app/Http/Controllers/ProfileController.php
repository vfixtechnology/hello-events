<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Hash;

class ProfileController extends Controller
{
     //user profile
     public function profile()
     {
         return view('backend.profile.index');
     }


    public function profileUpdate(Request $request)
    {
        $data = $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'nullable|digits:10',
                'image' => 'nullable|image|mimes:png,jpg,jpeg,webp',
                'remove_image' => 'nullable|boolean', // Add validation for the checkbox
            ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        // Check for a new image upload first
        if ($request->hasFile('image')) {
            $user->clearMediaCollection('image');
            $user->addMediaFromRequest('image')
            ->toMediaCollection('image');
        }
        // If no new image, check if the existing one should be removed
        elseif ($request->has('remove_image')) {
            $user->clearMediaCollection('image');
        }

        return redirect()->route('profile')->with('success', 'Profile Updated Successfully!');
    }

    public function passwordUpdate(Request $request)
     {
         $request->validate([
             'current_password' => 'required',
             'password' => 'required|string|min:8|confirmed',
             'password_confirmation' => 'required',
           ]);

           $user = Auth::user();

           if (!Hash::check($request->current_password, $user->password)) {
               return back()->with('password_error', 'Current password does not match!');
           }

           $user->password = Hash::make($request->password);
           $user->save();

           return redirect()->route('profile')->withSuccess('Password is successfully Updated!');
     }

}
