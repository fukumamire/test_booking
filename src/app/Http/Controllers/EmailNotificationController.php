<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class EmailNotificationController extends Controller
{
  public function index()
  {
    return view('admin.email-notifications.index');
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'subject' => 'required|string|max:255',
      'content' => 'required|string',
      'recipients' => 'required|array|min:1',
    ]);

    try {
      Mail::to($validatedData['recipients'])->send(new SendEmailNotification(
        $validatedData['subject'],
        $validatedData['content']
      ));

      return redirect()->route('admin.email-notification')->with('success', 'お知らせメールを作成しました。');
    } catch (\Exception $e) {
      return back()->with('error', 'メールの送信中にエラーが発生しました。');
    }
  }
}

// メール送信クラスの定義（例）
// class SendEmailNotification extends Mailable
// {
//   use Queueable, SerializesModels;

//   public $subject;
//   public $content;

//   public function __construct($subject, $content)
//   {
//     $this->subject = $subject;
//     $this->content = $content;
//   }

//   public function build()
//   {
//     return $this->view('emails.notification')
//       ->subject($this->subject);
//   }
// }
