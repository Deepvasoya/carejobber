<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\User;
use App\Company;
use App\CompanyMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SeekerSendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Fetch all conversations (companies) for seeker
     */
    public function all_messages()
    {
        try {
            $seeker_id = Auth::guard('api')->user()->id;

            $companyIds = CompanyMessage::where('seeker_id', $seeker_id)
                ->pluck('company_id')
                ->unique()
                ->toArray();

            if (empty($companyIds)) {
                return response()->json(["success" => true, "data" => []]);
            }

            $companies = Company::whereIn('id', $companyIds)->get();
            $companyData = [];

            foreach ($companies as $company) {
                $lastMessage = CompanyMessage::where('company_id', $company->id)
                    ->where('seeker_id', $seeker_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $companyData[] = [
                    'id'            => $company->id,
                    'name'          => $company->name,
                    'logo'          => $company->logo,
                    'slug'          => $company->slug,
                    'message_count' => $company->countMessages($seeker_id),
                    'last_message'  => $lastMessage,
                ];
            }

            return response()->json(["success" => true, "data" => $companyData]);
        } catch (\Exception $e) {
            \Log::error('Error in all_messages: ' . $e->getMessage());
            return response()->json(["success" => false, "message" => "Error fetching messages: " . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch conversation messages
     */
    public function append_messages(Request $request)
    {
        try {
            $seeker_id  = Auth::guard('api')->user()->id;
            $company_id = $request->get('company_id');

            if (!$company_id) {
                return response()->json(["success" => false, "message" => "Company ID is required"], 400);
            }

            $messages = CompanyMessage::where('company_id', $company_id)
                ->where('seeker_id', $seeker_id)
                ->orderBy('created_at', 'asc')
                ->get();

            $seeker  = User::find($seeker_id);
            $company = Company::find($company_id);

            if (!$company) {
                return response()->json(["success" => false, "message" => "Company not found"], 404);
            }

            $formattedMessages = [];
            foreach ($messages as $msg) {
                $isSeeker = $msg->type === 'message'; // ✅ seeker = message, company = reply

                $formattedMessages[] = [
                    'id'          => $msg->id,
                    'company_id'  => $msg->company_id,
                    'seeker_id'   => $msg->seeker_id,
                    'message'     => $msg->message,
                    'status'      => $msg->status,
                    'type' => $msg->type,
                    'created_at'  => $msg->created_at,
                    'updated_at'  => $msg->updated_at,
                    'is_seeker'   => $isSeeker,
                    'sender_name' => $isSeeker ? $seeker->name : $company->name,
                ];
            }

            return response()->json([
                "success" => true,
                "data"    => [
                    "messages" => $formattedMessages,
                    "seeker"   => [
                        'id'    => $seeker->id,
                        'name'  => $seeker->name,
                        'email' => $seeker->email,
                    ],
                    "company"  => [
                        'id'    => $company->id,
                        'name'  => $company->name,
                        'logo'  => $company->logo,
                        'slug'  => $company->slug,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in append_messages: ' . $e->getMessage());
            return response()->json(["success" => false, "message" => "Error fetching messages: " . $e->getMessage()], 500);
        }
    }

    /**
     * Seeker sends message
     */
    public function submit_message(Request $request)
{
    $this->validate($request, [
        'message'    => 'required',
        'company_id' => 'required|exists:companies,id',
    ]);

    $seeker_id = Auth::guard('api')->user()->id;

    $message = new CompanyMessage();
    $message->company_id = $request->company_id;
    $message->message    = $request->message;
    $message->seeker_id  = $seeker_id;
    $message->status     = 'unviewed';
    $message->type       = 'message'; // ✅ seeker always message
    $message->save();

    $seeker  = User::find($seeker_id);
    $company = Company::find($request->company_id);

    $isSeeker = true;

    $formattedMessage = [
        'id'          => $message->id,
        'company_id'  => $message->company_id,
        'seeker_id'   => $message->seeker_id,
        'message'     => $message->message,
        'status'      => $message->status,
        'type'        => $message->type,
        'created_at'  => $message->created_at,
        'updated_at'  => $message->updated_at,
        'is_seeker'   => $isSeeker,
        'sender_name' => $isSeeker ? $seeker->name : $company->name,
    ];

    return response()->json([
        'success' => true,
        'message' => 'Your message has been posted successfully.',
        'data'    => $formattedMessage
    ]);
}


    /**
     * Company sends reply
     */
    public function companyReply(Request $request)
    {
        $this->validate($request, [
            'message'    => 'required',
            'seeker_id'  => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $message              = new CompanyMessage();
        $message->company_id  = $request->company_id;
        $message->message     = $request->message;
        $message->seeker_id   = $request->seeker_id;
        $message->status      = 'unviewed';
        $message->type        = 'reply'; // ✅ company always reply
        $message->save();

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data'    => $message
        ]);
    }

    /**
     * Append messages only (alternative endpoint)
     */
    public function appendonly_messages(Request $request)
    {
        return $this->append_messages($request); // ✅ reuse logic
    }

    /**
     * Change message status
     */
    public function change_message_status(Request $request)
    {
        try {
            $company_id = $request->get('sender_id');
            $seeker_id  = Auth::guard('api')->user()->id;

            if (!$company_id) {
                return response()->json(['success' => false, 'message' => 'Sender ID is required'], 400);
            }

            $messages = CompanyMessage::where('company_id', $company_id)
                ->where('seeker_id', $seeker_id)
                ->get();

            foreach ($messages as $message) {
                $message->status = 'viewed';
                $message->save();
            }

            return response()->json(['success' => true, 'message' => 'Message status updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error in change_message_status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating message status: ' . $e->getMessage()], 500);
        }
    }
}
