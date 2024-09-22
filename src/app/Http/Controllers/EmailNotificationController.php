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
      
      'recipients.*' => 'email', // 各配列要素がメールアドレスであることを確認
    ]);

    try {
      Mail::to($validatedData['recipients'])->send(new SendEmailNotification(
        $validatedData['subject'],
        $validatedData['content']
      ));

      return redirect()->route('admin.email-notification')->with('success', 'お知らせメールが正常に送信されました。');
    } catch (\Exception $e) {
      return redirect()->route('admin.email-notification')->with('error', 'メールの送信中にエラーが発生しました。');
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
