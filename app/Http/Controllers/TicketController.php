<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->except('show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tickets = Ticket::paginate(10);
        return view('tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, ['email' => 'required|email']);

        // creating a ticket
        $ticket = Ticket::create([
            'email' => $request->email,
            'ticket_string' => Str::random(32),
            'status' => 'active',
        ]);

        if ($ticket) {
            return back()->with(['success' => 'Created a ticket successfully']);
        } else {
            return back()->with(['danger' => 'Something went wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        //
        return view('tickets.show', ['ticket' => $ticket]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function check()
    {
        return view('check');
    }

    public function test_and_use(Request $request)
    {
        $this->validate($request, ['ticket_string' => 'required|min:32']);

        $ticket = Ticket::where(
            'ticket_string',
            $request->ticket_string
        )->first();

        if ($ticket) {
            $info = [
                'id' => $ticket->id,
                'ticket' => $ticket->ticket_string,
                'email' => $ticket->email,
                'created' => $ticket->created_at->diffForHumans(),
                'status' => $ticket->status,
            ];

            if ($ticket->status == 'active') {
                $ticket->status = 'used';
                $ticket->used_at = now();
            }
            $ticket->save();

            return $info;
        }
    }

    public function download(Ticket $ticket)
    {
        return view('tickets.print', ['ticket' => $ticket]);
    }
}
