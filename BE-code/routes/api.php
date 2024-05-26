<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthControllers\AuthController;
use App\Http\Controllers\Api\V1\UploadControllers\ImageUploadController;
use App\Http\Controllers\Api\V1\CandidateControllers\CandidateController;
use App\Http\Controllers\Api\V1\CompanyControllers\CompanyController;

/**
 * call api  "/register"
 */
Route::post("register", [AuthController::class, "register"]);

/**
 * call api  "/login"
 */
Route::post("login", [AuthController::class, "login"]);
/**
 * call api  "/logout" & candidate & company after loged in
 */
Route::group([
    "middleware" => "auth:sanctum",

], function () {
    //General features
    Route::get("logout", [AuthController::class, "logout"]);
    Route::post("upload/{user_id}", [ImageUploadController::class, "uploadOrUpdate"]);
    Route::get("show/{user_id}", [ImageUploadController::class, "show"]);
    //Candidate Api
    Route::post("candidate/profile", [CandidateController::class, "profile"]);
    Route::post("candidate/getAllAndSearch", [CandidateController::class, "getAllAndSearchCompany"]);
    Route::post("candidate/create", [CandidateController::class, "create"]);
    Route::post("candidate/update", [CandidateController::class, "update"]);
    Route::delete("candidate/delete", [CandidateController::class, "delete"]);
    Route::get("candidate/mailBox/{candidate_id}", [CandidateController::class, "mailBox"]);
    Route::post("candidate/addInterestToList", [CandidateController::class, "addInterestToList"]);
    Route::get("candidate/getInterestListPost/{candidate_id}", [CandidateController::class, "getInterestListPosts"]);
    Route::post("candidate/addApplyToList", [CandidateController::class, "addApplyToList"]);
    Route::get("candidate/getApplyListPost/{candidate_id}", [CandidateController::class, "getApplyListPost"]);
    //Company Api
    Route::post("company/profile", [CompanyController::class, "profile"]);
    Route::post("company/getAllAndSearchCandidate", [CompanyController::class, "getAllAndSearchCandidate"]);
    Route::post("company/create", [CompanyController::class, "create"]);
    Route::post("company/createCarreer", [CompanyController::class, "createCarreer"]);
    Route::get("company/getCarreer", [CompanyController::class, "getCarreer"]);
    Route::post("company/updateCarreer", [CompanyController::class, "updateCarreer"]);
    Route::delete("company/deleteCarreer/{id}", [CompanyController::class, "deleteCarreer"]);
    Route::post("company/createPost", [CompanyController::class, "createPost"]);
    Route::get("company/getAppliedPosts", [CompanyController::class, "getAppliedPosts"]);
    Route::post("company/createPost", [CompanyController::class, "createPost"]);
    Route::post("company/sendMail", [CompanyController::class, "sendMail"]);
    Route::get("company/getMail/{candidate_id}", [CompanyController::class, "getMail"]);
});
