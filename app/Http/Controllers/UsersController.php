<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListPagesRequest;
use App\Http\Requests\ListUsersRequest;
use App\Http\Requests\ShowUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Transformers\UserTransformer;
use App\Models\User;
use App\Notifications\PasswordChangedEmail;
use App\Queries\PagesQuery;
use App\Queries\UsersQuery;
use App\Services\UsersService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{

    /**
     * @var UserTransformer
     */
    protected UserTransformer $_transformer;

    /**
     * @var UsersService
     */
    protected UsersService $_service;

    /**
     * UsersController constructor.
     * @param UserTransformer $transformer
     * @param UsersService $service
     */
    public function __construct(UserTransformer $transformer, UsersService $service)
    {
        $this->_transformer = $transformer;
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(ListUsersRequest $request)
    {
        $params = $request->validated();

        $items = UsersQuery::make($params)->query()->paginate(
            $params['per_page'] ?? config('filters.per_page')
        );

        return response()->success($this->_transformer->transform($items));
    }

    /**
     * Show the user's profile
     * @param ShowUserRequest $request
     * @param User $user
     * @return mixed
     */
    public function show(ShowUserRequest $request, User $user)
    {
        $request->validated();
        return response()->success($this->_transformer->transform($user->load(['profile'])));
    }

    /**
     * See your own profile
     * @return mixed
     */
    public function getOwnProfile()
    {
        return response()->success($this->_transformer->transform(Auth::user()->load('profile')));
    }

    /**
     * Update the user's profile
     * @param UpdateProfileRequest $request
     * @param User $user
     * @return mixed
     */
    public function update(UpdateProfileRequest $request, User $user)
    {
        $data = $request->validated();
        $user = $this->_service->update($user, $data);
        return response()->success($this->_transformer->transform($user));
    }

    /**
     * Update your own profile
     * @param UpdateProfileRequest $request
     * @return mixed
     */
    public function updateOwnProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = $this->_service->update(Auth::user(), $data);
        return response()->success($this->_transformer->transform($user));
    }

    /**
     * Upload a new photo to the user's profile
     * @param UpdatePhotoRequest $request
     * @param User|null $user
     * @return mixed
     */
    public function updateProfilePhoto(UpdatePhotoRequest $request, User $user = null)
    {
        try {
            // upload the new image
            $path = $request->file('photo')->store('profile-photos', 's3');
            /**
             * @var User $user
             */
            if (!$user) {
                $user = Auth::user();
            }

            // check if the user already has a profile photo
            if ($user->profile()->exists()) {
                $currentPhoto = $user->profile->photo;
                if ($currentPhoto) {
                    // delete the old photo
                    Storage::disk('s3')->delete($currentPhoto);
                }
            } else {
                $user->profile()->create();
            }


            // save the new photo on the profile
            $user->profile->photo = $path;
            $user->profile->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->error(__('We were unable to upload your photo at this time. Please try again later'));
        }

        return response(['photo' => $user->profile->photo ? config('filesystems.disks.s3.url') . '/' . $user->profile->photo : null]);
    }

    /**
     * Update your own password
     * @param UpdatePasswordRequest $request
     * @return mixed
     */
    public function changePassword(UpdatePasswordRequest $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();
        $data = $request->validated();
        if ($data['password']) {
            $user->forceFill(['password' => Hash::make($data['password'])])->save();
            $user->notify(new PasswordChangedEmail());
            return response()->message(__('The password has been changed successfully.'));
        }

        return response()->errorMessage(__('We were unable to update your password at this time. Please try again.'));
    }

    /**
     * Get the user's profile photo
     * @param User $user
     * @return Application|RedirectResponse|Redirector
     */
    public function profilePhoto(User $user)
    {
        if (!optional($user->profile)->photoUrl()) {
            return response()->empty();
        }

        $url = $user->profile->photoUrl();

        return redirect($url);
    }
}
