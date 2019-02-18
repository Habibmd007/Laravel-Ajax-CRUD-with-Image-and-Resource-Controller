<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paginate(){
    	$contacts = Client::latest()->paginate(10);
    	$i = 1;
    	return view("response", compact("contacts", "i"));
    }





    public function getdata(){
    	$contacts = Client::latest()->paginate(10);
    	$i = 1;
    	return view("response", compact("contacts", "i"));
    }

    public function errordata($error)
    {
        return view('errorData',['error' => $error]);
    }







    public function index()
    {
        $contacts = Client::latest()->paginate(10);
        return view('laracrud',compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //form showing by modal, without controller or ajax
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,svg,bmp,ico|max:1024',
        ]);

        if ($request->email) {
            $img = $request->file('image');
            
            // eta file er original name (extension soho) collect korbe.
            $img_name = $img->getClientOriginalName(); 
            
            // eta just file er name (extension baad diye) collect korbe.
            $base_name = pathinfo($img_name, PATHINFO_FILENAME); 
            
            // shudu extension collect korbe.
            $extension = $img->getClientOriginalExtension(); 
            
            // file name modify korar jonno
            $file_name_to_save = $base_name ."_".time().".".$extension;
            
            // file ta temp location theke laravel-er storage folder-a move korte hobe
            $img->move('images', $file_name_to_save);
            
            $contact = new Client();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->image = 'images/'.$file_name_to_save;
            $contact->save();

            if($contact){
                return response()->json("success");
            }else{
                return response()->json("error");
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = Client::find($id);
    	return view("single-view", compact("contact"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contact = Client::find($id);
    	return view("edit", compact("contact"));
    }

    





    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required',
            'phone' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,svg,bmp,ico|max:1024',
        ]);

        $contact = Client::find($id);
        if (file_exists($contact->image) && $contact->image !=='images/default.jpg') {
            unlink($contact->image);
        }

        $image = $request->file('image');
        $slug = str_slug($request->name);

        if (isset($image)) {
            $imageName = $slug.'-'.uniqid().'-'.'.'.$image->getClientOriginalExtension();
            $image->move('images/', $imageName);
            }else {
            $imageName = 'default.png';
            }
            $imageUrl = 'images/'.$imageName;

        

        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->image = $imageUrl;
        $contact->save();

        if($contact){
            return response()->json("success");
        }else{
            return response()->json("error");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::where('id', $id)->first();
        $client->delete();
        if($client){
            return response()->json("success");
        }else{
            return response()->json("error");
        }
    }
}
