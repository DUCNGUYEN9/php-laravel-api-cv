<?php

namespace App\Http\Controllers\Api\V1\CompanyControllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Carreer;
use App\Models\Company;
use App\Models\MailBox;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\ApplyListPost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    //profile
    public function profile(Request $request)
    {
        try {
            //check user exists
            $company = Company::find($request->id);
            if ($company) {
                $userId = $company->user_id;
                $user = User::find($userId);
                if ($user) {
                    $email = $user->email;
                    $company = Company::all();
                    return response()->json([
                        'status' => true,
                        'message' => 'profile listed successfully',
                        'email' => $email,
                        'data' => $company,
                    ], 200);
                } else {
                    echo "not info user";
                }
            } else {
                echo "not info user";
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //profile end
    //get all candidate
    public function getAllAndSearchCandidate(Request $request)
    {
        $candidateList = Candidate::where('job_position', '!=', '');
        //search from job_position
        if (isset($request->job_position) && $request->job_position != '') {
            $candidateList = $candidateList->where('job_position', 'like', '%' . $request->job_position . '%');
        }
        //pagination
        $candidateList = $candidateList->paginate(6);
        return response()->json([
            'status' => true,
            'message' => 'get all successfully',
            'data' => $candidateList,
        ], 200);
    }
    //get all candidate end
    /**
     * create
     */
    public function create(Request $request)
    {
        try {
            $validatorCandidate = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required|regex:/^\d{10,11}$/',
                'address' => 'required|string',
                'website' => 'string',
                'user_id' => 'required|exists:users,id|unique:companies,user_id',
            ]);
            /**
             * check validation
             */
            if ($validatorCandidate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'data' => $validatorCandidate->errors(),
                ], 422);
            }
            /**
             * check user Id
             */
            $userId = $request->user_id;
            if (Candidate::where('user_id', $userId)->exists()) {
                return  'user Id existed !';
            }
            if (Company::where('user_id', $userId)->exists()) {
                return 'user Id existed !';
            }
            /**
             * add data in $inputData
             */
            $inputData = array(
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'website' => $request->website,
                'user_id' => $request->user_id,

            );
            $company = Company::create($inputData);
            //return data added
            return response()->json([
                'status' => true,
                'message' => 'company added successfully',
                'data' => $company,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //create end
    // createCarreer

    public function createCarreer(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'company_id' => 'required',
            ]);

            $career = Carreer::create($validated);
            return response()->json($career, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //get all career
    public function getCarreer(Request $request)
    {
        $perPage = $request->input('per_page', 6);
        $careers = Carreer::paginate($perPage);
        return response()->json($careers);
    }
    //update carreer
    public function updateCarreer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $career = Carreer::findOrFail($request->id);
        $career->update($validated);

        return response()->json($career);
    }
    //delete career
    public function deleteCarreer($id)
    {
        $career = Carreer::findOrFail($id);
        $career->delete();
        return response()->json([
            'status' => true,
            'message' => 'delete success',
            'id' => $id
        ], 200);
    }
    // Carreer end
    //POST
    public function createPost(Request $request)
    {
        try {

            // Validate request data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'technology' => 'required|string',
                'salary' => 'required|string',
                'contact' => 'required|string',
                'expired_date' => 'required|date|after:today',
                'company_id' => 'required|exists:companies,id',
                'carreer_id' => 'required|exists:carreers,id',
                // Add more validation rules as needed
            ]);

            // Create a new Post instance
            $post = Post::create($validatedData);
            $career = Carreer::findOrFail($request->carreer_id);


            // Return a response with the newly created post
            return response()->json([
                'status' => true,
                'message' => 'success',
                'post' => $post,
                'carreer_name' => $career->name,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //POST end
    //getAppliedPosts
    public function getAppliedPosts()
    {
        $appliedPosts = ApplyListPost::distinct()->pluck('post_id');
        $posts = [];
        foreach ($appliedPosts as $postId) {
            $post = Post::find($postId);
            if ($post) {
                $appliedCandidates = ApplyListPost::where('post_id', $postId)->pluck('candidate_id');
                $candidates = Candidate::whereIn('id', $appliedCandidates)->get();
                $posts[] = [
                    'post' => $post,
                    'applied_candidates' => $candidates,
                ];
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $posts,
        ], 200);
    }
    //getAppliedPosts end
    //message
    public function sendMail(Request $request)
    {
        try {
            $validated = $request->validate([
                'messages' => 'required|string',
                'candidate_id' => 'required|exists:candidates,id',
                'company_id' => 'required|exists:companies,id'
            ]);
            $mail = MailBox::create($validated);
            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => $mail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //get mail
    public function getMail($candidate_id)
    {
        $mail = MailBox::where('candidate_id', $candidate_id)->get();
        if ($mail) {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $mail
            ], 200);
        }
    }
    //message end

}
