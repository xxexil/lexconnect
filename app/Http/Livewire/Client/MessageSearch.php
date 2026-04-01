<?php

namespace App\Http\Livewire\Client;


use Livewire\Component;
use App\Models\Conversation;

class MessageSearch extends Component
{
    public $search = '';
    public $activeConv;

    public function mount($activeConv = null)
    {
        $this->activeConv = $activeConv;
    }

    public function render()
    {
        $userId = auth()->id();
        $search = trim($this->search);

        $conversations = Conversation::with(['client', 'lawyer', 'messages', 'latestMessage'])
            ->where(function($q) use ($userId) {
                $q->where('client_id', $userId)
                  ->orWhere('lawyer_id', $userId);
            })
            ->when($search, function($query) use ($userId, $search) {
                $query->where(function($q) use ($userId, $search) {
                    $q->where(function($sub) use ($userId, $search) {
                        // If user is client, search lawyer's name
                        $sub->where('client_id', $userId)
                            ->whereHas('lawyer', function($lawyer) use ($search) {
                                $lawyer->where('name', 'like', "%$search%");
                            });
                    })
                    ->orWhere(function($sub) use ($userId, $search) {
                        // If user is lawyer, search client's name
                        $sub->where('lawyer_id', $userId)
                            ->whereHas('client', function($client) use ($search) {
                                $client->where('name', 'like', "%$search%");
                            });
                    });
                });
            })
            ->get();

        return view('livewire.client.message-search', [
            'conversations' => $conversations,
            'activeConv' => $this->activeConv,
        ]);
    }
}
