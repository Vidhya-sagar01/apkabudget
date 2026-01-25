<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpParser\Node\Stmt\Echo_;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\Transaction;
use App\Models\IdentityType;
use App\Models\Country;
use App\Models\Service;
use App\Models\ContactUs;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Zone;
use App\Models\Plan;
use App\Models\Part;
use App\Models\PriceList;
use App\Models\Subscription;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CancelledOrder;
use App\Models\Address;
use App\Models\Notification;
use App\Models\ZoneProvider;
use App\Models\ServiceVideo;
use App\Models\Banner;
use App\Models\AboutUs;
use App\Models\PrivacyPolicy;
use App\Models\TermCondition;
use App\Models\OrderAssignmentAttempt;
use App\Services\NotificationService;
use App\Models\Complaint;
use App\Models\HowItWork;
use App\Models\Admin;
use Carbon\Carbon;
use App\Models\Partner;
use App\Models\OfficeTiming;
use App\Models\Attendance;
use App\Models\Contact;
use App\Models\RatingReview;
use App\Imports\PartnerImport;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;


/** ============================
 * ✅Dashboard Functionality 
 * ============================ */
class AdminController extends Controller
{
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    protected function index()
    {
        $totalUsers = User::where('role', 1)->count();
        $totalProviders = User::where('role', 2)->count();
        
        $totalEarning = Transaction::where(['transaction' => 2, 'status' => 'success'])->sum('amount');
        
        $admin = Auth::guard('admin')->user();
        $timings = DB::table('office_timings')->get()->keyBy('day_of_week');
        $today = now()->toDateString();
        $admin = Auth::guard('admin')->user();
        $todayAttendance = Attendance::where('admin_id', $admin->id)->where('date', $today)->first();

        return view('Admin.dashboard', compact('totalUsers', 'totalProviders', 'totalEarning','todayAttendance','timings'));
    }
    protected function plans($category_id)
    {
        $plans = Plan::where('category_id', $category_id)->get();
        return view('Admin.plans.index', compact('plans', 'category_id'));
    }
    protected function add_plans(Request $request, $category_id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name'       => 'required|string|max:255',
                'price'      => 'required|numeric',
                'duration'   => 'required|integer',
                'leads'      => 'required|integer',
                'type'       => 'required|in:1,2',
                'features'   => 'nullable|string',
                'plan_size'  => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $plan = new Plan();
            $plan->name = $request->name;
            $plan->price = $request->price;
            $plan->duration = $request->duration;
            $plan->leads = $request->leads;
            $plan->type = $request->type;
            $plan->features = $request->features;
            $plan->category_id = $category_id;
            $plan->plan_size = $request->type == 2 ? 0 : $request->plan_size; // <- logic here
            $plan->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Plan added successfully',
                    'route' => route('admin.plans', ['category_id' => $category_id])
                ], 200);
            }
        }

        return view('Admin.plans.add', compact('category_id'));
    }
    protected function edit_plans(Request $request, $category_id, $id)
    {
        $plan = Plan::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:0',
                'leads' => 'required|integer|min:0',
                'type' => 'required|in:1,2',
                'plan_size' => 'nullable|in:0,1,2',
                'features' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $plan->name = $request->name;
            $plan->price = $request->price;
            $plan->duration = $request->duration;
            $plan->leads = $request->leads;
            $plan->type = $request->type;

            // Agar type = 2 (Security Plan) hai to plan_size 0 hi set karenge
            if ($request->type == 2) {
                $plan->plan_size = 0;
            } else {
                // Otherwise jo user ne select kiya wo set karenge
                $plan->plan_size = $request->plan_size ?? null;
            }

            $plan->features = $request->features;
            $plan->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Plan updated successfully',
                    'route' => route('admin.plans', ['category_id' => $category_id]),
                ], 200);
            }
        }

        return view('Admin.plans.edit', compact('category_id', 'plan'));
    }
    protected function categories()
    {
        $categories = Category::all();
        return view('Admin.category.index', compact('categories'));
    }
    protected function add_categories(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'max_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $categories = new Category();
            $categories->category = $request->category;
            $categories->max_price = $request->max_price;

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $categories->image = 'uploads/categories/' . $imageName;
            }
            $categories->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Category added successfully',
                    'route' => route('admin.categories')
                ], 200);
            }
        }
        return view('Admin.category.add');
    }
    protected function edit_categories(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('admin.add_categories')->with('error', 'Category not found.');
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'max_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $category->category = $request->category;
            $category->max_price = $request->max_price;

            if ($request->hasFile('image')) {
                if ($category->image && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $category->image = 'uploads/categories/' . $imageName;
            }

            $category->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Category updated successfully',
                    'route' => route('admin.categories'),
                ], 200);
            }
        }
        return view('Admin.category.edit', compact('category'));
    }
    protected function delete_categories($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'Category not found!',
            ], 404);
        }

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Category deleted successfully!',
            'route' => route('admin.categories'),
        ], 200);
    }
    protected function parts($categoryId)
    {
        // $category = Category::findOrFail($categoryId);
        $parts = Part::where('category_id', $categoryId)->get();
        return view('Admin.parts.index', compact('categoryId', 'parts'));
    }
    protected function add_parts(Request $request, $categoryId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'part' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $part = new Part();
            $part->category_id = $categoryId;
            $part->part = $request->part;

            $part->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Part added successfully',
                    'route' => route('admin.parts', ['categoryId' => $categoryId])
                ], 200);
            }
        }

        return view('Admin.parts.add', compact('categoryId'));
    }
    protected function edit_parts(Request $request, $categoryId, $id)
    {
        $part = Part::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'part' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $part->category_id = $categoryId;
            $part->part = $request->part;

            $part->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Part updated successfully',
                    'route' => route('admin.parts', ['categoryId' => $categoryId]),
                ], 200);
            }
        }

        return view('Admin.parts.edit', compact('categoryId', 'part'));
    }
    protected function delete_parts($categoryId, $id)
    {
        $part = Part::where('category_id', $categoryId)->find($id);

        if (!$part) {
            return response()->json([
                'status' => 0,
                'message' => 'Part not found!',
            ], 404);
        }

        $priceListExists = PriceList::where('part_id', $id)->exists();

        if ($priceListExists) {
            return response()->json([
                'status' => 0,
                'message' => 'Cannot delete part. It is used in the price list!',
            ], 400);
        }

        $part->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Part deleted successfully!',
            'route' => route('admin.parts', ['categoryId' => $categoryId]),
        ], 200);
    }

    protected function price_list($categoryId, $partId)
    {
        $price_lists = PriceList::where('part_id', $partId)->get();
        return view('Admin.price_lists.index', compact('price_lists', 'categoryId', 'partId'));
    }
    protected function add_price_list(Request $request, $categoryId, $partId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'detail' => 'required|string|max:255',
                'charge' => 'required|numeric',
                'labour_charge' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $price_list = new PriceList();
            $price_list->part_id = $partId;
            $price_list->detail = $request->detail;
            $price_list->charge = $request->charge;
            $price_list->labour_charge = $request->labour_charge;

            $price_list->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Price added successfully',
                    'route' => route('admin.price_list', ['categoryId' => $categoryId, 'partId' => $partId])
                ], 200);
            }
        }

        return view('Admin.price_lists.add', compact('categoryId', 'partId'));
    }
    protected function edit_price_list(Request $request, $categoryId, $partId, $id)
    {
        $price_list = PriceList::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'detail' => 'required|string|max:255',
                'charge' => 'required|numeric',
                'labour_charge' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $price_list->detail = $request->detail;
            $price_list->charge = $request->charge;
            $price_list->labour_charge = $request->labour_charge;

            $price_list->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Price updated successfully',
                    'route' => route('admin.price_list', ['categoryId' => $categoryId, 'partId' => $partId])
                ], 200);
            }
        }

        return view('Admin.price_lists.edit', compact('price_list', 'categoryId', 'partId'));
    }
    protected function delete_price_list($categoryId, $partId, $id)
    {
        $price_list = PriceList::where('part_id', $partId)->find($id);

        if (!$price_list) {
            return response()->json([
                'status' => 0,
                'message' => 'Price not found!',
            ], 404);
        }

        $price_list->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Price deleted successfully!',
            'route' => route('admin.price_list', ['categoryId' => $categoryId, 'partId' => $partId])
        ], 200);
    }

    // Service Videos
    protected function service_videos()
    {
        $service_videos = ServiceVideo::OrderBy('id', 'DESC')->get();
        return view('Admin.service_videos.index', compact('service_videos'));
    }
    protected function add_service_videos(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'video' => 'required|mimes:mp4,avi,mov,mkv|max:51200' // Allow only video formats & max 50MB
            ]);

            if ($validator->fails()) {
                if ($request->json()) {
                    return response()->json([
                        'status' => 'false',
                        'message' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $ServiceVideo = new ServiceVideo();

            if ($request->hasFile('video')) {
                $videoName = time() . '.' . $request->file('video')->getClientOriginalExtension();
                $request->file('video')->move(public_path('uploads/videos'), $videoName);
                $ServiceVideo->video_url = 'uploads/videos/' . $videoName;
            }

            $ServiceVideo->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Service Video added successfully',
                    'route' => route('admin.service_videos')
                ], 200);
            }
        }
        return view('Admin.service_videos.add');
    }

    protected function edit_service_videos(Request $request, $id)
    {
        $ServiceVideo = ServiceVideo::find($id);
        if (!$ServiceVideo) {
            return redirect()->route('admin.service_videos')->with('error', 'Service Videos not found.');
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'video' => 'nullable|mimes:mp4,avi,mov,mkv|max:51200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('video')) {
                // Delete old video if exists
                if ($ServiceVideo->video_url && file_exists(public_path($ServiceVideo->video_url))) {
                    unlink(public_path($ServiceVideo->video_url));
                }

                $videoName = time() . '.' . $request->file('video')->getClientOriginalExtension();
                $request->file('video')->move(public_path('uploads/videos'), $videoName);
                $ServiceVideo->video_url = 'uploads/videos/' . $videoName;
            }

            $ServiceVideo->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Service Video updated successfully',
                    'route' => route('admin.service_videos'),
                ], 200);
            }
        }
        return view('Admin.service_videos.edit', compact('ServiceVideo'));
    }
    protected function delete_service_videos($id)
    {
        $ServiceVideo = ServiceVideo::find($id);

        if (!$ServiceVideo) {
            return response()->json([
                'status' => 0,
                'message' => 'Service Video not found!',
            ], 404);
        }

        // Delete the video file if it exists
        if ($ServiceVideo->video_url && file_exists(public_path($ServiceVideo->video_url))) {
            unlink(public_path($ServiceVideo->video_url));
        }

        // Delete the record from the database
        $ServiceVideo->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Service Video deleted successfully!',
            'route' => route('admin.service_videos'),
        ], 200);
    }
    protected function about_us(Request $request)
    {
        $userContent = AboutUs::where('type', 1)->first();
        $partnerContent = AboutUs::where('type', 2)->first();
        return view('Admin.about-us.index', compact('userContent', 'partnerContent'));
    }
    public function update_about_us(Request $request)
    {
        $request->validate([
            'type' => 'required|in:1,2',
            'content' => 'required',
        ]);

        DB::table('about_us')->updateOrInsert(
            ['type' => $request->type],
            [
                'content' => $request->content,
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'About Us content updated.');
    }
    protected function privacy_policy(Request $request)
    {
        $userContent = PrivacyPolicy::where('type', 1)->first();
        $partnerContent = PrivacyPolicy::where('type', 2)->first();
        return view('Admin.privacy-policy.index', compact('userContent', 'partnerContent'));
    }
    public function update_privacy_policy(Request $request)
    {
        $request->validate([
            'type' => 'required|in:1,2',
            'content' => 'required',
        ]);

        PrivacyPolicy::updateOrInsert(['type' => $request->type],['content' => $request->content]);

        return back()->with('success', 'Privacy Policy content updated.');
    }
    public function terms_condition(Request $request)
    {
        $categories = Category::all();
        $terms = TermCondition::all()->keyBy('category_id');
        return view('Admin.terms-condition.index', compact('categories', 'terms'));
    }

    public function update_terms_condition(Request $request)
    {
        $validated = $request->validate([
            'content_english' => 'required|array',
            'content_hindi' => 'required|array',
        ]);

        foreach ($request->content_english as $category_id => $content_en) {
            $content_hi = $request->content_hindi[$category_id] ?? '';

            TermCondition::updateOrCreate(
                ['category_id' => $category_id],
                [
                    'content_english' => $content_en,
                    'content_hindi' => $content_hi
                ]
            );
        }

        return redirect()->back()->with('success', 'Terms & Conditions updated successfully!');
    }
    protected function contact_us(Request $request)
    {
        $contact = ContactUs::first();
        return view('Admin.contact-us.index', compact('contact'));
    }
    public function update_contact_us(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'address' => 'required|string|max:255',
        ]);

        ContactUs::updateOrCreate(
            ['id' => 1],
            [
                'phone_number'     => $request->phone_number,
                'whatsapp_number'  => $request->whatsapp_number,
                'email'            => $request->email,
                'address'          => $request->address,
            ]
        );

        return back()->with('success', 'Contact Us details updated successfully.');
    }
    
    protected function subadmins(Request $request)
    {
        $subadmins = Admin::where('role', 2)->get();
        return view('Admin.subadmins.index', compact('subadmins'));
    }
    protected function add_subadmins(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                // BASIC INFO
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email',
                'mobile_no' => 'required|digits:10',
                'mobile_official' => 'required|digits:10|different:mobile_no',
                'password' => 'required|string|min:6',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                // 'category' => 'required|string',
                // PERMISSIONS
                'permissions' => 'nullable|array',
                'permissions.*' => 'string',
                // DOCUMENTS
                'aadhaar_front' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'aadhaar_back' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'pan_card' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'marksheet_10' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'marksheet_12' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'experience_letter' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
                // BANK DETAILS
                'bank_name' => 'nullable|string|max:255',
                'account_holder' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:30',
                'ifsc_code' => 'nullable|string|max:20',
                'branch_name' => 'nullable|string|max:255',
                // SALARY
                'salary_amount' => 'required|numeric|min:0',
                'salary_type' => 'required|string|in:Fixed,Variable,Commission',
                'joining_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

             $admin = new Admin();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->mobile_no = $request->mobile_no;
            $admin->mobile_official = $request->mobile_official;
            $admin->mobile_emergency = $request->mobile_emergency;
            $admin->password = Hash::make($request->password); // for login
            $admin->temp_password = $request->password; // for display
            $admin->status = 1;
            $admin->role = 2;
            $admin->category = $request->category;
            $admin->permissions = json_encode($request->permissions ?? []);

            if ($request->hasFile('image')) {
                $admin->image = $this->uploadFile($request->file('image'), 'uploads/admin');
            }

            // Upload documents
            $documents = [
                'aadhaar_front',
                'aadhaar_back',
                'pan_card',
                'marksheet_10',
                'marksheet_12',
                'experience_letter'
            ];

            foreach ($documents as $doc) {
                if ($request->hasFile($doc)) {
                    $admin->{$doc} = $this->uploadFile($request->file($doc), 'uploads/admin/docs');
                }
            }

            // Bank details
            $admin->bank_name = $request->bank_name;
            $admin->account_holder = $request->account_holder;
            $admin->account_number = $request->account_number;
            $admin->ifsc_code = $request->ifsc_code;
            $admin->branch_name = $request->branch_name;

            // Salary details
            $admin->salary_amount = $request->salary_amount;
            $admin->salary_type = $request->salary_type;
            $admin->joining_date = $request->joining_date;

            // if ($request->hasFile('image')) {
            //     $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
            //     $request->file('image')->move(public_path('uploads/admin'), $imageName);
            //     $admin->image = 'uploads/admin/' . $imageName;
            // }

            // $admin->permissions = json_encode($request->permissions ?? []);
            $admin->save();
            
            foreach ($request->timings as $day => $timing) {
                OfficeTiming::create([
                    'admin_id' => $admin->id, // Assuming you just created this
                    'day_of_week' => $day,
                    'start_time' => $timing['start'],
                    'end_time' => $timing['end'],
                    'lunch_start' => $timing['lunch_start'] ?? null,
                    'lunch_end' => $timing['lunch_end'] ?? null,
                ]);
            }

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Subadmin added successfully',
                    'route' => route('admin.subadmins')
                ], 200);
            }
        }
        return view('Admin.subadmins.add');
    }
    private function uploadFile($file, $folder)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($folder), $filename);
        return $folder . '/' . $filename;
    }
    protected function edit_subadmins(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return redirect()->route('admin.subadmins')->with('error', 'Subadmin not found.');
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
               'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email,' . $id,
                'mobile_no' => 'required|string|max:20',
                // 'mobile_official' => 'required|string|max:20',
                'password' => 'nullable|string|min:6',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'aadhaar_front' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'aadhaar_back' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'pan_card' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'marksheet_10' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'marksheet_12' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'experience_letter' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'bank_name' => 'nullable|string|max:255',
                'account_holder' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:20',
                'ifsc_code' => 'nullable|string|max:11',
                'branch_name' => 'nullable|string|max:255',
                'salary_amount' => 'required|numeric',
                'salary_type' => 'required|string|in:Fixed,Variable,Commission',
                'joining_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->mobile_no = $request->mobile_no;
            $admin->mobile_official = $request->mobile_official;
            $admin->mobile_emergency = $request->mobile_emergency;
            $admin->category = $request->category;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
                $admin->temp_password = $request->password;
            }

            if ($request->hasFile('image')) {
                if ($admin->image && file_exists(public_path($admin->image))) {
                    unlink(public_path($admin->image));
                }
                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('uploads/admin'), $imageName);
                $admin->image = 'uploads/admin/' . $imageName;
            }
            
            $documents = [
                'aadhaar_front',
                'aadhaar_back',
                'pan_card',
                'marksheet_10',
                'marksheet_12',
                'experience_letter'
            ];
            foreach ($documents as $doc) {
                if ($request->hasFile($doc)) {
                    if ($admin->$doc && file_exists(public_path($admin->$doc))) {
                        unlink(public_path($admin->$doc));
                    }
                    $docName = time() . '.' . $request->$doc->getClientOriginalExtension();
                    $request->$doc->move(public_path('uploads/admin/documents'), $docName);
                    $admin->$doc = 'uploads/admin/documents/' . $docName;
                }
            }

            $admin->permissions = json_encode($request->permissions ?? []);
            $admin->bank_name = $request->bank_name;
            $admin->account_holder = $request->account_holder;
            $admin->account_number = $request->account_number;
            $admin->ifsc_code = $request->ifsc_code;
            $admin->branch_name = $request->branch_name;
            $admin->salary_amount = $request->salary_amount;
            $admin->salary_type = $request->salary_type;
            $admin->joining_date = $request->joining_date;
            $admin->save();
            
            if ($request->has('timings') && is_array($request->timings)) {
                foreach ($request->timings as $day => $timing) {
                    OfficeTiming::updateOrCreate(
                        ['admin_id' => $admin->id, 'day_of_week' => $day],
                        [
                            'start_time' => $timing['start'],
                            'end_time' => $timing['end'],
                            'lunch_start' => $timing['lunch_start'] ?? null,
                            'lunch_end' => $timing['lunch_end'] ?? null,
                        ]
                    );
                }
            }

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Subadmin updated successfully',
                    'route' => route('admin.subadmins'),
                ], 200);
            }
        }
        $timings = OfficeTiming::where('admin_id', $id)
            ->get()
            ->keyBy('day_of_week')
            ->toArray();
        return view('Admin.subadmins.edit', compact('admin','timings'));
    }
    protected function delete_subadmins($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 0,
                'message' => 'SubAdmin not found!',
            ], 404);
        }

        if ($admin->image && file_exists(public_path($admin->image))) {
            unlink(public_path($admin->image));
        }

        $admin->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Subadmin deleted successfully!',
            'route' => route('admin.subadmins'),
        ], 200);
    }

    // Banners
    protected function banners(Request $request)
    {
        $query = Banner::query();

        // Apply filter if AJAX request and type is present
        if ($request->ajax() && $request->filled('type') && in_array($request->type, ['1', '2', '3'])) {
            $query->where('type', $request->type);
        }

        $banners = $query->get();

        // Return partial view if it's an AJAX call
        if ($request->ajax()) {
            return view('Admin.banners.partials.table', compact('banners'));
        }
        return view('Admin.banners.index', compact('banners'));
    }
    protected function add_banners(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:1,2,3',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'type.required' => 'Type field is required.',
                'type.in' => 'Invalid type. Only 1, 2, or 3 are allowed.',
                'image.required' => 'Image is required.',
                'image.image' => 'Uploaded file must be an image.',
                'image.mimes' => 'Only jpeg, png, jpg, and webp formats are allowed.',
                'image.max' => 'Image size should not exceed 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }


            $banners = new Banner();

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/banners'), $imageName);
                $banners->image = 'uploads/banners/' . $imageName;
            }
            $banners->type = $request->type;
            $banners->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Banner added successfully',
                    'route' => route('admin.banners')
                ], 200);
            }
        }
        return view('Admin.banners.add');
    }

    protected function edit_banners(Request $request, $id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return redirect()->route('admin.banners')->with('error', 'Banner not found.');
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:1,2,3',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'type.required' => 'Type field is required.',
                'type.in' => 'Invalid type. Only 1, 2, or 3 are allowed.',
                'image.required' => 'Image is required.',
                'image.image' => 'Uploaded file must be an image.',
                'image.mimes' => 'Only jpeg, png, jpg, and webp formats are allowed.',
                'image.max' => 'Image size should not exceed 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            if ($request->hasFile('image')) {
                if ($banner->image && file_exists(public_path($banner->image))) {
                    unlink(public_path($banner->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/banners'), $imageName);
                $banner->image = 'uploads/banners/' . $imageName;
            }
            $banner->type = $request->type;
            $banner->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Banner updated successfully',
                    'route' => route('admin.banners'),
                ], 200);
            }
        }
        return view('Admin.banners.edit', compact('banner'));
    }
    protected function delete_banners($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => 0,
                'message' => 'Banner not found!',
            ], 404);
        }

        if ($banner->image && file_exists(public_path($banner->image))) {
            unlink(public_path($banner->image));
        }

        $banner->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Banner deleted successfully!',
            'route' => route('admin.banners'),
        ], 200);
    }

    protected function subcategories($categoryId)
    {
        // $category = Category::findOrFail($categoryId);
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        return view('Admin.subcategory.index', compact('categoryId', 'subcategories'));
    }
    protected function add_subcategory(Request $request, $categoryId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                // 'details' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                if ($request->json()) {
                    return response()->json([
                        'status' => 'false',
                        'message' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $subcategory = new SubCategory();
            $subcategory->category_id = $categoryId;
            $subcategory->name = $request->name;
            $subcategory->details = $request->details;

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $subcategory->image = 'uploads/categories/' . $imageName;
            }

            $subcategory->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'SubCategory added successfully',
                    'route' => route('admin.subcategories', ['categoryId' => $categoryId])
                ], 200);
            }
        }

        return view('Admin.subcategory.add', compact('categoryId'));
    }

    protected function edit_subcategory(Request $request, $categoryId, $id)
    {
        $subcategory = SubCategory::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                // 'details' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'false',
                    'message' => $validator->errors()
                ], 422);
            }

            $subcategory->category_id = $categoryId;
            $subcategory->name = $request->name;
            $subcategory->details = $request->details;

            if ($request->hasFile('image')) {
                if ($subcategory->image && file_exists(public_path($subcategory->image))) {
                    unlink(public_path($subcategory->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $subcategory->image = 'uploads/categories/' . $imageName;
            }

            $subcategory->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'SubCategory updated successfully',
                    'route' => route('admin.subcategories', ['categoryId' => $categoryId, 'id' => $id]),
                ], 200);
            }
        }

        return view('Admin.subcategory.edit', compact('categoryId', 'subcategory'));
    }
    protected function delete_subcategory($categoryId, $id)
    {
        $subcategory = SubCategory::where('category_id', $categoryId)->find($id);

        if (!$subcategory) {
            return response()->json([
                'status' => 0,
                'message' => 'Subcategory not found!',
            ], 404);
        }

        if ($subcategory->image && file_exists(public_path($subcategory->image))) {
            unlink(public_path($subcategory->image));
        }

        $subcategory->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Subcategory deleted successfully!',
            'route' => route('admin.subcategories', ['categoryId' => $categoryId]),
        ], 200);
    }

    protected function subsubcategories($categoryId, $subcategoryId)
    {
        $Subsubcategories = SubSubCategory::where(['sub_subcategory_id' => $categoryId, 'subcategory_id' => $subcategoryId])->get();
        return view('Admin.subsubcategory.index', compact('Subsubcategories', 'categoryId', 'subcategoryId'));
    }
    protected function add_subsubcategory(Request $request, $categoryId, $subcategoryId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'sub_subcategory_name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                if ($request->json()) {
                    return response()->json([
                        'status' => 'false',
                        'message' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $Subsubcategory = new SubSubCategory();
            $Subsubcategory->sub_subcategory_id = $categoryId;
            $Subsubcategory->subcategory_id = $subcategoryId;
            $Subsubcategory->sub_subcategory_name = $request->sub_subcategory_name;

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $Subsubcategory->image = 'uploads/categories/' . $imageName;
            }

            $Subsubcategory->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Sub SubCategory added successfully',
                    'route' => route('admin.subsubcategories', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId])
                ], 200);
            }
        }

        return view('Admin.subsubcategory.add', compact('categoryId', 'subcategoryId'));
    }
    protected function edit_subsubcategory(Request $request, $categoryId, $subcategoryId, $id)
    {
        $Subsubcategory = SubSubCategory::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'sub_subcategory_name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'false',
                    'message' => $validator->errors()
                ], 422);
            }

            $Subsubcategory->sub_subcategory_id = $categoryId;
            $Subsubcategory->subcategory_id = $subcategoryId;
            $Subsubcategory->sub_subcategory_name = $request->sub_subcategory_name;

            if ($request->hasFile('image')) {
                if ($Subsubcategory->image && file_exists(public_path($Subsubcategory->image))) {
                    unlink(public_path($Subsubcategory->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $Subsubcategory->image = 'uploads/categories/' . $imageName;
            }

            $Subsubcategory->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Sub SubCategory updated successfully',
                    'route' => route('admin.subsubcategories', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId])
                ], 200);
            }
        }

        return view('Admin.subsubcategory.edit', compact('Subsubcategory', 'categoryId', 'subcategoryId'));
    }
    protected function delete_subsubcategory($categoryId, $subcategoryId, $id)
    {
        $Subsubcategory = SubSubCategory::where(['sub_subcategory_id' => $categoryId, 'subcategory_id' => $subcategoryId])->find($id);

        if (!$Subsubcategory) {
            return response()->json([
                'status' => 0,
                'message' => 'Sub Subcategory not found!',
            ], 404);
        }

        if ($Subsubcategory->image && file_exists(public_path($Subsubcategory->image))) {
            unlink(public_path($Subsubcategory->image));
        }

        $Subsubcategory->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Sub Subcategory deleted successfully!',
            'route' => route('admin.subsubcategories', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId])
        ], 200);
    }
    protected function services($categoryId, $subcategoryId, $subsubcategoryId)
    {
        $services = Service::where(['category_id' => $categoryId, 'subcategory_id' => $subcategoryId, 'sub_subcategory_id' => $subsubcategoryId])->get();
        return view('Admin.service.index', compact('services', 'categoryId', 'subcategoryId', 'subsubcategoryId'));
    }
    protected function add_service(Request $request, $categoryId, $subcategoryId, $subsubcategoryId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'service_name' => 'required|string|max:255',
                'price' => 'required|numeric|min:1',
                'time' => 'required|date_format:H:i',
                'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                'details' => 'required|array', // validate that details is an array
                'details.*' => 'nullable|string|max:255', // each detail should be a string
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $service = new Service();
            $service->category_id = $categoryId;
            $service->subcategory_id = $subcategoryId;
            $service->sub_subcategory_id = $subsubcategoryId;
            $service->service_name = $request->service_name;
            $service->price = $request->price;
            $service->time = $request->time;

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/services'), $imageName);
                $service->image = 'uploads/services/' . $imageName;
            }

            $filteredDetails = array_filter($request->details, function ($value) {
                return !is_null($value) && trim($value) !== '';
            });

            $service->details = json_encode(array_values($filteredDetails));
            $service->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Service added successfully',
                    'route' => route('admin.services', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId])
                ], 200);
            }
        }

        return view('Admin.service.add', compact('categoryId', 'subcategoryId', 'subsubcategoryId'));
    }
    protected function edit_service(Request $request, $categoryId, $subcategoryId, $subsubcategoryId, $id)
    {
        $service = Service::findOrFail($id);

        if ($request->isMethod('post')) {
           $validator = Validator::make($request->all(), [
                'service_name' => 'required|string|max:255',
                'price' => 'required|numeric|min:1',
                'time' => 'required',
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5120',
                'details' => 'sometimes|array',
                'details.*' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $service->category_id = $categoryId;
            $service->subcategory_id = $subcategoryId;
            $service->sub_subcategory_id = $subsubcategoryId;
            $service->service_name = $request->service_name;
            $service->price = $request->price;
            $service->time = $request->time;

            if ($request->hasFile('image')) {
                if ($service->image && file_exists(public_path($service->image))) {
                    unlink(public_path($service->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/services'), $imageName);
                $service->image = 'uploads/services/' . $imageName;
            }
            if ($request->has('details')) {
                $filteredDetails = array_filter($request->details, function ($value) {
                    return !is_null($value) && trim($value) !== '';
                });

                $service->details = json_encode(array_values($filteredDetails));
            }


            $service->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Service updated successfully',
                    'route' => route('admin.services', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId])
                ], 200);
            }
        }

        return view('Admin.service.edit', compact('service', 'categoryId', 'subcategoryId', 'subsubcategoryId'));
    }
    
    protected function delete_service($categoryId, $subcategoryId, $subsubcategoryId, $id)
    {
        $service = Service::where(['category_id' => $categoryId, 'subcategory_id' => $subcategoryId, 'sub_subcategory_id' => $subsubcategoryId])->find($id);

        if (!$service) {
            return response()->json([
                'status' => 0,
                'message' => 'Service not found!',
            ], 404);
        }

        if ($service->image && file_exists(public_path($service->image))) {
            unlink(public_path($service->image));
        }

        $service->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Service deleted successfully!',
            'route' => route('admin.services', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId])
        ], 200);
    }
    protected function how_it_works($categoryId, $subcategoryId, $subsubcategoryId, $serviceId)
    {
        $how_it_works = HowItWork::where('service_id', $serviceId)->orderByDesc('id')->get();
        return view('Admin.how_it_work.index', compact('how_it_works', 'categoryId', 'subcategoryId', 'subsubcategoryId', 'serviceId'));
    }
    protected function add_how_it_works(Request $request, $categoryId, $subcategoryId, $subsubcategoryId, $serviceId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $how_it_work = new HowItWork();
            $how_it_work->title = $request->title;
            $how_it_work->description = $request->description;
            $how_it_work->service_id = $serviceId;

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/how_it_work'), $imageName);
                $how_it_work->image = 'uploads/how_it_work/' . $imageName;
            }

            $how_it_work->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'How it work step added successfully',
                    'route' => route('admin.how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId])
                ], 200);
            }
        }

        return view('Admin.how_it_work.add', compact('categoryId', 'subcategoryId', 'subsubcategoryId', 'serviceId'));
    }
    protected function edit_how_it_works(Request $request, $categoryId, $subcategoryId, $subsubcategoryId, $serviceId, $id)
    {
        $how_it_work = HowItWork::findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $how_it_work->title = $request->title;
            $how_it_work->description = $request->description;

            if ($request->hasFile('image')) {
                if ($how_it_work->image && file_exists(public_path($how_it_work->image))) {
                    unlink(public_path($how_it_work->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/how_it_work'), $imageName);
                $how_it_work->image = 'uploads/how_it_work/' . $imageName;
            }

            $how_it_work->save();

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'How it work step updated successfully',
                    'route' => route('admin.how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId])
                ], 200);
            }
        }

        return view('Admin.how_it_work.edit', compact('how_it_work', 'categoryId', 'subcategoryId', 'subsubcategoryId', 'serviceId'));
    }
    protected function delete_how_it_works($categoryId, $subcategoryId, $subsubcategoryId, $serviceId, $id)
    {
        $how_it_work = HowItWork::where('id', $id)->find($id);

        if (!$how_it_work) {
            return response()->json([
                'status' => 0,
                'message' => 'How it work not found!',
            ], 404);
        }

        if ($how_it_work->image && file_exists(public_path($how_it_work->image))) {
            unlink(public_path($how_it_work->image));
        }

        $how_it_work->delete();

        return response()->json([
            'status' => 1,
            'message' => 'How it work deleted successfully!',
            'route' => route('admin.how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId])
        ], 200);
    }

    /** ============================
     * ✅Contory data fetch on cities  Functionality 
     * ============================ */
    protected function countries()
    {
        $data = Country::all();
        return view('Admin.country.index', compact('data'));
    }

    /** ============================
     * ✅Users  Functionality 
     * ============================ */
    protected function users()
    {
        $users = User::where('role', 1)->orderBy('id', 'DESC')->get();
        return view('Admin.users.index', compact('users'));
    }

    protected function add_users(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
                'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
                'address' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = new User();
            $user->name      = $request->name;
            $user->mobile_no = $request->mobile_no;
            $user->address = $request->address;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->role      = 1;
            $user->save();

            return response()->json([
                'status' => 1,
                'message' => 'Data Added successfully',
                'route' => route('admin.users'),
            ], 200);
        } else {
            return view('Admin.users.add');
        }
    }

    protected function edit_users(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
                'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);

            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $user->save();

            return response()->json([
                'status' => 1,
                'message' => 'Data Edit successfully',
                'route' => route('admin.users'),
            ], 200);
        } else {
            $data = User::findOrFail($id);
            return view('Admin.users.edit', compact('data'));
        }
    }

    protected function delete_users($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Data Delete successfully',
            'route' => route('admin.users'),
        ], 200);
    }

    protected function bookings($userId)
    {
        $bookings = Order::with(['provider:id,name,mobile_no'])->where('user_id', $userId)->orderBy('id', 'DESC')->get();
        return view('Admin.bookings.index', compact('bookings', 'userId'));
    }
    protected function addresses($userId)
    {
        $addresses = Address::where('user_id', $userId)->orderBy('id', 'DESC')->get();
        return view('Admin.addresses.index', compact('addresses', 'userId'));
    }
    protected function add_address(Request $request, $userId)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'address' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'flat_no' => 'required',
                'landmark' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            Address::create([
                'type' => 1,
                'user_id' => $userId,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'flat_no' => $request->flat_no,
                'landmark' => $request->landmark
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Address Added successfully',
                'route' => route('admin.addresses', ['userId' => $userId]),
            ], 200);
        } else {
            return view('Admin.addresses.add', compact('userId'));
        }
    }
    
    // public function getProviders(Request $request)
    // {
    //     try {
    //         $zoneId = $request->input('zone_id');

    //         // Fetch providers for the given zone
    //         $providers = ZoneProvider::where('zone_id', $zoneId)
    //             ->leftJoin('users', 'zone_provider.user_id', '=', 'users.id')
    //             ->select('zone_provider.user_id', 'users.name', 'users.mobile_no')
    //             ->get();

    //         // Show 'Unknown Provider' if the user is missing
    //         $providers = $providers->map(function ($provider) {
    //             $provider->name = $provider->name ?? 'Unknown Provider';
    //             $provider->mobile_no = $provider->mobile_no ?? 'Unknown Mobile Nomber';
    //             return $provider;
    //         });

    //         return response()->json($providers);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
    
    public function getProviders(Request $request)
    {
        try {
            $categoryId = $request->input('category_id');
            $zoneId     = $request->input('zone_id');

            if (!$categoryId || !$zoneId) {
                return response()->json(['error' => 'Both category_id and zone_id are required'], 400);
            }

            // Fetch providers by category and zone
            $providers = ZoneProvider::where('zone_provider.zone_id', $zoneId)
                ->join('users', 'zone_provider.user_id', '=', 'users.id')
                ->where('users.role', 2) // only providers
                ->where('users.category_id', $categoryId) // filter by category from users table
                ->select('zone_provider.user_id', 'users.name', 'users.mobile_no')
                ->get();


            // Show 'Unknown Provider' if user details missing
            $providers = $providers->map(function ($provider) {
                $provider->name = $provider->name ?? 'Unknown Provider';
                $provider->mobile_no = $provider->mobile_no ?? 'Unknown Mobile Number';
                return $provider;
            });

            return response()->json($providers);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    protected function create_booking(Request $request, $userId)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'subcategory_id' => 'required|exists:sub_categories,id',
                'slot_date' => 'required|date',
                'slot_time' => 'required',
                'services' => 'required|array',
                'services.*' => 'exists:services,id',
                'address_id' => 'required|exists:addresses,id',
                'provider_id' => 'nullable|exists:users,id',
                'zone_id' => 'required|exists:zones,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $slotTime = explode('-', $request->slot_time);
            $slotStartTime = $slotTime[0];
            $slotEndTime = $slotTime[1];

            $totalPrice = Service::whereIn('id', $request->services)->sum('price');
            // $bookingId = 'BOOK-' . strtoupper(Str::random(8));

            $assignedProviderId = $request->provider_id;

            // $isAdminCreated = $assignedProviderId ? true : false;

            $order = Order::create([
                'user_id' => $userId,
                'subcategory_id' => $request->subcategory_id,
                'address_id' => $request->address_id,
                'zone_id'    => $request->zone_id,
                'total_price' => $totalPrice,
                'payment_method' => 'cod',
                // 'booking_id' => $bookingId,
                'slot_date' => $request->slot_date,
                'slot_start_time' => $slotStartTime,
                'slot_end_time' => $slotEndTime,
                'status' => $request->provider_id ? 'accepted' : 'placed',
                'provider_id' => $request->provider_id,
                'is_admin_created' => true
            ]);

            $bookingId = 'BOOK-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->update(['booking_id' => $bookingId]);

            foreach ($request->services as $serviceId) {
                $service = Service::find($serviceId);
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'unit_price' => $service->price,
                    'total_price' => $service->price
                ]);
            }
            $title = 'New Booking Received!';
            $message = "You have received a new booking (ID: {$bookingId}). Total Amount: ₹{$totalPrice}.";

            if ($assignedProviderId) {

                Notification::create([
                    'user_id' => $assignedProviderId,
                    'title'   => $title,
                    'message' => $message
                ]);

                $serviceProvider = User::where('id', $assignedProviderId)->where('role', 2)->first();
                if ($serviceProvider && $serviceProvider->device_token) {
                    $this->notificationService->sendPushNotification([$serviceProvider->device_token], $title, $message);
                }
            } else {

                $zoneProviderIds = ZoneProvider::where('zone_id', $request->zone_id)->pluck('user_id');

                if ($zoneProviderIds->isNotEmpty()) {
                    foreach ($zoneProviderIds as $providerId) {
                        Notification::create([
                            'user_id' => $providerId,
                            'title'   => $title,
                            'message' => $message
                        ]);
                    }

                    $serviceProviders = User::whereIn('id', $zoneProviderIds)
                        ->where('role', 2)
                        ->whereNotNull('device_token')
                        ->pluck('device_token')
                        ->toArray();

                    if (!empty($serviceProviders)) {
                        $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
                    }
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Booking Create Successfully',
                'route' => route('admin.bookings', ['userId' => $userId]),
            ], 200);
        } else {
            $categories = Category::all();
            $addresses = Address::where('user_id', $userId)->orderBy('id', 'DESC')->get();
            $zones = Zone::OrderBy('id', 'DESC')->get();
            return view('Admin.bookings.add', compact('userId', 'categories', 'addresses', 'zones'));
        }
    }
    protected function isPointInPolygon($latitude, $longitude, $polygon)
    {
        if (!is_array($polygon) || count($polygon) < 3) {
            throw new \Exception("Invalid polygon data: Polygon must have at least 3 points.");
        }

        foreach ($polygon as $index => $point) {
            if (!isset($point['lng']) || !isset($point['lat'])) {
                throw new \Exception("Invalid polygon data at index $index: Missing lat or lng.");
            }
        }

        $inside = false;
        $x = (float)$longitude;
        $y = (float)$latitude;
        $numPoints = count($polygon);
        $j = $numPoints - 1;

        for ($i = 0; $i < $numPoints; $j = $i++) {
            $xi = (float)$polygon[$i]['lng'];
            $yi = (float)$polygon[$i]['lat'];
            $xj = (float)$polygon[$j]['lng'];
            $yj = (float)$polygon[$j]['lat'];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }


    public function getSubcategories($categoryId)
    {
        $subcategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json($subcategories);
    }

    // Fetch Sub Subcategories
    public function getSubSubcategories($categoryId, $subcategoryId)
    {
        $subSubcategories = SubSubCategory::where('sub_subcategory_id', $categoryId)
            ->where('subcategory_id', $subcategoryId)
            ->get();
        return response()->json($subSubcategories);
    }

    // Fetch Services
    public function getServices($categoryId, $subcategoryId, $subSubcategoryId)
    {
        $services = Service::where('category_id', $categoryId)
            ->where('subcategory_id', $subcategoryId)
            ->where('sub_subcategory_id', $subSubcategoryId)
            ->get();
        return response()->json($services);
    }
    // Get available slots
    public function getDailySlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = Carbon::parse($request->date)->setTimezone('Asia/Kolkata');
        $startTime = $date->copy()->setTime(9, 0); // Start at 9 AM
        $endTime = $date->copy()->setTime(19, 0);  // End at 6 PM
        $interval = 15; // 15-minute slots
        $slots = [];

        $now = Carbon::now('Asia/Kolkata');

        while ($startTime < $endTime) {
            $nextSlot = $startTime->copy()->addMinutes($interval);

            // Skip past slots if date is today
            if ($date->isToday() && $startTime->lessThan($now)) {
                $startTime = $nextSlot;
                continue;
            }

            $slots[] = [
                'slot' => $startTime->format('g:i A') . ' - ' . $nextSlot->format('g:i A'),
                'start_time' => $startTime->format('H:i'),
                'end_time' => $nextSlot->format('H:i'),
                'is_available' => true
            ];

            $startTime = $nextSlot;
        }

        return response()->json(['slots' => $slots]);
    }

    /** ============================
     * ✅Providers Functionality 
     * ============================ */
    public function getZones($providerId)
    {
        $zones = Zone::all();
        $assignedZoneIds = $zones->where('providers', '!=', null)->pluck('id')->toArray();
        $assignedZones = Zone::whereHas('providers', function ($query) use ($providerId) {
            $query->where('user_id', $providerId);
        })->pluck('id')->toArray();

        return response()->json([
            'zones' => $zones,
            'assignedZones' => $assignedZones,
        ]);
    }

    public function assignZones(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',
            'zones' => 'nullable|array',
            'zones.*' => 'exists:zones,id',
        ]);

        $provider = User::findOrFail($request->provider_id);
        $provider->zones()->sync($request->zones);

        return response()->json(['message' => 'Zones assigned successfully!']);
    }
    protected function user_details($id)
    {
        $details = User::with([
            'country:id,name',
            'state:id,name',
            'city:id,name',
            'category:id,category',
            'identityType:id,identity',
            'zones:id,name'
        ])->select('id', 'name', 'mobile_no', 'email', 'temp_password', 'country_id', 'state_id', 'city_id', 'pincode', 'address', 'device_type', 'device_model', 'login_at', 'logout_at', 'category_id', 'experience', 'identity_id', 'identity_number', 'identity_image', 'identity_image_back', 'created_at')->find($id);

        $securityPlan = Subscription::getActiveSubscription($id, 2);
        $mainPlan = Subscription::getActiveSubscription($id, 1);
        return view('Admin.users.details', compact('details', 'securityPlan', 'mainPlan'));
    }
    protected function user_block($id, Request $request)
    {
        $provider = User::find($id);

        if ($provider) {
            $provider->is_blocked = !$provider->is_blocked;
            $provider->save();

            if ($provider->is_blocked) {
                $provider->tokens()->delete();
            }

            return response()->json([
                'status' => $provider->is_blocked ? 'blocked' : 'unblocked'
            ]);
        }

        return response()->json(['status' => 'error'], 400);
    }
    protected function providers(Request $request)
    {
        //       $providers=User::where('role',2)->orderBy('id','DESC')->get();
        //       $providerStats = [];

        //     foreach ($providers as $provider) {
        //         // $totalLeads = Order::where('provider_id', $provider->id)->count();
        //         $acceptedLeads = Order::where('provider_id', $provider->id)->where('status', 'accepted')->count();
        //         $completedLeads = Order::where('provider_id', $provider->id)->where('status', 'completed')->count();

        //         $skippedLeads = OrderAssignmentAttempt::where('provider_id', $provider->id)
        //                             ->where('status', 'skipped')->count();
        // $totalLeads = $completedLeads+$acceptedLeads+$skippedLeads;
        //         $providerStats[$provider->id] = [
        //             'total' => $totalLeads,
        //             'accepted' => $acceptedLeads,
        //             'completed' => $completedLeads,
        //             'skipped' => $skippedLeads,
        //         ];
        //     }
        //         return view('Admin.providers.index', compact('providers','providerStats'));
        $query = User::where('role', 2);

        // Filter by category (for both normal and AJAX)
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $providers = $query->orderBy('id', 'DESC')->get();
        $providerStats = [];

        foreach ($providers as $provider) {
            $acceptedLeads = Order::where('provider_id', $provider->id)
                ->where('status', 'accepted')->count();

            $completedLeads = Order::where('provider_id', $provider->id)
                ->where('status', 'completed')->count();

            $skippedLeads = OrderAssignmentAttempt::where('provider_id', $provider->id)
                ->where('status', 'skipped')->count();

            $totalLeads = $completedLeads + $acceptedLeads + $skippedLeads;

            $providerStats[$provider->id] = [
                'total' => $totalLeads,
                'accepted' => $acceptedLeads,
                'completed' => $completedLeads,
                'skipped' => $skippedLeads,
            ];
        }

        // If it's AJAX request, return only the partial table view
        if ($request->ajax()) {
            return view('Admin.providers.partials.table', compact('providers', 'providerStats'))->render();
        }

        // Else return full page view
        $categories = Category::all();
        return view('Admin.providers.index', compact('providers', 'providerStats', 'categories'));
    }
    protected function getPlans($userId, $type)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $categoryId = $user->category_id;
        $plans = Plan::where('type', $type)->where('category_id', $categoryId)->get();
        return response()->json(['plans' => $plans]);
    }
    protected function activateSecurity(Request $request,$userId, $planId)
    {
        $plan = Plan::find($planId);
        if (!$plan) {
            return response()->json(['status' => 'error', 'message' => 'Invalid plan selected!']);
        }

        if (Subscription::hasActiveSubscription($userId, $plan->type)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Plan is already active!',
            ]);
        }

