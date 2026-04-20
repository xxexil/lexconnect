<?php

namespace App\Livewire\Lawyer;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class EarningsHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $type = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'type']);
        $this->resetPage();
    }

    public function render()
    {
        $userId = auth()->id();
        $search = trim($this->search);

        $query = Payment::with(['client', 'consultation'])
            ->where('lawyer_id', $userId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->whereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('consultation', function ($consultationQuery) use ($search) {
                        $consultationQuery->where('code', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->type !== '', function ($query) {
                $query->where('type', $this->type);
            });

        // Filtered subtotals (before pagination)
        $filteredAll     = $query->clone()->get();
        $filteredNet     = $filteredAll->whereIn('status', ['paid', 'downpayment_paid'])
                            ->sum(fn($p) => $p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)));
        $filteredPending = $filteredAll->where('status', 'pending')
                            ->sum(fn($p) => $p->amount - ($p->firm_cut ?? 0));

        $payments = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10);

        return view('livewire.lawyer.earnings-history', [
            'payments'        => $payments,
            'filteredNet'     => $filteredNet,
            'filteredPending' => $filteredPending,
        ]);
    }
}
