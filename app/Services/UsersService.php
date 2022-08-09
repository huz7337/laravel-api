<?php


namespace App\Services;


use App\Jobs\UpdateConversationParticipant;
use App\Models\Invitation;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\ClientAcceptedInvitationEmail;
use App\Notifications\ClientInvitationEmail;
use App\Notifications\NewAccountCreatedEmail;
use App\Notifications\PasswordChangedEmail;
use App\Notifications\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UsersService
{

    /**
     * Authenticate user
     * @param User $user
     * @return string
     */
    public function login(User $user)
    {
        return $user->createToken('auth')->plainTextToken;
    }


    /**
     * Create user account
     * @param array $data
     * @return User
     */
    public function register(array $data)
    {
        // only client users can register on their own
        $data['account_type'] = User::ACCOUNT_TYPE_CLIENT;
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // accept terms & conditions
        $user->acceptTerms();
        // create profile
        $user->profile()->create();
        // assign athlete role
        $user->assignRole(User::ROLE_USER);
        // send confirmation email
        $user->notify(new NewAccountCreatedEmail());

        return $user;
    }


    /**
     * Change the user's password
     * @param array $credentials
     * @return mixed
     */
    public function resetPassword(array $credentials)
    {
        return Password::reset($credentials, function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
            $user->notify(new PasswordChangedEmail());
        });
    }


    /**
     * Update the user and their profile
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        if (isset($data['profile'])) {
            if ($user->profile()->exists()) {
                $user->profile->update($data['profile']);
            } else {

                $user->profile()->create($data['profile']);
            }
        }

        $user->load('profile')->refresh();

        return $user;
    }
}
