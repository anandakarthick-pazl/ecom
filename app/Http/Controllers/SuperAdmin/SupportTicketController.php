<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\SupportTicket;
use App\Models\SuperAdmin\TicketReply;
use App\Models\SuperAdmin\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreated;
use App\Mail\TicketStatusUpdated;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with(['company', 'user', 'assignedTo'])
                                ->latest()
                                ->paginate(15);

        return view('super-admin.support.index', compact('tickets'));
    }

    public function create()
    {
        $companies = Company::active()->get();
        $agents = User::superAdmins()->get();

        return view('super-admin.support.create', compact('companies', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $ticket = SupportTicket::create([
            'company_id' => $request->company_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
            'assigned_to' => $request->assigned_to,
            'status' => 'open'
        ]);

        // Send email notifications
        $company = Company::find($request->company_id);
        
        try {
            Mail::to($company->email)->send(new TicketCreated($ticket));
            
            if ($request->assigned_to) {
                $agent = User::find($request->assigned_to);
                Mail::to($agent->email)->send(new TicketCreated($ticket));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket creation email: ' . $e->getMessage());
        }

        return redirect()->route('super-admin.support.show', $ticket)
                        ->with('success', 'Support ticket created successfully!');
    }

    public function show(SupportTicket $support)
    {
        $support->load(['company', 'user', 'assignedTo', 'replies.user']);
        $agents = User::superAdmins()->get();

        return view('super-admin.support.show', compact('support', 'agents'));
    }

    public function edit(SupportTicket $support)
    {
        $companies = Company::active()->get();
        $agents = User::superAdmins()->get();

        return view('super-admin.support.edit', compact('support', 'companies', 'agents'));
    }

    public function update(Request $request, SupportTicket $support)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:open,in_progress,waiting_customer,resolved,closed'
        ]);

        $oldStatus = $support->status;
        
        $support->update($request->only([
            'title', 'description', 'priority', 'category', 'assigned_to', 'status'
        ]));

        if ($support->status === 'resolved' && $oldStatus !== 'resolved') {
            $support->update(['resolved_at' => now()]);
        }

        // Send status update email if status changed
        if ($oldStatus !== $support->status) {
            try {
                Mail::to($support->company->email)->send(new TicketStatusUpdated($support));
            } catch (\Exception $e) {
                \Log::error('Failed to send ticket status update email: ' . $e->getMessage());
            }
        }

        return redirect()->route('super-admin.support.show', $support)
                        ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(SupportTicket $support)
    {
        $support->delete();

        return redirect()->route('super-admin.support.index')
                        ->with('success', 'Ticket deleted successfully!');
    }

    public function respond(Request $request, SupportTicket $support)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120', // 5MB max
            'close_ticket' => 'boolean'
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support/attachments', 'public');
                $attachments[] = $path;
            }
        }

        // Add response to ticket responses array
        $responses = $support->responses ?? [];
        $responses[] = [
            'type' => 'admin',
            'message' => $request->message,
            'attachments' => $attachments,
            'created_at' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ];

        $support->update([
            'responses' => $responses,
            'status' => $request->boolean('close_ticket') ? 'closed' : 'in_progress',
            'updated_at' => now()
        ]);

        if ($request->boolean('close_ticket')) {
            $support->update(['resolved_at' => now()]);
        }

        return redirect()->route('super-admin.support.show', $support)
                        ->with('success', 'Response added successfully!');
    }

    public function updateStatus(Request $request, SupportTicket $support)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,pending,resolved,closed'
        ]);

        $oldStatus = $support->status;
        $support->update(['status' => $request->status]);

        if ($support->status === 'resolved' && $oldStatus !== 'resolved') {
            $support->update(['resolved_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function updatePriority(Request $request, SupportTicket $support)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $support->update(['priority' => $request->priority]);

        return response()->json(['success' => true]);
    }

    public function assign(Request $request, SupportTicket $support)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $support->update(['assigned_to' => $request->assigned_to]);

        return response()->json(['success' => true]);
    }
}
