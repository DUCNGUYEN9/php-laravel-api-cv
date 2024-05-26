<?php

namespace App\Http\Controllers\Api\V1\CandidateControllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Company;
use App\Models\MailBox;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\ApplyListPost;
use App\Models\InterestsListPost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{



    /**
     * Profile
     * @param  $token:get token from candidate login
     */

    public function profile(Request $request)
    {
        try {
            //check user exists
            $candidate = Candidate::find($request->id);
            if ($candidate) {
                $userId = $candidate->user_id;
                $user = User::find($userId);
                if ($user) {
                    $email = $user->email;
                    $candidate = Candidate::all();
                    return response()->json([
                        'status' => true,
                        'message' => 'profile listed successfully',
                        'email' => $email,
                        'data' => $candidate,
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
    /**
     * get all info company
     */
    public function getAllAndSearchCompany(Request $request)
    {
        $companies = Company::where('address', '!=', '');
        //search from address
        if (isset($request->address) && $request->address != '') {
            $companies = $companies->where('address', 'like', '%' . $request->address . '%');
        }
        //pagination
        $companies = $companies->paginate(6);
        return response()->json([
            'status' => true,
            'message' => 'get all successfully',
            'data' => $companies,
        ], 200);
    }
    /**
     * create
     */
    public function create(Request $request)
    {
        try {
            $validatorCandidate = Validator::make($request->all(), [
                'full_name' => 'required',
                'birth_date' => 'required|date',
                'phone' => 'required|regex:/^\d{10,11}$/',
                // 'avatar' => 'nullable|url',
                'school' => 'required|string',
                'address' => 'required|string',
                'bio' => 'required|string',
                'job_position' => 'required|string',
                'certification' => 'required|string',
                'user_id' => 'required|exists:users,id|unique:candidates,user_id',
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
                'full_name' => $request->full_name,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                // 'avatar' => isset($request->avatar) ? $request->avatar : '',
                'school' => $request->school,
                'address' => $request->address,
                'bio' => $request->bio,
                'job_position' => $request->job_position,
                'certification' => $request->certification,
                'user_id' => $request->user_id,

            );
            $candidate = Candidate::create($inputData);
            //return data added
            return response()->json([
                'status' => true,
                'message' => 'added successfully',
                'data' => $candidate,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create candidate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //create end
    /**
     * update
     */
    public function update(Request $request)
    {
        try {
            $validatorCandidate = Validator::make($request->all(), [
                'id' => 'required|exists:candidates,id',
                'full_name' => 'required',
                'birth_date' => 'required|date',
                'phone' => 'required|regex:/^\d{10,11}$/',
                // 'avatar' => 'nullable|url',
                'school' => 'required|string',
                'address' => 'required|string',
                'bio' => 'required|string',
                'job_position' => 'required|string',
                'certification' => 'required|string',
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
             * update data
             */
            $candidate = Candidate::find($request->id);
            $candidate->full_name = $request->full_name;
            $candidate->birth_date = $request->birth_date;
            $candidate->phone = $request->phone;
            // $candidate->avatar = $request->avatar;
            $candidate->school = $request->school;
            $candidate->address = $request->address;
            $candidate->bio = $request->bio;
            $candidate->job_position = $request->job_position;
            $candidate->certification = $request->certification;
            $candidate->save();
            //return data added
            return response()->json([
                'status' => true,
                'message' => 'updated successfully',
                'data' => $candidate,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update candidate',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //update end
    //MailBox
    public function mailBox($candidate_id)
    {
        try {
            $mailBox = MailBox::where('candidate_id', $candidate_id)->get();
            if ($mailBox) {
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'data' => $mailBox,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'No data found',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    //mailBox end
    //addInterestToList
    public function addInterestToList(Request $request)
    {
        try {
            $candidate_id = $request->input('candidate_id');
            $post_id = $request->input('post_id');

            $existingInterest = InterestsListPost::where('candidate_id', $candidate_id)
                ->where('post_id', $post_id)
                ->first();

            if (!$existingInterest) {
                $interest = new InterestsListPost();
                $interest->candidate_id = $candidate_id;
                $interest->post_id = $post_id;
                $interest->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Interest added successfully.',
                    'interest' => $interest
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Interest already exists.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    //addInterestToList end
    //interests_list_posts
    public function getInterestListPosts($candidate_id)
    {
        $interestListPosts = InterestsListPost::where('candidate_id', $candidate_id)->get();
        if ($interestListPosts) {
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => $interestListPosts,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'no data found',
        ], 200);
    }
    //interests_list_posts end
    //addApplyToList
    public function addApplyToList(Request $request)
    {
        try {
            $candidate_id = $request->input('candidate_id');
            $post_id = $request->input('post_id');

            $existingApply = ApplyListPost::where('candidate_id', $candidate_id)
                ->where('post_id', $post_id)
                ->first();

            if (!$existingApply) {
                $apply = new ApplyListPost();
                $apply->candidate_id = $candidate_id;
                $apply->post_id = $post_id;
                $apply->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Apply added successfully.',
                    'apply' => $apply
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Apply already exists.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    //addApplyToList end
    //apply_list_posts
    public function getApplyListPost($candidate_id)
    {
        try {
            $applyListPost = ApplyListPost::where('candidate_id', $candidate_id)->get();
            if ($applyListPost) {
                $postIds = $applyListPost->pluck('post_id')->toArray();

                $posts = Post::whereIn('id', $postIds)->get();
                foreach ($posts as $post) {
                    $postData[] = [
                        'title' => $post->title,
                        'content' => $post->content,
                        'technology' => $post->technology,
                        'salary' => $post->salary,
                        'contact' => $post->contact,
                        'expired_date' => $post->expired_date,
                    ];
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $postData,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'no data found',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    //apply_list_posts end
}