if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');

            if (!$file->isValid()) {
                return response()->json(['status' => 'error', 'message' => 'Invalid screenshot file.']);
            }

            $filename = 'screenshot_' . time() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/screenshots');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $screenshotPath = 'uploads/screenshots/' . $filename;
        } else {
            return response()->json(['status' => 'error', 'message' => 'Screenshot is required.']);
        }

        $startDate = now();
        $endDate = now()->addDays($plan->duration);
        $subscriptionStatus = 'active';

        $newSubscription = Subscription::updateOrCreate(
            ['user_id' => $userId, 'type' => $plan->type],
            [
                'plan_id'   => $planId,
                'status'    => $subscriptionStatus,
                'start_date' => $startDate,
                'end_date'  => $endDate,
            ]
        );

        $transactionId = 'TXN-Admin-' . now()->timestamp;

        Transaction::create([
            'type'            => $plan->type,
            'user_id'         => $userId,
            'transaction'     => 2, // Debit Transaction
            'amount'          => $plan->price,
            'transaction_id'  => $transactionId,
            'subscription_id' => $newSubscription->id,
            'status'          => 'success',
            'screenshot' => $screenshotPath,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Security plan activated successfully!']);
    }
    protected function add_providers(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
                'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
                'email' => 'required|email|unique:users,email',
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'required|string|min:6',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'pincode' => 'required|digits:6',
                'address' => 'required|string|max:500',
                'category_id' => 'required|exists:categories,id',
                'experience' => 'required|integer|min:0',
                'identity_id' => 'required|integer',
                'identity_number' => 'required|string|max:50',
                'identity_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = new User();
            $user->name = $request->name;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $user->role = 2;
            $user->password = Hash::make($request->password);
            $user->country_id = $request->country_id;
            $user->state_id = $request->state_id;
            $user->city_id = $request->city_id;
            $user->pincode = $request->pincode;
            $user->address = $request->address;
            $user->category_id = $request->category_id;
            $user->experience = $request->experience;
            $user->identity_id = $request->identity_id;
            $user->identity_number = $request->identity_number;
            $user->identity_image = $request->identity_image;

            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $filename = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profiles/'), $filename);
                $user->profile = $filename;
            }
            if ($request->hasFile('identity_image')) {
                $file = $request->file('identity_image');
                $filename = 'identity_image' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/identities/'), $filename);
                $user->identity_image = $filename;
            }
            $user->save();
            return response()->json([
                'status' => 1,
                'message' => 'Provider Added Successfully',
                'route' => route('admin.providers'),
            ], 200);
        }
        $countries = Country::all();
        $categories = Category::all();
        $identities = IdentityType::all();

        return view('Admin.providers.add', compact('countries', 'categories', 'identities'));
    }
    protected function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get();
        return response()->json($states);
    }

    protected function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->get();
        if ($cities->isEmpty()) {
            return response()->json(['message' => 'Cities not found'], 404);
        }
        return response()->json($cities);
    }


    protected function edit_providers(Request $request, $id)
    {
        $provider = User::findOrFail($id);

        if ($request->isMethod('post')) {
            try {
                $request->validate([
                    'mobile_no' => 'numeric|digits:10|unique:users,mobile_no',
                    'email' => 'email|unique:users,email,' . $id,
                    'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'password' => 'nullable|string|min:6',
                    'country_id' => 'required|integer',
                    'state_id' => 'required|integer',
                    'pincode' => 'required|digits:6',
                    'address' => 'required|string|max:500',
                    'category_id' => 'required|integer',
                    'experience' => 'required|integer|min:0',
                    'identity_id' => 'required|integer',
                    'identity_number' => 'required|string|max:50',
                    'identity_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $provider->name = $request->name;
                // $provider->mobile_no = $request->mobile_no;
                // $provider->email = $request->email;

               if ($request->hasFile('profile')) {
                    $file = $request->file('profile');
                    $filename = 'profile_' . time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/profiles'), $filename);
                    $provider->profile = $filename;
                }


                if ($request->hasFile('identity_image')) {
                    $file = $request->file('identity_image');
                    $fileName = 'identity_image_' . time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/identities'), $fileName);
                    $provider->identity_image = $fileName;
                }
                if ($request->password) {
                    $provider->password = Hash::make($request->password);
                }
                $provider->country_id = $request->country_id;
                $provider->state_id = $request->state_id;
                $provider->pincode = $request->pincode;
                $provider->address = $request->address;
                $provider->category_id = $request->category_id;
                $provider->experience = $request->experience;
                $provider->identity_id = $request->identity_id;
                $provider->identity_number = $request->identity_number;
                $provider->identity_image = $request->identity_image;
                $provider->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Provider updated successfully',
                    'route' => route('admin.providers'),
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                        'status' => 'error',
                        'message' => 'Something went wrong! ' . $e->getMessage(),
                    ], 500);
            }
        }

        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $categories = Category::all();
        $identities = IdentityType::all();

        return view('Admin.providers.edit', compact('provider', 'countries', 'states', 'cities', 'categories', 'identities'));
    }

    protected function delete_providers($id)
    {
        
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Data Delete successfully',
            'route' => route('admin.providers'),
        ], 200);
    }
    /** ============================
     * ✅sub subCategory Functionality 
     * ============================ */
    protected function subSubCategory($subcategory_id)

    {
        $subcategory = SubCategory::find($subcategory_id);

        if (!$subcategory) {
            return redirect()->back()->with('error', 'Subcategory not found!');
        }

        $subSubCategories = SubSubCategory::with('subcategory.category')
            ->where('subcategory_id', $subcategory->id)
            ->get();

        return view('Admin.subsubcategory.index', [
            'subcategory_id' => $subcategory_id,
            'subcategory_name' => $subcategory->subcategory_name,
            'subSubCategories' => $subSubCategories
        ]);
    }

    protected function addSubSubCategory(Request $request, $subcategory_id)
    {
        $subcategory = Subcategory::find($subcategory_id);

        if (!$subcategory) {
            return redirect()->back()->with('error', 'Subcategory not found!');
        }

        if ($request->isMethod('get')) {
            $subSubCategories = SubSubCategory::where('subcategory_id', $subcategory->id)->get();

            return view('Admin.subsubcategory.add', compact('subcategory', 'subSubCategories', 'subcategory_id'));
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'sub_subcategory_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'image_' . time() . '-' . $file->getClientOriginalName();
            $filePath = public_path('uploads/categories');
            $file->move($filePath, $fileName);
            $imagePath = 'uploads/categories/' . $fileName;
        }

        // Data Insert
        SubSubCategory::create([
            'subcategory_id' => $subcategory_id,
            'sub_subcategory_id' => $subcategory_id,
            'sub_subcategory_name' => $request->sub_subcategory_name,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.subSubCategories', ['subcategory_id' => $subcategory->id]);
    }

    protected function editSubSubCategory(Request $request, $subcategory_id, $id)
    {

        $subSubCategory = SubSubCategory::find($id);
        if (!$subSubCategory) {
            return redirect()->back()->with('error', 'Sub-Sub Category not found.');
        }

        if ($request->isMethod('get')) {
            $subcategory = SubCategory::find($subcategory_id);

            return view('Admin.subsubcategory.edit', [
                'subSubCategory' => $subSubCategory,
                'subcategory_id' => $subcategory_id
            ]);
        }


        if ($request->isMethod('post')) {
            $subcategory = SubCategory::find($subcategory_id);
            $subSubCategory = SubSubCategory::find($id);

            if (!$subSubCategory) {
                return redirect()->back()->with('error', 'Sub-Sub Category not found.');
            }


            $request->validate([
                'sub_subcategory_name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            ]);


            if ($request->hasFile('image')) {

                if ($subSubCategory->image && File::exists(public_path($subSubCategory->image))) {
                    File::delete(public_path($subSubCategory->image));
                }

                $file = $request->file('image');
                $fileName = 'image_' . time() . '-' . $file->getClientOriginalName();
                $filePath = public_path('uploads/categories');
                $file->move($filePath, $fileName);
                $subSubCategory->image = 'uploads/categories/' . $fileName;
            }
            $subSubCategory->sub_subcategory_name = $request->sub_subcategory_name;
            $subSubCategory->save();

            return redirect()->route('admin.subSubCategories', ['subcategory_id' => $subcategory->id]);
        }
        return redirect()->back()->with('error', 'Invalid request method.');
    }



    protected function deleteSubSubCategory($subcategory_id, $id)
    {
        $subSubCategory = SubSubCategory::where('subcategory_id', $subcategory_id)->where('id', $id)->first();

        if (!$subSubCategory) {
            return response()->json([
                'status' => 1,
                'message' => 'Sub-Sub Category not found!'
            ], 404);
        }

        if ($subSubCategory->image && file_exists(public_path($subSubCategory->image))) {
            unlink(public_path($subSubCategory->image));
        }

        $subSubCategory->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Sub-Sub Category deleted successfully!',
            'route' => route('admin.subSubCategories', compact('subcategory_id', 'id'))
        ]);
    }



    /** ============================
     * ✅ServiceList Functionality 
     * ============================ */

    protected function service($category_id, $subcategory_id, $id)
    {
        $services = Service::where('category_id', $category_id)
            ->where('subcategory_id', $subcategory_id)
            ->where('sub_subcategory_id', $id)
            ->get();

        return view('Admin.service.index', compact('services', 'category_id', 'subcategory_id', 'id'));
    }


    // Add Service
    protected function addService(Request $request, $category_id, $subcategory_id, $sub_subcategory_id)
    {
        if ($request->isMethod('get')) {
            return view('Admin.service.add', [
                'category_id' => $category_id,
                'subcategory_id' => $subcategory_id,
                'sub_subcategory_id' => $sub_subcategory_id
            ]);
        }

        $request->validate([
            'service_name' => 'required|string|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price'        => 'required|numeric',
            'time'         => 'required|string|max:255'
        ]);

        $service = new Service();
        $service->category_id    = $category_id;
        $service->subcategory_id = $subcategory_id;
        $service->sub_subcategory_id = $sub_subcategory_id;
        $service->service_name   = $request->service_name;
        $service->price          = $request->price;
        $service->time           = $request->time;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'image_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = public_path('uploads/services');
            $file->move($filePath, $fileName);
            $service->image = 'uploads/services/' . $fileName;
        }

        $service->save();

        return redirect()->route('admin.service', ['category_id' => $category_id, 'subcategory_id' => $subcategory_id, 'id' => $sub_subcategory_id])->with('success', 'Service Added Successfully!');
    }

    // Edit Service
    protected function editService(Request $request, $category_id, $subcategory_id, $sub_subcategory_id, $service_id)
    {

        $subsubcategory = Service::find($service_id);

        if (!$subsubcategory) {
            return response()->json([
                'status' => 0,
                'message' => 'Service Not Found!'
            ]);
        }

        if ($request->isMethod('get')) {
            return view('Admin.service.edit', compact('subsubcategory', 'category_id', 'subcategory_id', 'service_id'));
        }

        $request->validate([
            'service_name' => 'required|string|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price'        => 'required|numeric',
            'time'         => 'required'
        ]);

        $subsubcategory->service_name = $request->service_name;
        $subsubcategory->price        = $request->price;
        $subsubcategory->time         = $request->time;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'image_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = public_path('uploads/services');

            if ($subsubcategory->image && File::exists(public_path($subsubcategory->image))) {
                File::delete(public_path($subsubcategory->image));
            }

            $file->move($filePath, $fileName);
            $subsubcategory->image = 'uploads/services/' . $fileName;
        }
        $subsubcategory->save();


        return redirect()->route('admin.service', [
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id,
            'id' => $sub_subcategory_id
        ])->with('success', 'Service Updated Successfully!');
    }


    protected function deleteService($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 0,
                'message' => 'Service Not Found!'
            ]);
        }

        // Purani Image Delete
        if ($service->image && file_exists(public_path($service->image))) {
            unlink(public_path($service->image));
        }

        $service->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Service deleted successfully!',
            'route' => route('admin.service', [
                'category_id' => $service->category_id,
                'subcategory_id' => $service->subcategory_id,
                'id' => $service->id
            ])
        ]);
    }


    /** ============================
     * ✅Transaction Functionality 
     * ============================ */

    protected function transaction()
    {
        $transactions = Transaction::with(['user:id,name,mobile_no'])->OrderBy('id', 'DESC')->get();
        return view('Admin.transaction.index', compact('transactions'));
    }

    protected function TransProvider()
    {
        $providers = User::with(['subscriptions.plan'])
            ->where('role', 2)
            ->get();

        return view('Admin.transaction.transProvider', compact('providers'));
    }
    protected function zone()
    {
        $data = Zone::with('providers')->OrderBy('id', 'DESC')->get();
        $providers = User::where('role', 2)->select('id', 'name', 'mobile_no','email','role')->get();
        return view('Admin.zone.index', compact('data', 'providers'));
    }
    protected function add_zone(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name'       => 'required|regex:/^[A-Za-z\s]+$/|max:255',
                'boundary'   => 'required|json',
                'center_lat' => 'required|numeric|between:-90,90',
                'center_lng' => 'required|numeric|between:-180,180',
                'perimeter'  => 'required|numeric|min:0',
                'area'       => 'required|numeric|min:0',
                'areas'      => 'required|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            // Create new Zone
            $zone = new Zone();
            $zone->name       = $request->name;
            $zone->boundary   = $request->boundary;
            $zone->center_lat = $request->center_lat;
            $zone->center_lng = $request->center_lng;
            $zone->perimeter  = $request->perimeter;
            $zone->area       = $request->area;
            $zone->areas      = $request->areas;
            $zone->save();

            return response()->json([
                'status' => 1,
                'message' => 'Zone added successfully!',
                'route' => route('admin.zones'),
            ], 200);
        }
        return view('Admin.zone.add');
    }
    protected function edit_zone($id)
    {
        $zone = Zone::findOrFail($id);
        return view('Admin.zone.edit', compact('zone'));
    }
    protected function update_zone(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|regex:/^[A-Za-z\s]+$/|max:255',
            'boundary'   => 'required|json',
            'center_lat' => 'required|numeric|between:-90,90',
            'center_lng' => 'required|numeric|between:-180,180',
            'perimeter'  => 'required|numeric|min:0',
            'area'       => 'required|numeric|min:0',
            'areas'      => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ], 422);
        }

        $zone = Zone::findOrFail($id);
        $zone->name       = $request->name;
        $zone->boundary   = $request->boundary;
        $zone->center_lat = $request->center_lat;
        $zone->center_lng = $request->center_lng;
        $zone->perimeter  = $request->perimeter;
        $zone->area       = $request->area;
        $zone->areas      = $request->areas;
        $zone->save();

        return redirect()->route('admin.zones')->with('success', 'Zone updated successfully!');

    }

    protected function get_providers($zone_id)
    {
        $zone = Zone::with('providers')->find($zone_id); // Eager load providers
        $allProviders = User::where('role', 2)->get(); // Ya jo bhi condition ho

        $assignedProviders = $zone->providers->pluck('id')->toArray(); // Assigned providers' IDs

        return response()->json([
            'providers' => $allProviders,
            'assignedProviders' => $assignedProviders,
        ]);
    }

    protected function assign_provider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|exists:zones,id',
            'providers' => 'required|array',
            'providers.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ], 422);
        }
        $zone = Zone::find($request->zone_id);
        $zone->providers()->sync($request->providers ?? []);
        return response()->json([
            'status' => 1,
            'message' => 'Providers assigned successfully!',
            'route' => route('admin.zones'),
        ], 200);
    }

    protected function all_bookings(Request $request)
    {
        $query = Order::with(['subcategory.category', 'zone', 'user', 'provider'])->orderBy('id', 'DESC');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $bookings = $query->get();
        $zones = Zone::all();
        $categories = Category::all();

        return view('Admin.all-bookings.index', compact('bookings', 'zones', 'categories'));
    }
    
     public function changeStatus(Request $request)
    {
        $service = Order::find($request->id);
            //dd($service);
        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found!'
            ], 404);
        }

        // Only allow specific statuses
        $allowedStatuses = ['pending', 'placed', 'accepted', 'completed','survey done'];

        if (!in_array($request->status, $allowedStatuses)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid status value!'
            ], 400);
        }

        $service->status = $request->status;
        $service->save();

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully!',
            'data' => $service
        ]);
    }


    public function updateField(Request $request)
    {
        $booking = Order::find($request->id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $field = $request->field;
        $value = $request->value;

        if (!in_array($field, ['xtotal_amount', 'xcommission_amount', 'xstatus', 'xdescription'])) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $booking->$field = $value;
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function storeFromBooking(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'provider_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string',
        ]);

        $existing = Complaint::where('user_id', $request->user_id)
            ->where('order_id', $request->order_id)
            ->where('status', '!=', 'resolved')
            ->first();

        if ($existing) {
            return back()->with('error', 'Complaint already exists for this order and is not resolved yet.');
        }

        $complaint = Complaint::create([
            'user_id' => $request->user_id,
            'provider_id' => $request->provider_id,
            'order_id' => $request->order_id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        $provider = User::find($request->provider_id);
        $title = 'New Complaint Received';
        $message = "A new complaint has been submitted for Booking ID: {$complaint->order->booking_id}.";

        if ($provider && $provider->device_token) {
            $this->notificationService->sendPushNotification([$provider->device_token], $title, $message);
        }

        return back()->with('success', 'Complaint submitted successfully!');
    }
    protected function exportCsv(): StreamedResponse
    {
        $bookings = Order::with(['user', 'provider', 'zone'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=bookings.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr No',
            'Zone',
            'By',
            'User',
            'Provider',
            'Booking Date',
            'Booking ID',
            'Price',
            'Status',
            'Slot Date',
            'Slot Start',
            'Slot End'
        ];

        // echo("Yaha pe aata he");
        $callback = function () use ($bookings, $columns) {
            // echo("Yaha par nahi aa raha he");
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // dd(987);

            foreach ($bookings as $index => $val) {
                fputcsv($file, [
                    $index + 1,
                    $val->zone->name ?? 'N/A',
                    $val->is_admin_created ? 'Admin' : 'App',
                    optional($val->user)->mobile_no
                        ? optional($val->user)->mobile_no . ' (' . optional($val->user)->name . ')'
                        : 'N/A',
                    optional($val->provider)->mobile_no
                        ? optional($val->provider)->mobile_no . ' (' . optional($val->provider)->name . ')'
                        : 'N/A',
                    $val->created_at->format('d M, Y - h:i A'),
                    $val->booking_id,
                    $val->total_price,
                    $val->status,
                    $val->slot_date,
                    \Carbon\Carbon::createFromFormat('H:i:s', $val->slot_start_time)->format('g:i A'),
                    \Carbon\Carbon::createFromFormat('H:i:s', $val->slot_end_time)->format('g:i A'),
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'bookings.csv', $headers);
    }
    
    protected function assignZoneToBooking(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'zone_id' => 'required|exists:zones,id'
        ]);
        $order = Order::find($request->order_id);
        $order->zone_id = $request->zone_id;
        $order->save();

        $title = 'New Booking Received!';
        $message = "You have received a new booking (ID: {$order->booking_id}). Total Amount: ₹{$order->total_price}.";

        $zoneProviderIds = ZoneProvider::where('zone_id', $request->zone_id)->pluck('user_id');

        if ($zoneProviderIds->isNotEmpty()) {
            foreach ($zoneProviderIds as $providerId) {
                Notification::create([
                    'user_id' => $providerId,
                    'title'   => $title,
                    'message' => $message
                ]);
            }

            $serviceProviders = User::whereIn('id', $zoneProviderIds)
                ->where('role', 2)
                ->whereNotNull('device_token')
                ->pluck('device_token')
                ->toArray();

            if (!empty($serviceProviders)) {
                $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
            }
        }

        return redirect()->back()->with('success', 'Zone assigned to booking successfully!');
    }

    protected function booking_detail($id)
    {
        $booking = Order::with([
            'user:id,name,mobile_no',
            'subCategory:id,name',
            'address:id,address,latitude,longitude,flat_no,landmark',
            'orderItems:id,order_id,service_id,quantity,unit_price,total_price',
            'orderItems.service:id,service_name,image'
        ])
            ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'slot_date', 'slot_start_time', 'slot_end_time', 'address_id', 'total_price', 'payment_method', 'status', 'status', 'created_at')
            ->where('id', $id)
            ->first();

        return view('Admin.all-bookings.booking_details', compact('booking'));
    }
    protected function complaints()
    {
        $complaints = Complaint::with(['user', 'provider', 'order'])->latest()->get();
        return view('Admin.complaints.index', compact('complaints'));
    }
    protected function resolve(Complaint $complaint)
    {
        $complaint->status = 'resolved';
        $complaint->save();

        return redirect()->back()->with('success', 'Complaint status updated to resolved.');
    }
    protected function booking_cancel(Request $request)
    {
        $order = Order::find($request->id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.']);
        }
        CancelledOrder::create([
            'order_id' => $order->id,
            'provider_id' => $order->provider_id,
            'reason' => 'Cancelled by admin',
            'subscription_id' => $order->subscription_id,
        ]);

        $order->status = 'placed';
        $order->provider_id = NULL;
        $order->save();

        return response()->json(['success' => true]);
    }
    protected function partners_data(Request $request)
    {
       $query = Partner::with('categoryRelation');

        if ($request->ajax()) {
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $partners = $query->get();
            $view = view('Admin.partners.partials.table', compact('partners'))->render();

            return response()->json(['html' => $view]);
        }

        $partners = $query->get();
        $categories = Category::all();
        return view('Admin.partners.index', compact('partners', 'categories'));
    }
    protected function add_partners_data(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:xlsx,xls,csv',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            Excel::import(new PartnerImport($request->category_id), $request->file('file'));

            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Partners imported successfully!',
                    'route' => route('admin.partners_data')
                ], 200);
            }
        }
        $categories = Category::all();
        return view('Admin.partners.add', compact('categories'));
    }
    public function updatePartnerStatus(Request $request)
    {
        $partner = Partner::findOrFail($request->id);
        $partner->status = $request->status;
        $partner->save();

        return response()->json(['message' => 'Status updated successfully.']);
    }
    
    public function punchIn(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $today = now()->toDateString();

        $existing = Attendance::where('admin_id', $admin->id)->where('date', $today)->first();
        if ($existing) {
            return response()->json(['message' => 'Already punched in'], 400);
        }

        if (!$this->isWithinOfficeRadius($request->latitude, $request->longitude)) {
            return response()->json(['message' => 'You are not at the office location'], 400);
        }

        Attendance::create([
            'admin_id' => $admin->id,
            'date' => $today,
            'check_in' => now(),
            'status' => 'Present',
        ]);

        return response()->json(['message' => 'Punched in successfully']);
    }


    public function punchOut(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('admin_id', $admin->id)->where('date', $today)->first();
        if (!$attendance) {
            return response()->json(['message' => 'Please punch in first'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Already punched out'], 400);
        }

        if (!$this->isWithinOfficeRadius($request->latitude, $request->longitude)) {
            return response()->json(['message' => 'You are not at the office location'], 400);
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = now();
        $workingMinutes = $checkIn->diffInMinutes($checkOut);

        $status = 'Present';
        $expectedMinutes = 480; // Default 8 hours
        $lateMarginMinutes = 15;

        // ✅ Load admin's office timing
        $day = strtolower(now()->format('l')); // monday, tuesday
        $timing = OfficeTiming::where('admin_id', $admin->id)
            ->where('day_of_week', $day)
            ->first();

        if ($timing) {
            $start = Carbon::parse($timing->start_time);
            $end = Carbon::parse($timing->end_time);
            $lunchStart = $timing->lunch_start ? Carbon::parse($timing->lunch_start) : null;
            $lunchEnd = $timing->lunch_end ? Carbon::parse($timing->lunch_end) : null;

            $expectedMinutes = $start->diffInMinutes($end);
            if ($lunchStart && $lunchEnd) {
                $expectedMinutes -= $lunchStart->diffInMinutes($lunchEnd);
            }

            if ($checkIn->gt($start->addMinutes($lateMarginMinutes))) {
                $status = 'Half Day';
            }

            if ($workingMinutes < ($expectedMinutes / 2)) {
                $status = 'Half Day';
            }
        }

        $attendance->update([
            'check_out' => $checkOut,
            'working_minutes' => $workingMinutes,
            'status' => $status,
        ]);

        return response()->json(['message' => 'Punched out successfully']);
    }


    private function isWithinOfficeRadius($userLat, $userLon, $radius = 15.0)
    {

        $officeLat = 28.5787807; // Replace with your office's actual coordinates
        $officeLon = 77.316088;

        $earthRadius = 6371; // km

        $dLat = deg2rad($officeLat - $userLat);
        $dLon = deg2rad($officeLon - $userLon);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($userLat)) * cos(deg2rad($officeLat)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        // dd($distance);
        return $distance <= $radius; // returns true if within radius
    }

    protected function attendances(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = Attendance::with('admin');

        if ($admin->role == 2) {
            $query->where('admin_id', $admin->id);
        }

        // if ($request->ajax() && $request->filled('type') && in_array($request->type, ['1', '2', '3'])) {
        //     $query->where('type', $request->type);
        // }

        $attendances = $query->get();

        if ($request->ajax()) {
            return view('Admin.attendances.partials.table', compact('attendances'));
        }
        return view('Admin.attendances.index', compact('attendances'));
    }
        
    protected function partners_contactlist(Request $request)
    {
         $query = Contact::with('user')->orderBy('id', 'desc');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                // ->orWhereHas('user', function ($q2) use ($search) {
                //     $q2->where('name', 'like', "%{$search}%");
                // });
            });
        }

        $contactList = $query->paginate(1000)->appends($request->query());

        return view('Admin.contactList.index', compact('contactList'));
    }

    public function ratingReview(){
        $ratingReviews = RatingReview::all();
        return view('Admin.rating-review.index', compact('ratingReviews'));
    }

    public function addRatingReview(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'profession' => 'required|string|max:255',
                'rating' => 'required|integer|min:1|max:5',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'content' => 'required|string|max:1000',
            ]);

            // Handle image upload
            $path = null;
            if ($request->hasFile('photo')) {
                $imageName = time() . '.' . $request->file('photo')->getClientOriginalExtension();
                $request->file('photo')->move(public_path('uploads/rating_reviews'), $imageName);
                $path = 'uploads/rating_reviews/' . $imageName;
            }

            // Save to DB
            RatingReview::create([
                'name' => $request->name,
                'profession' => $request->profession,
                'rating' => $request->rating,
                'photo' => $path,
                'content' => $request->content,
            ]);

        return redirect()->route('admin.rating_review')->with('success', 'Rating review added successfully!');
    }

        return view('Admin.rating-review.add');
    }

    public function editRatingReview(Request $request, $id)
    {
        $ratingReview = RatingReview::findOrFail($id);
        if ($request->isMethod('put')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'profession' => 'required|string|max:255',
                'rating' => 'required|integer|min:1|max:5',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // optional in edit
                'content' => 'required|string|max:1000',
            ]);

            // Handle image upload (optional on edit)
            if ($request->hasFile('photo')) {
                // delete old image if exists
                if ($ratingReview->photo && file_exists(public_path($ratingReview->photo))) {
                    unlink(public_path($ratingReview->photo));
                }

                $imageName = time() . '.' . $request->file('photo')->getClientOriginalExtension();
                $request->file('photo')->move(public_path('uploads/rating_reviews'), $imageName);
                $ratingReview->photo = 'uploads/rating_reviews/' . $imageName;
            }

            // Update other fields
            $ratingReview->update([
                'name'       => $request->name,
                'profession' => $request->profession,
                'rating'     => $request->rating,
                'content'    => $request->content,
                'photo'      => $ratingReview->photo, // keep old if no new upload
            ]);

        return redirect()->route('admin.rating_review')->with('success', 'Rating review updated successfully!');
    }
        return view('Admin.rating-review.edit', compact('ratingReview'));
    }

    public function deleteRatingReview($id)
    {
        $ratingReview = RatingReview::findOrFail($id);

        if ($ratingReview->photo && file_exists(public_path($ratingReview->photo))) {
            unlink(public_path($ratingReview->photo));
        }

        $ratingReview->delete();

        return redirect()->route('admin.rating_review')->with('success', 'Rating review deleted successfully!');
    }
    
      public function allReport(Request $request)
    {
        $categoryId = $request->category_id ?? null;
        $startDate  = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate    = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        // Base queries
        $userQuery = User::query();
        $securityQuery = Subscription::query();

        // ------------------------
        // Filter by category
        // ------------------------
        if ($categoryId) {
            $userQuery->where('category_id', $categoryId);
            $securityQuery->whereHas('plan', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // ------------------------
        // Filter by date range
        // ------------------------
        if ($startDate && $endDate) {
            // apply on user created_at
            $userQuery->whereBetween('created_at', [$startDate, $endDate]);

            // apply on subscription start_date
            $securityQuery->whereBetween('start_date', [$startDate, $endDate]);
        }

        // ------------------------
        // Users
        // ------------------------
        $totalUsers = (clone $userQuery)->where('role', 1)->count();
        $activeUsers = (clone $userQuery)->where('role', 1)->where('is_blocked', 0)->count();

        // ------------------------
        // Providers
        // ------------------------
        $totalProviders = (clone $userQuery)->where('role', 2)->count();

        $activeProviders = (clone $userQuery)
            ->where('role', 2)
            ->where('is_blocked', 0)
            ->whereHas('subscriptions', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'active')
                ->where('type', 1);

                // also respect date filter
                if ($startDate && $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                }
            })
            ->count();

        // ------------------------
        // Plans
        // ------------------------
        $totalPlans = (clone $securityQuery)->where('type', 1)->count();
        $activePlans = (clone $securityQuery)->where('type', 1)->where('status', 'active')->count();

        // ------------------------
        // Security
        // ------------------------
        $totalSecurity = (clone $securityQuery)->where('type', 2)->count();
        $activeSecurity = (clone $securityQuery)->where('type', 2)->where('status', 'active')->count();

        // Categories list
        $categories = Category::select('id', 'category')->get();

        // AJAX partial
        if ($request->ajax()) {
            return view('Admin.reports.partials.all-reports', compact(
                'totalProviders', 'activeProviders',
                'totalUsers','activeUsers',
                'totalPlans', 'activePlans',
                'categories',
                'totalSecurity','activeSecurity'
            ))->render();
        }

        // Full page
        return view('Admin.reports.all-reports', compact(
            'totalProviders', 'activeProviders',
            'totalUsers','activeUsers',
            'totalPlans', 'activePlans',
            'categories',
            'totalSecurity','activeSecurity'
        ));
    }

    public function providerReport(Request $request)
    {
        $categoryId = $request->category_id ?? null;
        $startDate  = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate    = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        // Base query: orders with provider & provider->category
        $query = Order::with([
            'provider:id,name,category_id',
            'provider.category:id,category'
        ]);

        // 🔹 Filter by provider's category
        if ($categoryId) {
            $query->whereHas('provider', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // 🔹 Filter by date range
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // 🔹 Group orders by provider
            $providerReports = $query->get()->groupBy('provider_id')->map(function ($orders) {
            $provider = $orders->first()->provider;

            return [
                'provider_id'     => $provider->id ?? null,
                'provider_name'   => $provider->name ?? 'N/A',
                'category'        => $provider->category->category ?? 'N/A',

                // Totals
                'total_leads'     => $orders->count(),
                'accepted_leads'  => $orders->where('status', 'accepted')->count(),
                'completed_leads' => $orders->where('status', 'completed')->count(),
                'skipped_leads'   => $orders->where('status', 'skipped')->count(),
            ];
        })->values(); // reset keys for clean array

        // 🔹 Categories list for dropdown filter
        $categories = Category::select('id', 'category')->get();

        // 🔹 Return partial if AJAX
        if ($request->ajax()) {
            return view('Admin.reports.partials.providers-report', compact('providerReports', 'categories'))->render();
        }

        // 🔹 Return full page
        return view('Admin.reports.providers', compact('providerReports', 'categories'));
    }

    public function getUserDataByBookingId($booking_id)
    {
        $order = Order::with([
            'user:id,name,email,mobile_no',
            'address:id,user_id,address'
        ])->where('id', $booking_id)->first();

        if (!$order || !$order->user) {
            return response()->json(['message' => 'No user found for this booking ID'], 404);
        }

        return response()->json([
            'id'      => $order->user->id,
            'name'    => $order->user->name,
            'email'   => $order->user->email,
            'mobile'  => $order->user->mobile_no,
            'address' => $order->address ? $order->address->address : null,
        ]);
    }

   public function quotations(){
        $quotations = DB::table('quotations')
                        ->join('quotation_items', 'quotations.id', '=', 'quotation_items.quotation_id')
                        ->join('orders', 'orders.id', '=', 'quotations.order_id')
                        ->join('users', 'users.id', '=', 'quotations.quotation_for')
                        ->select(
                            'quotations.id',
                            'quotations.quotation_no',
                            'quotations.quotation_date',
                            'quotations.total_amount',
                            'orders.booking_id',
                            'users.name')
                            ->distinct()
                            ->orderBy('quotations.created_at', 'desc')->get();
        return view('Admin.quotations.index', compact('quotations'));
    }

    public function addQuotations(Request $request)
    {
        $bookings = Order::select('id','booking_id')->orderBy('created_at', 'desc')->get();
        $contact_us = ContactUs::first();

        // Auto-generate quotation number
        $lastQuotation = Quotation::orderBy('id', 'desc')->first();
        $nextNumber = $lastQuotation ? intval(substr($lastQuotation->quotation_no, 4)) + 1 : 1;
        $quotationNo = 'ABQT' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    
        if ($request->isMethod('post')) {
            $request->validate([
                'quotation_no'          => 'required',
                'order_id'              => 'required',
                'quotation_date'        => 'required|date',
                'quotation_from'        => 'required',
                'quotation_for'         => 'required',
                'add_notes'             => 'nullable',
                'custom_address'        => 'nullable',
                'items'                 => 'required|array|min:1',
                'items.*.service_name'  => 'required|string',
                'items.*.quantity'      => 'required|integer|min:1',
                'items.*.rate'          => 'required|numeric|min:0',
                'items.*.image.*'       => 'nullable|image|mimes:jpg,jpeg,png',
            ]);

            DB::beginTransaction();
            try {
                $quotation = Quotation::create([
                    'order_id'       => $request->order_id,
                    'quotation_no'   => $quotationNo,
                    'quotation_date' => $request->quotation_date,
                    'quotation_from' => $request->quotation_from,
                    'quotation_for'  => $request->quotation_for,
                    'add_notes'      => $request->add_notes,
                    'custom_address' => $request->custom_address,
                    'discount_value' => $request->reduction_value ?? 0,
                    'discount_type'  => $request->reduction_type ?? '%',
                    'total_amount'   => 0,
                ]);

                $grandTotal = 0;

                foreach ($request->items as $index => $item) {
                    $amount = $item['quantity'] * $item['rate'];
                    $grandTotal += $amount;

                    // Handle multiple images for each item
                    $images = [];
                    if (isset($item['image']) && is_array($item['image'])) {

                        foreach ($item['image'] as $imageFile) {
                            if ($imageFile && $imageFile->isValid()) {
                                $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                                $imageFile->move(public_path('uploads/quotations'), $imageName);

                                $images[] = 'uploads/quotations/' . $imageName;
                            }
                        }
                    }
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'service_name' => $item['service_name'],
                        'description'  => $item['description'] ?? null,
                        'image'        => !empty($images) ? json_encode($images) : null, // store as JSON
                        'unit'         => $item['unit'] ?? null,
                        'quantity'     => $item['quantity'],
                        'rate'         => $item['rate'],
                        'amount'       => $amount,
                    ]);
                }

                // Apply discount
                if ($quotation->discount_type == "%") {
                    $grandTotal -= ($grandTotal * $quotation->discount_value / 100);
                } else {
                    $grandTotal -= $quotation->discount_value;
                }

                $quotation->update(['total_amount' => max(0, $grandTotal)]);

                DB::commit();
               
                return redirect()->route('admin.quotations')->with('success', 'Quotation saved successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        }
        return view('Admin.quotations.add', compact('bookings','contact_us','quotationNo'));
    }

    public function deleteQuotations($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete images from server
            foreach ($quotation->items as $item) {
                if ($item->image) {
                    $images = $item->image; // if stored as comma-separated or array
                    if (is_array($images)) {
                        foreach ($images as $imgPath) {
                            if (file_exists(public_path($imgPath))) {
                                @unlink(public_path($imgPath));
                            }
                        }
                    } else {
                        // if single image string
                        if (file_exists(public_path($images))) {
                            @unlink(public_path($images));
                        }
                    }
                }
            }

            // Delete child items
            $quotation->items()->delete();

            // Delete quotation
            $quotation->delete();

            DB::commit();
            return redirect()->route('admin.quotations')->with('success', 'Quotation deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.quotations')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function viewQuotations($id)
    {
        // Eager load related models: order, user, contact, items
            $quotation = Quotation::with([
                'order:id,booking_id,address_id',
                'user:id,name,email,mobile_no',
                'contact:id,phone_number,whatsapp_number,email,address',
                'items:id,quotation_id,service_name,description,quantity,rate,unit,amount'
            ])
            ->where('id', $id)
            ->firstOrFail();

            // dd($quotation);

        return view('Admin.quotations.view', compact('quotation'));
    }

    public function editQuotations(Request $request, $id)
    {
        // Load quotation with related models
        $quotation = Quotation::with([
            'order:id,booking_id,address_id',
            'user:id,name,email,mobile_no',
            'contact:id,phone_number,whatsapp_number,email,address',
            'items:id,quotation_id,service_name,description,quantity,unit,rate,amount,image',

        ])->findOrFail($id);

        $bookings = Order::select('id','booking_id')
                        ->orderBy('created_at', 'desc')
                        ->get();

        if ($request->isMethod('put')) {
            $request->validate([
                'order_id'              => 'required',
                'quotation_date'        => 'required|date',
                'quotation_from'        => 'required',
                'quotation_for'         => 'required',
                'add_notes'             => 'nullable',
                'custom_address'        => 'nullable',
                'items'                 => 'required|array|min:1',
                'items.*.service_name'  => 'required|string',
                'items.*.quantity'      => 'required|integer|min:1',
                'items.*.rate'          => 'required|numeric|min:0',
                'items.*.image.*'       => 'nullable|image|mimes:jpg,jpeg,png',
            ]);

            DB::beginTransaction();
            try {
                // Update main quotation
                $quotation->update([
                    'order_id'       => $request->order_id,
                    'quotation_date' => $request->quotation_date,
                    'quotation_from' => $request->quotation_from,
                    'quotation_for'  => $request->quotation_for,
                    'add_notes'      => $request->add_notes,
                    'custom_address' => $request->custom_address,
                    'discount_value' => $request->reduction_value ?? 0,
                    'discount_type'  => $request->reduction_type ?? '%',
                ]);

                $grandTotal = 0;

                // Delete existing items (or you can update selectively if needed)
                $quotation->items()->delete();

                // Insert updated items
                foreach ($request->items as $index => $item) {
                    $amount = $item['quantity'] * $item['rate'];
                    $grandTotal += $amount;

                    // Handle multiple images
                    $images = [];
                    if (isset($item['image']) && is_array($item['image'])) {
                        foreach ($item['image'] as $imageFile) {
                            if ($imageFile && $imageFile->isValid()) {
                                $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                                $imageFile->move(public_path('uploads/quotations'), $imageName);
                                $images[] = 'uploads/quotations/' . $imageName;
                            }
                        }
                    }

                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'service_name' => $item['service_name'],
                        'description'  => $item['description'] ?? null,
                        'image'        => !empty($images) ? json_encode($images) : null,
                        'unit'         => $item['unit'] ?? null,
                        'quantity'     => $item['quantity'],
                        'rate'         => $item['rate'],
                        'amount'       => $amount,
                    ]);
                }

                // Apply discount
                if ($quotation->discount_type == "%") {
                    $grandTotal -= ($grandTotal * $quotation->discount_value / 100);
                } else {
                    $grandTotal -= $quotation->discount_value;
                }

                $quotation->update(['total_amount' => max(0, $grandTotal)]);

                DB::commit();

                return redirect()->route('admin.quotations')->with('success', 'Quotation updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        }
        return view('Admin.quotations.edit', compact('quotation', 'bookings'));
    }

    public function downloadQuotations(Request $request, $id)
    {
        $quotation = Quotation::with([
            'order:id,booking_id,address_id',
            'order.address:id,address', // fetch address via order
            'user:id,name,email,mobile_no',
            'address:id,address',       // if quotation has direct address_id
            'contact:id,email,phone_number,whatsapp_number,address',
            'items:id,quotation_id,service_name,description,quantity,rate,unit,amount'
        ])->where('id', $id)->firstOrFail();

        $pdf = \PDF::loadView('Admin.quotations.quotation-pdf', compact('quotation'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quotation-'.$quotation->quotation_no.'.pdf');
    }

}
