<?php

namespace App\Http\Controllers;

use App\Http\Models\Category;
use App\Http\Models\InterestedUserRelation;
use App\Http\Models\Offer;
use App\Http\Models\Photo;
use App\Http\Models\Setting;
use App\Http\Models\StarredRelation;
use App\Http\Models\User;
use App\Http\Utils\DBHelper;
use App\Http\Utils\QuickbloxHelper;
use App\Http\Utils\Utils;
use App\Mail\EmailOTPMailable;
use Hamcrest\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;
use Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;

class APIController extends BaseController
{
    public function checkEmail()
    {


        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $email = request('email');

        $user = User::where([
            ['email', $email],
            ['email_verified', 1],
            ['mobile_verified', 1],
            ['signup_passed', 1],
        ])->first();

        if (!isset($user)) {
            return Utils::makeResponse([], config('constants.response-message.not-existing-email'));
        }

        $simple_info = $user->setVisible([
            'id',
            'name',
            'avatar_filename'
        ])->toArray();

        return Utils::makeResponse(['user' => $simple_info]);


    }

    public function checkPassword()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $credentials = request(['email', 'password']);

        $user = User::where([
            ['email', $credentials['email']],
            ['email_verified', 1],
            ['mobile_verified', 1],
            ['signup_passed', 1],
        ])->with('photos', 'instagrams')->first();

        if ($user == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-credentials'));
        }
        if (!Hash::check($credentials['password'], $user->password)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-credentials'));
        }
        if (!$token = auth()->attempt($credentials)) {
            return Utils::makeResponse([], config('constants.response-message.error-generate-api-token'));
        }

        $user = $user->setVisible([
            'id',
            'name',
            'email',
            'country',
            'mobile',
            'birthday',
            'about_me',
            'interested_gender',
            'gender',
            'show_my_distance',
            'age_lower_value',
            'age_upper_value',
            'max_distance',
            'avatar_filename',
            'location_name',
            'latitude',
            'longitude',
            'photos',
            'instagrams',
            'qb_id',
            'qb_password',
        ])->toArray();

