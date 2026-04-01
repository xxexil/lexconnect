<?php

namespace App\Livewire\Client;

use App\Models\Conversation;
use Livewire\Component;

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
            ->where(function ($query) use ($userId) {
                $query->where('client_id', $userId)
                    ->orWhere('lawyer_id', $userId);
            })
            ->when($search !== '', function ($query) use ($userId, $search) {
                $query->where(function ($nested) use ($userId, $search) {
                    $nested->where(function ($subQuery) use ($userId, $search) {
                        $subQuery->where('client_id', $userId)
                            ->whereHas('lawyer', function ($lawyerQuery) use ($search) {
                                $lawyerQuery->where('name', 'like', "%{$search}%");
                            });
                    })->orWhere(function ($subQuery) use ($userId, $search) {
                        $subQuery->where('lawyer_id', $userId)
                            ->whereHas('client', function ($clientQuery) use ($search) {
                                $clientQuery->where('name', 'like', "%{$search}%");
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
