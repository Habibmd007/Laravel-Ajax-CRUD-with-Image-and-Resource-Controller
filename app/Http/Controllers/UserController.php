<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,[
            'firstname'     => 'required',
            'lastname'      => 'required',
            'email'         => 'required|unique:users',
            'password'      => 'required',
            'image'         => 'required|mimes:png,jpg,jpeg,svg,bmp,ico|max:1024',
        ]);

        $image = $request->file('image');
        $slug = str_slug($request->title);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imageName = $slug.'-'.$currentDate.'-'.uniqid().'-'.$image->getClientOriginalExtension();
            if (!file_exists('thumbnail_images/')) {
                mkdir('thumbnail_images/', 0777, true);
            }
            $image->move('thumbnail_images/', $imageName);
        }else {
            $imageName = 'default.png';
        }
        
   
        
        $imageUrl = 'thumbnail_images/'.$imageName;
        $user= new User();
        $user->name =strtolower(trim($request->firstname.$request->lastname));
        $user->email = strtolower(trim($request->email));
        $user->password = bcrypt($request->password);
        $user->photo = $imageUrl;
        $user->email_verification_token = str_random(32);
        $user->save();

        // mail commented coz using notification
        // Mail::to($user->email)->queue(new Mailverification($user));
         Mail::to($user->email)->send(new Mailverification($user));

        // $user->notify(new MailNotify($user));
        return redirect('user/create')->with('msg', 'Verify your email to  active your account');

    }
}
