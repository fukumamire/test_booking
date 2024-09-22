<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;

class EmailNotificationController extends Controller
{
  public function index()
  {
    if (!Auth::guard('admin')->check()) {
      return redirect()->route('admin.login');
    }
    return view('admin.email-notifications.index');
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'subject' => 'required|string|max:255',
      'content' => 'required|string',
      'recipients' => 'required|array|min:1',
      'recipients.*' => 'email', // 各配列要素がメールアドレスであることを確認
    ]);

    try {
      Mail::to($validatedData['recipients'])->send(new SendEmailNotification(
        $validatedData['subject'],
        $validatedData['content']
      ));

      session()->flash('success', 'お知らせメールが正常に送信されました。');
      return redirect()->route('admin.email-notification');
    } catch (\Exception $e) {
      session()->flash('error', 'メールの送信中にエラーが発生しました。');
      return redirect()->route('admin.email-notification');
    }
  }
}

// メール送信クラスの定義
class SendEmailNotification extends Mailable
{
  public $subject;
  public $content;

  public function __construct($subject, $content)
  {
    $this->subject = $subject;
    $this->content = $content;
  }

  public function build()
  {
    return $this->view('emails.notification')
      ->subject($this->subject);
  }
}
