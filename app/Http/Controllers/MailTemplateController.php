<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateMail;
use App\Models\EmailTemplate;

class MailTemplateController extends Controller
{
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
        ]);

        $template = EmailTemplate::create([
            'name' => $request->input('name'),
            'content' => $request->input('content')
        ]);

        return response()->json($template, 201);
    }

    public function getTemplates()
    {
        $templates = EmailTemplate::all();
        return response()->json($templates);
    }

    public function deleteTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Template deleted successfully.']);
    }

    public function sendMailTemplate(Request $request)
    {
        $request->validate([
            'templateName' => 'required|string',
            'templateContent' => 'required|string',
            'recipientEmail' => 'required|email',
            'dynamicData' => 'required|array',
        ]);

        $templateName = $request->input('templateName');
        $templateContent = $request->input('templateContent');
        $recipientEmail = $request->input('recipientEmail');
        $dynamicData = $request->input('dynamicData');
        

        $mail = Mail::to($recipientEmail);

        if ($ccEmails = $request->input('ccEmails', [])) {
            $mail->cc($ccEmails);
        }

        $mail->send(new TemplateMail($templateName, $templateContent, $dynamicData));

        return response()->json(['message' => 'Email sent successfully.']);
    }
}
