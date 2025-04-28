<?php 
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    private $leaveRequest;
    private $type;

    public function __construct($leaveRequest, $type)
    {
        $this->leaveRequest = $leaveRequest;
        $this->type = $type; // نوع الإشعار (طلب جديد، موافقة، رفض)
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // إرسال الإشعار عبر البريد وقاعدة البيانات
    }

    public function toMail($notifiable)
    {
        $message = new MailMessage();
        
        if ($this->type === 'new_request') {
            $message->subject('طلب إجازة جديد')
                    ->line('قام الموظف ' . $this->leaveRequest->employee->name . ' بتقديم طلب إجازة.')
                    ->action('عرض الطلب', url('/dashboard/leave-requests/' . $this->leaveRequest->id));
        } elseif ($this->type === 'approved') {
            $message->subject('تمت الموافقة على طلب الإجازة')
                    ->line('تمت الموافقة على طلب الإجازة الخاص بك.')
                    ->action('عرض التفاصيل', url('/dashboard/leave-requests/' . $this->leaveRequest->id));
        } elseif ($this->type === 'rejected') {
            $message->subject('تم رفض طلب الإجازة')
                    ->line('تم رفض طلب الإجازة الخاص بك.')
                    ->action('عرض التفاصيل', url('/dashboard/leave-requests/' . $this->leaveRequest->id));
        }

        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'employee_name' => $this->leaveRequest->employee->name,
            'type' => $this->type
        ];
    }
}