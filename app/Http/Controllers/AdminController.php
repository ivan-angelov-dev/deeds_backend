<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Category;
use App\Http\Models\Offer;
use App\Http\Models\Photo;
use App\Http\Models\Setting;
use App\Http\Models\User;
use App\Http\Utils\Utils;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{

    public function showDashboardPage()
    {
        return view('dashboard');
    }

    public function showUserlistPage() {
        $users = User::all();
        return view('user-list')->with('users', $users);
    }

    public function showUserdetailPage() {
        $user_id = request('id');
        $user = User::where('id', $user_id)->first();
        $offers = Offer::where('creator_id', $user_id)->with('category')->get();
        $photos = Photo::where('user_id', $user_id)->get();

        if ($user != null) {
            return view('user-detail')
                ->with([
                    'user' => $user,
                    'offers' => $offers,
                    'photos' => $photos
                ]);
        }
    }

    public function showOfferlistPage() {
        $offers = Offer::with('creator')->get();
        return view('offer-list')->with('offers', $offers);
    }

    public function showOfferdetailPage() {
        $offer_id = request('id');
        $offer = Offer::where('id', $offer_id)->with('category', 'interested_users')->first();
        if ($offer != null) {
            return view('offer-detail')
                ->with([
                    'offer' => $offer
                ]);
        }
    }

    public function showCategoryPage() {
        $categories = Category::all();
        return view('category')->with('categories', $categories);
    }

    public function showCategorydetailPage() {
        $category_id = request('id');
        $category = Category::where('id', $category_id)->with('offers')->first();
        if ($category != null) {
            return view('category-detail')
                ->with([
                    'category' => $category
                ]);
        }
    }

    public function showSettingsPage() {
        $twilio = Setting::all();
        return view('settings')->with('twilio', $twilio);
    }

    public function editProfile() {

        $oldPassword = request('oldPassword');
        $newPassword = request('newPassword');

        $email = session()->get('admin')->email;
        $admin = Admin::where('email', $email)->first();

        if (hash::check($oldPassword, $admin->password)) {

            Admin::where('email',$email)->update(['password' => hash::make($newPassword)]);
            $admin = Admin::where('email', $email)->first();
            session()->put('admin', $admin);

            return Utils::makeResponse();

        } else {
            return Utils::makeResponse([], 'Password is not correct.');
        }

    }
}

