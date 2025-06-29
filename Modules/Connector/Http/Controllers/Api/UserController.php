<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Contact;
use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Connector\Notifications\NewPassword;
use Modules\Connector\Transformers\CommonResource;

/**
 * @group User management
 * @authenticated
 *
 * APIs for managing users
 */
class UserController extends ApiController
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * List users
     *
     * @queryParam service_staff boolean Filter service staffs from users list (0, 1)
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "user_type": "user",
                    "surname": "Mr",
                    "first_name": "Admin",
                    "last_name": null,
                    "username": "admin",
                    "email": "admin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "0.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "fb_link": null,
                    "twitter_link": null,
                    "social_media_1": null,
                    "social_media_2": null,
                    "permanent_address": null,
                    "current_address": null,
                    "guardian_name": null,
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null,
                    "bank_details": null,
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:19",
                    "updated_at": "2018-01-04 02:15:19"
                }
            ]
        }
     */
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        if (! empty(request()->service_staff) && request()->service_staff == 1) {
            $users = $this->commonUtil->getServiceStaff($business_id);
        } else {
            $users = User::where('business_id', $business_id)
                        ->get();
        }

        return CommonResource::collection($users);
    }

    /**
     * Get the specified user
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "user_type": "user",
                    "surname": "Mr",
                    "first_name": "Admin",
                    "last_name": null,
                    "username": "admin",
                    "email": "admin@example.com",
                    "language": "en",
                    "contact_no": null,
                    "address": null,
                    "business_id": 1,
                    "max_sales_discount_percent": null,
                    "allow_login": 1,
                    "essentials_department_id": null,
                    "essentials_designation_id": null,
                    "status": "active",
                    "crm_contact_id": null,
                    "is_cmmsn_agnt": 0,
                    "cmmsn_percent": "0.00",
                    "selected_contacts": 0,
                    "dob": null,
                    "gender": null,
                    "marital_status": null,
                    "blood_group": null,
                    "contact_number": null,
                    "fb_link": null,
                    "twitter_link": null,
                    "social_media_1": null,
                    "social_media_2": null,
                    "permanent_address": null,
                    "current_address": null,
                    "guardian_name": null,
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null,
                    "bank_details": null,
                    "id_proof_name": null,
                    "id_proof_number": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:19",
                    "updated_at": "2018-01-04 02:15:19"
                }
            ]
        }
     * @urlParam user required comma separated ids of the required users Example: 1
     */
    public function show($user_ids)
    {
        $user = Auth::user();
        dd($user) ;

        $business_id = $user->business_id;
        $user_ids = explode(',', $user_ids);

        $users = User::where('business_id', $business_id)
                    ->whereIn('id', $user_ids)
                    ->get();

        return CommonResource::collection($users);
    }

    /**
     * Get the loggedin user details.
     *
     * @response {
            "data":{
                "id": 1,
                "user_type": "user",
                "surname": "Mr",
                "first_name": "Admin",
                "last_name": null,
                "username": "admin",
                "email": "admin@example.com",
                "language": "en",
                "contact_no": null,
                "address": null,
                "business_id": 1,
                "max_sales_discount_percent": null,
                "allow_login": 1,
                "essentials_department_id": null,
                "essentials_designation_id": null,
                "status": "active",
                "crm_contact_id": null,
                "is_cmmsn_agnt": 0,
                "cmmsn_percent": "0.00",
                "selected_contacts": 0,
                "dob": null,
                "gender": null,
                "marital_status": null,
                "blood_group": null,
                "contact_number": null,
                "fb_link": null,
                "twitter_link": null,
                "social_media_1": null,
                "social_media_2": null,
                "permanent_address": null,
                "current_address": null,
                "guardian_name": null,
                "custom_field_1": null,
                "custom_field_2": null,
                "custom_field_3": null,
                "custom_field_4": null,
                "bank_details": null,
                "id_proof_name": null,
                "id_proof_number": null,
                "deleted_at": null,
                "created_at": "2018-01-04 02:15:19",
                "updated_at": "2018-01-04 02:15:19"
            }
        }
     */
    public function loggedin()
    {
        $user = Auth::user();
        $user->is_admin = $this->commonUtil->is_admin($user);

        if (! $user->is_admin) {
            $user->all_permissions = $user->getAllPermissions()->pluck('name');
        }
        unset($user->permissions);
        unset($user->roles);

        return new CommonResource($user);
    }

    /**
     * Update user password.
     *
     * @bodyParam current_password string required Current password of the user
     * @bodyParam new_password string required New password of the user
     *
     * @response {
            "success":1,
            "msg":"Password updated successfully"
        }
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (! empty($request->input('current_password')) && ! empty($request->input('new_password'))) {
                if (Hash::check($request->input('current_password'), $user->password)) {
                    $user->password = Hash::make($request->input('new_password'));
                    $user->save();
                    $output = ['success' => 1,
                        'msg' => __('lang_v1.password_updated_successfully'),
                    ];
                } else {
                    $output = ['success' => 0,
                        'msg' => __('lang_v1.u_have_entered_wrong_password'),
                    ];
                }
            } else {
                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $this->respond($output);

       /* if ($output['success']) {
            return $this->respond($output);
        } else {
            return $this->otherExceptions($output['msg']);
        }*/
    }

    /**
     * Register User
     *
     * @bodyParam surname string prefix like Mr, Mrs,Dr
     * @bodyParam first_name string required
     * @bodyParam last_name string
     * @bodyParam email string required
     * @bodyParam is_active string required 'active', 'inactive', 'terminated'
     * @bodyParam user_type string required 'user_customer' for contact/customer login & 'user' for general user
     * @bodyParam crm_contact_id integer if user_type is 'user_customer' then required
     * @bodyParam allow_login boolean 1 to allow login & 0 to disable login
     * @bodyParam username string minimum 5 characters
     * @bodyParam password string minimum 6 characters & required if 'allow_login' is 1
     * @bodyParam role integer id of role to be assigned to user & required if user_type is 'user'
     * @bodyParam access_all_locations boolean 1 if user has access all location else 0 & required if user_type is 'user'
     * @bodyParam location_permissions array array of location ids to be assigned to user & required if user_type is 'user' and 'access_all_locations' is 0
     * @bodyParam cmmsn_percent decimal
     * @bodyParam max_sales_discount_percent decimal
     * @bodyParam selected_contacts boolean 1 or 0
     * @bodyParam selected_contact_ids array array of contact ids & required if 'selected_contacts' is 1
     * @bodyParam dob date dob of user in "Y-m-d" format Ex: 1997-10-29
     * @bodyParam gender string if user is 'male', 'female', 'others'
     * @bodyParam marital_status string if user is 'married', 'unmarried', 'divorced'
     * @bodyParam blood_group string
     * @bodyParam contact_number string
     * @bodyParam alt_number string
     * @bodyParam family_number string
     * @bodyParam fb_link string
     * @bodyParam twitter_link string
     * @bodyParam social_media_1 string
     * @bodyParam social_media_2 string
     * @bodyParam custom_field_1 string
     * @bodyParam custom_field_2 string
     * @bodyParam custom_field_3 string
     * @bodyParam custom_field_4 string
     * @bodyParam guardian_name string
     * @bodyParam id_proof_name string ID proof of user like Adhar No.
     * @bodyParam id_proof_number string Id Number like adhar number
     * @bodyParam permanent_address string
     * @bodyParam current_address string
     * @bodyParam bank_details.*.account_holder_name string
     * @bodyParam bank_details.*.account_number string
     * @bodyParam bank_details.*.bank_name string
     * @bodyParam bank_details.*.bank_code string
     * @bodyParam bank_details.*.branch string
     * @bodyParam bank_details.*.tax_payer_id string
     *
     * @response {
        "success": 1,
        "msg": "User added successfully",
        "user": {
            "surname": "Mr",
            "first_name": "Test",
            "last_name": "kumar",
            "email": "test@example.com",
            "user_type": "user_customer",
            "crm_contact_id": "2",
            "allow_login": 1,
            "username": "0017",
            "cmmsn_percent": "25",
            "max_sales_discount_percent": "52",
            "dob": "1997-10-12",
            "gender": "male",
            "marital_status": "unmarried",
            "blood_group": "0+",
            "contact_number": "4578451245",
            "alt_number": "7474747474",
            "family_number": "7474147414",
            "fb_link": "fb.com/username",
            "twitter_link": "twitter.com/username",
            "social_media_1": "test",
            "social_media_2": "test",
            "custom_field_1": "test",
            "custom_field_2": "test",
            "custom_field_3": "test",
            "custom_field_4": "test",
            "guardian_name": "test",
            "id_proof_name": "uid",
            "id_proof_number": "747845120124",
            "permanent_address": "test permanent adrress",
            "current_address": "test current address",
            "bank_details": "{\"account_holder_name\":\"test\",\"account_number\":\"test\",\"bank_name\":\"test\",\"bank_code\":\"test\",\"branch\":\"test\",\"tax_payer_id\":\"test\"}",
            "selected_contacts": "1",
            "status": "active",
            "business_id": 1,
            "updated_at": "2021-08-12 18:03:58",
            "created_at": "2021-08-12 18:03:58",
            "id": 140
        }
    }
     */
    public function registerUser(Request $request)
    {

        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $input = $request->only([ 'name',
                'mobile', 'email', 'password','address','governorate','building','street','city'
            ]);


            /*check if Email exit*/

            $is_exist=User::where('email',$input['email'])->whereNull('deleted_at')->count();
            if($is_exist>0){
                $output = [
                    'success' => 10,
                    'msg' => 'عفوا هذا الإيميل تم تسجيلة',
                ];

                return $output;
            }



            $user_details['first_name']=$input['name'];
            $user_details['username']=$input['email'];
            $user_details['email']=$input['email'];
            $user_details['address']=$input['address'];
            $user_details['contact_number']=$input['mobile'];
            $user_details['status'] = 'inactive';
            $user_details['email_confirmed'] = 0;
            $user_details['user_type'] = 'site_user';
            $user_details['business_id'] = $business_id;
            $user_details['password'] =Hash::make($input['password']);

            DB::beginTransaction();
            $site_user = User::create($user_details);

            //add customer

            $contact_data['name']=$input['name'];
            $contact_data['first_name']=$input['name'];
            $contact_data['username']=$input['email'];
            $contact_data['email']=$input['email'];
            $contact_data['address_line_1']=$input['address'];
            $contact_data['shipping_address']=$input['address'];
            $contact_data['mobile']=$input['mobile'];
            $contact_data['contact_status'] = 'active';
            $contact_data['email_confirmed'] = 0;
            $contact_data['user_type'] = 'site_user';
            $contact_data['business_id'] = $business_id;
            $contact_data['created_by'] = $user->id;
            $contact_data['type'] = 'customer';
            $contact_data['contact_type'] = 'customer';

            $contact_data['governorate'] =$input['governorate'];
            $contact_data['building'] = $input['building'];
            $contact_data['street'] = $input['street'];
            $contact_data['city'] =$input['city'];



            $contact = Contact::create($contact_data);

            $contact->contact_id='website-'.$contact->id;
            $contact->save();

            $site_user->crm_contact_id=$contact->id;
            $site_user->save();

            $output = [
                'success' => 1,
                'msg' => __('user.user_added'),
                'user'=>['name'=>$site_user->first_name,
                         'mobile'=>$site_user->contact_number,
                         'email'=>$site_user->email,
                         'address'=>$site_user->address,
                         'contact_id'=>$site_user->crm_contact_id,
                         'governorate'=>$contact->governorate,
                         'street' => $contact->street,
                         'city' =>$contact->city,
                         'building' => $contact->building,
                ]

            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
                'error'=>$e->getMessage()
            ];
        }

        if ($output['success']) {
            return $this->respond($output);
        } else {
            return $output;
        }
    }

    public function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#@_$&%*(){}[]!^?><=';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Recover forgotten password.
     *
     * @bodyParam email string required Users email id
     *
     * @response {
            "success":1,
            "msg":"New password sent to user@example.com successfully"
        }
     */
    public function forgetPassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (! empty($request->input('email'))) {
                $forgotten_user = User::where('business_id', $user->business_id)
                                    ->where('email', $request->input('email'))
                                    ->first();
                if (! empty($forgotten_user)) {
                    $new_password = $this->generateRandomString();
                    $forgotten_user->password = Hash::make($new_password);
                    $forgotten_user->save();

                    $forgotten_user->notify(new NewPassword($new_password));
                    $output = ['success' => 1,
                        'msg' => "New password sent to {$forgotten_user->email} successfully",
                    ];
                } else {
                    $output = ['success' => 0,
                        'msg' => 'User not found',
                    ];
                }
            } else {
                $output = ['success' => 0,
                    'msg' => 'Email Required',
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage(),
            ];
        }

        if ($output['success']) {
            return $this->respond($output);
        } else {
            return $this->otherExceptions($output['msg']);
        }
    }


    public function login(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        try {
            $input = $request->only(['username', 'password' ]);

            $password=Hash::make($input['password']);

            $user=User::where('business_id',$business_id)->where('username',$input['username'])
                         ->select('crm_contact_id as contact_id', 'first_name','contact_number','email','address','password')
                        ->first();

            if(!$user){
                $output = [
                    'success' => 10,
                    'msg' => 'User Not found'
                ];
                return $output;
            }


            if (!Hash::check($request->password, $user->password)) {
                $output = [
                    'success' => 11,
                    'msg' => 'Invalid data'
                ];
                return $output;
            }


            // Get customer Id

            $customer=Contact::where('id',$user->contact_id)->first();



                $output = [
                    'success' => 1,
                    'msg' => 'Logged in Successfully',
                    'user'=>[
                             'contact_id'=>$user->contact_id,
                             'name'=>$user->first_name,
                             'mobile'=>$user->contact_number,
                             'email'=>$user->email,
                             'address'=>$user->address,
                             'governorate'=>$customer->governorate,
                             'street' => $customer->street,
                             'city' =>$customer->city,
                             'building' => $customer->building,



                             ]
                ];
                return $output;


        } catch (\Exception $e) {

            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
                'error'=>$e->getMessage()
            ];

            return  $output;
        }
    }

}
