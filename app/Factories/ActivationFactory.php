<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 3/15/2018
 * Time: 5:29 PM
 */
namespace App\Factories;

use App\Models\User;
use App\Repositories\ActivationRepository;
use Illuminate\Support\Facades\Mail;

class ActivationFactory
{
    protected $activationRepo;
    protected $resendAfter = 24;

    public function __construct(ActivationRepository $activationRepo, Mail $mail)
    {
        $this->activationRepo = $activationRepo;
    }

    public function sendActivationMail($user)
    {
        if ($user->activated || !$this->shouldSend($user)) {
            return;
        }

        $token = $this->activationRepo->createActivation($user);

        $link = route('user.activate', $token);
        $body = sprintf('Activate account %s', $link, $link);

        Mail::send([], [], function($message) use ($body, $user) {
            $message->to([$user->email]);
            $message->subject('Activation mail');
            $message->setBody("<p>$body</p>", 'text/html');
        });
    }

    public function activateUser($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $user = User::find($activation->user_id);

        $user->activated = true;

        $user->save();

        $this->activationRepo->deleteActivation($token);

        return $user;
    }

    private function shouldSend($user)
    {
        $activation = $this->activationRepo->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }
}