<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;


class VerifyEmail extends Mailable
{
  use Queueable, SerializesModels;

  public $user;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($user)
  {
    $this->user = $user;

  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('emails.verify-email')
    ->subject('メールアドレスの確認')
    ->with([
      'user' => $this->user,
      'verificationUrl' => $this->verificationUrl($this->user),
    ]);
  }

  protected function verificationUrl($user)
  {
    return URL::temporarySignedRoute(
      'verification.verify',
      now()->addMinutes(60),
      ['id' => $user->getKey()]
    );
  }

}