        return Utils::makeResponse([
            'api_token' => $token,
            'user' => $user
        ]);

    }

    public function signupStep1()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'country' => 'required',
            'mobile' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $email = request('email');
        $password = request('password');
        $country = request('country');
        $mobile = request('mobile');

        $user = User::where('signup_passed', 1)
            ->where(function ($query) use ($mobile, $country, $email) {
                $query->where('email', $email)
                    ->orWhere(
                        [
                            ['country', '=', $country],
                            ['mobile', '=', $mobile],
                        ]
                    );
            })
            ->first();

        if ($user != null && $user->email_verified == 1 && $user->mobile_verified == 1) {
            return Utils::makeResponse([], config('constants.response-message.user-already-exist'));
        }
        if ($user != null) {
            $user->delete();
        }

        $sql = User::where('signup_passed', '<>', 1)
            ->where(function ($query) use ($mobile, $country, $email) {
                $query->where('email', $email)
                    ->orWhere(
                        [
                            ['country', '=', $country],
                            ['mobile', '=', $mobile],
                        ]
                    );
            })
            ->delete();

        $user = new User();
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->country = $country;
        $user->mobile = $mobile;

        $mobile_otp = Utils::genOTP();
        $email_otp = Utils::genOTP();

        $user->mobile_otp = $mobile_otp;
        $user->email_otp = $email_otp;

        $user->mobile_verified = 0;
        $user->email_verified = 0;

        $user->signup_passed = 0;

        $user->gender = "OTHER";
        $user->show_my_distance = 1;
        $user->age_lower_value = 18;
        $user->age_upper_value = 38;
        $user->max_distance = 20;

        $user->save();

        $to_number = $country . $mobile;

        try {
            $sid = Setting::where('key', config('constants.settings-key.twilio-sid'))->first();
            $token = Setting::where('key', config('constants.settings-key.twilio-token'))->first();
            $number = Setting::where('key', config('constants.settings-key.twilio-number'))->first();

            $sid = $sid == null ? '' : ($sid->value == null ? '' : $sid->value);
            $token = $token == null ? '' : ($token->value == null ? '' : $token->value);
            $number = $number == null ? '' : ($number->value == null ? '' : $number->value);

            $twilio = new Client($sid, $token);

            $msg4verification = "Welcome to Deeds\nPlease use $mobile_otp for mobile otp. Thanks.";

//            $message = $twilio->messages
//                ->create($to_number,
//                    array(
//                        "from" => $number,
//                        "body" => $msg4verification
//                    )
//                );

        } catch (ConfigurationException $e) {

            return Utils::makeResponse([], config('constants.response-message.error-send-mobile-otp'));

        }

        try {

//            Mail::to($email)->send(new EmailOTPMailable(['otp' => $email_otp]));

        } catch (\Exception $e) {
            return Utils::makeResponse([], config('constants.response-message.error-send-email-otp'));
        }

        if (!$token = auth()->attempt(['email' => $email, 'password' => $password])) {
            return Utils::makeResponse([], config('constants.response-message.error-generate-api-token'));
        }

        return Utils::makeResponse([
            'user_id' => $user->id,
            'api_token' => $token
        ], config('constants.response-message.ok'));

    }

    public function signupStep2()
    {
        $validation = Validator::make(request()->all(), [
            'email_otp' => 'required',
            'mobile_otp' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $email_otp = request('email_otp');
        $mobile_otp = request('mobile_otp');

        $me = request()->user;

        if ($me->email_otp != $email_otp) {
            return Utils::makeResponse([], config('constants.response-message.invalid-email-otp'));
        }

        if ($me->mobile_otp != $mobile_otp) {
            return Utils::makeResponse([], config('constants.response-message.invalid-mobile-otp'));
        }

        $me->email_verified = 1;
        $me->mobile_verified = 1;
        $me->save();

        return Utils::makeResponse();

    }

    public function signupStep3()
    {

        $validation = Validator::make(request()->all(), [
            'name' => 'required',
            'birthday' => 'required|date',
            'about_me' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $name = request('name');
        $birthday = request('birthday');
        $about_me = request('about_me');

        $me = request()->user;

        $me->name = $name;
        $me->birthday = $birthday;
        $me->about_me = $about_me;

        $me->save();

        return Utils::makeResponse();


    }

    public function signupStep4()
    {

        $validation = Validator::make(request()->all(), [
            'photo' => 'image'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }


        $request = request();

        $images_dir = public_path('images');
        if (!file_exists($images_dir)) {
            mkdir($images_dir);
        }


        $file_name = 'image_' . time() . '.png';

        $request->file('photo')->move($images_dir, $file_name);

        $me = request()->user;

        $photo = new Photo();

        $photo->user_id = $me->id;
        $photo->image_filename = $file_name;
        $photo->save();

        return Utils::makeResponse(['photo' => $file_name]);


    }

    public function signupFinish()
    {

        $me = request()->user;


        // quickblox

        try {

            $qb = new QuickbloxHelper();

            $session = $qb->createSession();

            $token = $session->token;

            $qb_username = 'deeds_quickblox_username_' . $me->id;
            $qb_password = 'deeds_quickblox_password_' . time();

            $qb_user = $qb->createUser($token, $qb_username, $qb_password, $me->email);

            if (array_key_exists('errors', $qb_user)) {
                $errors = $qb_user->errors;
                return Utils::makeResponse(['errors' => $errors], config('constants.response-message.quickblox-create-user-error'));
            }

            $qb_user = $qb_user->user;

            $qb_id = $qb_user->id;

            $me->signup_passed = 1;
            $me->qb_id = $qb_id;
            $me->qb_password = $qb_password;

            $me->save();

            return Utils::makeResponse([
                'qb_id' => $qb_id,
                'qb_password' => $qb_password,
            ]);

        } catch (\Exception $e) {

            return Utils::makeResponse(
                [
                    'error' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                    ]
                ],
                config('constants.response-message.unknown-error'));
        }


    }

    public function getNearbyOffers()
    {
        $latitude = request('latitude');
        $longitude = request('longitude');
        $skip = request('skip');


        if (!isset($skip)) {
            $skip = 0;
        }

        $me = request()->user;

        if (isset($latitude)) {
            $latitude = floatval($latitude);
            $me->latitude = $latitude;
            $me->save();
        } else {
            if (isset($me->latitude)) {
                $latitude = $me->latitude;
            } else {
                $latitude = 0;
                $me->latitude = 0;
                $me->save();
            }
        }

        if (isset($longitude)) {
            $longitude = floatval($longitude);
            $me->longitude = $longitude;
            $me->save();
        } else {
            if (isset($me->longitude)) {
                $longitude = $me->longitude;
            } else {
                $longitude = 0;
                $me->longitude = 0;
                $me->save();
            }
        }

        Log::info($latitude);
        Log::info($longitude);

        $category_array = Category::all();

        $distance_sql = "(
		111.111 * DEGREES(
			ACOS(
				LEAST(
					COS(RADIANS(t_offer.latitude)) * COS(RADIANS($latitude)) * COS(
						RADIANS(
							t_offer.longitude - $longitude
						)
					) + SIN(RADIANS(t_offer.latitude)) * SIN(RADIANS($latitude)),
					1.0
				)
			)
		)
	)";

        // ******************** drizzle edit ***************

        $query = Offer::select([
            't_offer.*',
            DB::raw("$distance_sql as distance_in_km")
        ])
            ->where([
                ['creator_id', '<>', $me->id],
            ])
//            ->whereRaw(
//                "$distance_sql < $me->max_distance"
//            )
            ->with('creator', 'creator.photos', 'creator.instagrams', 'category');


        $total_count = $query->count();
        $offer_array = $query->offset($skip)->limit(40)->get();

        foreach ($offer_array as &$offer) {
            if (isset($offer->creator)) {
                $offer->creator->setVisible([
                    'id',
                    'name',
                    'avatar_filename',
                    'about_me',
                    'photos',
                    'instagrams',
                    'latitude',
                    'longitude',
                    'show_my_distance',
                    'qb_id'
                ]);
            }
        }

        return Utils::makeResponse([
            'category_array' => $category_array,
            'total_count' => $total_count,
            'offer_array' => $offer_array
        ]);

    }

    public function getOffersByCategory()
    {

        $category_id = request('category_id');

        if (!isset($category_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $me = request()->user;

        $offer_array = Offer::select([
            't_offer.*'
        ])
            ->where([
                ['creator_id', '<>', $me->id],
                ['category_id', $category_id],
            ])
            ->with('creator', 'creator.photos', 'creator.instagrams', 'category')
            ->get();

        foreach ($offer_array as &$offer) {
            if (isset($offer->creator)) {
                $offer->creator->setVisible([
                    'id',
                    'name',
                    'avatar_filename',
                    'about_me',
                    'photos',
                    'instagrams',
                    'latitude',
                    'longitude',
                    'show_my_distance',
                    'qb_id'
                ]);
            }
        }

        return Utils::makeResponse([
            'offer_array' => $offer_array
        ]);

    }

    public function getOfferDetail()
    {
        $offer_id = request('offer_id');

        if (!isset($offer_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $offer = Offer::where('id', $offer_id)->with('creator', 'creator.photos', 'creator.instagrams', 'category')->first();

        if ($offer == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        if (isset($offer->creator)) {
            $offer->creator->setVisible([
                'id',
                'name',
                'avatar_filename',
                'about_me',
                'photos',
                'instagrams',
                'latitude',
                'longitude',
                'show_my_distance',
                'qb_id'
            ]);
        }

        return Utils::makeResponse([
            'offer' => $offer
        ]);

    }

    public function getUserDetail()
    {

        $user_id = request('user_id');

        if (!isset($user_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $user = User::where('id', $user_id)->with('photos', 'instagrams')->first();

        if ($user == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $me = request()->user;

        $starred = StarredRelation::where([
                ['user_id', $me->id],
                ['starred_user_id', $user->id]
            ])->count() > 0;

        $user = $user->setVisible([
            'id',
            'name',
            'about_me',
            'photos',
            'instagrams',
            'latitude',
            'longitude',
            'show_my_distance',
            'qb_id'
        ])->toArray();

        $user['starred'] = $starred ? 1 : 0;

        return Utils::makeResponse(['user' => $user]);

    }

    public function getSearchOffer()
    {
        $keyword = request('keyword');

        if (!isset($keyword) || $keyword == '') {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $me = request()->user;

        $offer_array = Offer::select([
            't_offer.*'
        ])
            ->where([
                ['creator_id', '<>', $me->id],
                ['title', 'like', "%$keyword%"],
            ])
            ->with('creator', 'creator.photos', 'creator.instagrams', 'category')
            ->get();

        foreach ($offer_array as &$offer) {
            if (isset($offer->creator)) {
                $offer->creator->setVisible([
                    'id',
                    'name',
                    'avatar_filename',
                    'about_me',
                    'photos',
                    'instagrams',
                    'latitude',
                    'longitude',
                    'show_my_distance',
                    'qb_id'
                ]);
            }
        }

        return Utils::makeResponse([
            'offer_array' => $offer_array
        ]);
    }

    public function createOffer()
    {

        $validation = Validator::make(request()->all(), [
            'title' => 'required',
            'date' => 'required',
            'time_hour' => 'required',
            'time_minute' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'location_name' => 'required',
            'description' => 'required',
            'category_id' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $me = request()->user;

        $title = request('title');
        $date = request('date');
        $time_hour = request('time_hour');
        $time_minute = request('time_minute');
        $latitude = request('latitude');
        $longitude = request('longitude');
        $location_name = request('location_name');
        $description = request('description');
        $category_id = request('category_id');

        $offer = new Offer();

        $offer->title = $title;
        $offer->date = $date;
        $offer->time_hour = $time_hour;
        $offer->time_minute = $time_minute;
        $offer->description = $description;
        $offer->category_id = $category_id;
        $offer->location_name = $location_name;
        $offer->latitude = $latitude;
        $offer->longitude = $longitude;
        $offer->creator_id = $me->id;
        $offer->create_timestamp = time();

        $offer->save();

        return Utils::makeResponse();

    }

    public function editOffer()
    {

        $validation = Validator::make(request()->all(), [
            'offer_id' => 'required',
            'title' => 'required',
            'date' => 'required',
            'time_hour' => 'required',
            'time_minute' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'location_name' => 'required',
            'description' => 'required',
            'category_id' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $me = request()->user;

        $offer_id = request('offer_id');
        $title = request('title');
        $date = request('date');
        $time_hour = request('time_hour');
        $time_minute = request('time_minute');
        $latitude = request('latitude');
        $longitude = request('longitude');
        $location_name = request('location_name');
        $description = request('description');
        $category_id = request('category_id');

        $offer = Offer::where([
            ['id', '=', $offer_id],
            ['creator_id', '=', $me->id],
        ])->first();

        if (!isset($offer)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $has_interested_users = InterestedUserRelation::where('offer_id', $offer->id)->count() > 0;

        if ($has_interested_users) {
            return Utils::makeResponse([], config('constants.response-message.already-has-interested-user'));
        }

        $offer->title = $title;
        $offer->date = $date;
        $offer->time_hour = $time_hour;
        $offer->time_minute = $time_minute;
        $offer->description = $description;
        $offer->category_id = $category_id;
        $offer->location_name = $location_name;
        $offer->latitude = $latitude;
        $offer->longitude = $longitude;
//        $offer->creator_id = $me->id;
//        $offer->create_timestamp = time();

        $offer->save();

        return Utils::makeResponse();

    }

    public function getMyOffers()
    {

        $me = request()->user;

        $offers = Offer::where('creator_id', $me->id)
            ->with('creator', 'interested_users', 'interested_users.photos', 'interested_users.instagrams', 'category')
            ->get();

        $starred_users = StarredRelation::where('user_id', $me->id)->with('starred_user')->get()->toArray();

        $starred_users = array_map(function ($item) {
            return $item['starred_user'];
        }, $starred_users);


        foreach ($offers as $offer) {
            $offer->creator->setVisible([
                'id',
                'name',
                'avatar_filename',
                'show_my_distance',
                'latitude',
                'longitude',
                'instagrams',
                'photos',
                'about_me',
                'qb_id'
            ]);

            foreach ($offer->interested_users as $user) {
                $user->setVisible([
                    'id',
                    'name',
                    'avatar_filename',
                    'show_my_distance',
                    'latitude',
                    'longitude',
                    'instagrams',
                    'photos',
                    'about_me',
                    'qb_id'
                ]);
            }
        }

        $offers = $offers->toArray();

        foreach ($offers as &$offer) {
            foreach ($offer['interested_users'] as &$user) {
                $starred = false;
                foreach ($starred_users as $starred_user) {
                    if ($user['id'] == $starred_user['id']) {
                        $starred = true;
                        break;
                    }
                }
                $user['starred'] = $starred ? 1 : 0;
            }
        }

        return Utils::makeResponse(['offer_array' => $offers]);


    }


    public function showInterest()
    {

        $offer_id = request('offer_id');

        if (!isset($offer_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $offer = Offer::where('id', $offer_id)->first();

        if ($offer == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $me = request()->user;

        $creator_id = $offer->creator_id;

        if ($me->id == $creator_id) {
            return Utils::makeResponse([], config('constants.response-message.cannot-show-interest-yourself'));
        }

        $already_interested = InterestedUserRelation::where([
                ['user_id', $me->id],
                ['offer_id', $offer_id]
            ])->count() > 0;

        if (!$already_interested) {
            $relation = new InterestedUserRelation();
            $relation->user_id = $me->id;
            $relation->offer_id = $offer_id;

            $relation->save();
        }

        return Utils::makeResponse();

    }


    public function profileUploadPhoto()
    {

        $validation = Validator::make(request()->all(), [
            'photo' => 'image'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $request = request();

        $images_dir = public_path('images');
        if (!file_exists($images_dir)) {
            mkdir($images_dir);
        }


        $file_name = 'image_' . time() . '.png';

        $request->file('photo')->move($images_dir, $file_name);

        $me = request()->user;

        $photo = new Photo();

        $photo->user_id = $me->id;
        $photo->image_filename = $file_name;
        $photo->save();

        return Utils::makeResponse(['photo' => $file_name]);

    }

    public function profileUpdate()
    {

        $validation = Validator::make(request()->all(), [
            'about_me' => 'required',
            'gender' => 'required|in:MAN,WOMAN,OTHER',
            'show_my_distance' => 'required|in:0,1'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $about_me = request('about_me');
        $gender = request('gender');
        $show_my_distance = request('show_my_distance');

        $me = request()->user;

        $me->about_me = $about_me;
        $me->gender = $gender;
        $me->show_my_distance = $show_my_distance;

        $me->save();

        return Utils::makeResponse();

    }

    public function signupStep2SendEmailOTP()
    {

        $me = request()->user;

        $email = $me->email;

        $email_otp = Utils::genOTP();
        $me->email_otp = $email_otp;

        $me->save();

        try {

//            Mail::to($email)->send(new EmailOTPMailable(['otp' => $email_otp]));

        } catch (\Exception $e) {
            return Utils::makeResponse([], config('constants.response-message.error-send-email-otp'));
        }

        return Utils::makeResponse();


    }

    public function signupStep2SendMobileOTP()
    {

        $me = request()->user;

        $country = $me->country;
        $mobile = $me->mobile;


        $mobile_otp = Utils::genOTP();
        $me->mobile_otp = $mobile_otp;

        $me->save();

        $to_number = $country . $mobile;

        try {
            $sid = Setting::where('key', config('constants.settings-key.twilio-sid'))->first();
            $token = Setting::where('key', config('constants.settings-key.twilio-token'))->first();
            $number = Setting::where('key', config('constants.settings-key.twilio-number'))->first();

            $sid = $sid == null ? '' : ($sid->value == null ? '' : $sid->value);
            $token = $token == null ? '' : ($token->value == null ? '' : $token->value);
            $number = $number == null ? '' : ($number->value == null ? '' : $number->value);

            $twilio = new Client($sid, $token);

            $msg4verification = "Welcome to Deeds\nPlease use $mobile_otp for mobile otp. Thanks.";

//            $message = $twilio->messages
//                ->create($to_number,
//                    array(
//                        "from" => $number,
//                        "body" => $msg4verification
//                    )
//                );

        } catch (ConfigurationException $e) {

            return Utils::makeResponse([], config('constants.response-message.error-send-mobile-otp'));

        }

        return Utils::makeResponse();


    }

    public function sendEmailOTP()
    {


        $validation = Validator::make(request()->all(), [
            'email' => 'required|email'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $email = request('email');

        $existing = User::where('email', $email)->count() > 0;

        if ($existing) {
            return Utils::makeResponse([], config('constants.response-message.email-already-existing'));
        }


        $me = request()->user;

        $me->temp_email = $email;

        $email_otp = Utils::genOTP();
        $me->email_otp = $email_otp;

        $me->save();

        try {

//            Mail::to($email)->send(new EmailOTPMailable(['otp' => $email_otp]));

        } catch (\Exception $e) {
            return Utils::makeResponse([], config('constants.response-message.error-send-email-otp'));
        }

        return Utils::makeResponse();

    }


    public function updateEmail()
    {

        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
            'email_otp' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $email = request('email');
        $email_otp = request('email_otp');

        $existing = User::where('email', $email)->count() > 0;

        if ($existing) {
            return Utils::makeResponse([], config('constants.response-message.email-already-existing'));
        }

        $me = request()->user;
        $temp_email = $me->temp_email;
        $sent_otp = $me->email_otp;

        if (isset($temp_email) && isset($sent_otp) && strcmp($temp_email, $email) == 0 && $email_otp == $sent_otp) {

            $me->email = $email;

            $me->save();

            return Utils::makeResponse();

        }

        return Utils::makeResponse([], config('constants.response-message.invalid-email-otp'));

    }


    public function sendMobileOTP()
    {


        $validation = Validator::make(request()->all(), [
            'country' => 'required',
            'mobile' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $country = request('country');
        $mobile = request('mobile');

        $existing = User::where([
                ['country', $country],
                ['mobile', $mobile],
            ])->count() > 0;

        if ($existing) {
            return Utils::makeResponse([], config('constants.response-message.mobile-already-existing'));
        }


        $me = request()->user;

        $me->temp_country = $country;
        $me->temp_mobile = $mobile;

        $mobile_otp = Utils::genOTP();
        $me->mobile_otp = $mobile_otp;

        $me->save();

        $to_number = $country . $mobile;

        try {
            $sid = Setting::where('key', config('constants.settings-key.twilio-sid'))->first();
            $token = Setting::where('key', config('constants.settings-key.twilio-token'))->first();
            $number = Setting::where('key', config('constants.settings-key.twilio-number'))->first();

            $sid = $sid == null ? '' : ($sid->value == null ? '' : $sid->value);
            $token = $token == null ? '' : ($token->value == null ? '' : $token->value);
            $number = $number == null ? '' : ($number->value == null ? '' : $number->value);

            $twilio = new Client($sid, $token);

            $msg4verification = "Welcome to Deeds\nPlease use $mobile_otp for mobile otp. Thanks.";

//            $message = $twilio->messages
//                ->create($to_number,
//                    array(
//                        "from" => $number,
//                        "body" => $msg4verification
//                    )
//                );

        } catch (ConfigurationException $e) {

            return Utils::makeResponse([], config('constants.response-message.error-send-mobile-otp'));

        }

        return Utils::makeResponse();

    }


    public function updateMobile()
    {

        $validation = Validator::make(request()->all(), [
            'country' => 'required',
            'mobile' => 'required',
            'mobile_otp' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $country = request('country');
        $mobile = request('mobile');
        $mobile_otp = request('mobile_otp');

        $existing = User::where([
                ['country', $country],
                ['mobile', $mobile],
            ])->count() > 0;

        if ($existing) {
            return Utils::makeResponse([], config('constants.response-message.mobile-already-existing'));
        }


        $me = request()->user;
        $temp_country = $me->temp_country;
        $temp_mobile = $me->temp_mobile;
        $sent_otp = $me->mobile_otp;

        if (isset($temp_country) && isset($temp_mobile) && isset($sent_otp) && strcmp($temp_country, $country) == 0 && strcmp($temp_mobile, $mobile) == 0 && $mobile_otp == $sent_otp) {

            $me->country = $country;
            $me->mobile = $mobile;

            $me->save();

            return Utils::makeResponse();

        }

        return Utils::makeResponse([], config('constants.response-message.invalid-mobile-otp'));

    }

    public function updateInterestedGender()
    {

        $validation = Validator::make(request()->all(), [
            'interested_gender' => 'required|in:MAN,WOMAN',
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $interested_gender = request('interested_gender');

        $me = request()->user;

        $me->interested_gender = $interested_gender;

        $me->save();


        return Utils::makeResponse();
    }

    public function updateMaxDistance()
    {

        $validation = Validator::make(request()->all(), [
            'max_distance' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $max_distance = request('max_distance');

        $me = request()->user;

        $me->max_distance = $max_distance;

        $me->save();


        return Utils::makeResponse();
    }

    public function updateAgeRange()
    {

        $validation = Validator::make(request()->all(), [
            'age_lower_value' => 'required|numeric',
            'age_upper_value' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $age_lower_value = request('age_lower_value');
        $age_upper_value = request('age_upper_value');

        if ($age_lower_value >= $age_upper_value) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }


        $me = request()->user;

        $me->age_lower_value = $age_lower_value;
        $me->age_upper_value = $age_upper_value;

        $me->save();


        return Utils::makeResponse();

    }

    public function updateAllSettings()
    {


        $max_distance = request('max_distance');

        $age_lower_value = request('age_lower_value');
        $age_upper_value = request('age_upper_value');

        $interested_gender = request('interested_gender');

        $me = request()->user;

        if (isset($max_distance) && is_numeric($max_distance)) {
            $me->max_distance = $max_distance;
        }

        if (isset($interested_gender) && ($interested_gender == 'MAN' || $interested_gender == 'WOMAN')) {
            $me->interested_gender = $interested_gender;
        }

        if (isset($age_lower_value) && isset($age_upper_value)) {
            if ($age_lower_value < $age_upper_value) {
                $me->age_lower_value = $age_lower_value;
                $me->age_upper_value = $age_upper_value;
            }

        }

        $me->save();

        return Utils::makeResponse();

    }

    public function getCategory()
    {
        $category_array = Category::all();

        return Utils::makeResponse(['category_array' => $category_array]);
    }


}
