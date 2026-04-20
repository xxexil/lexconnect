<div class="lp-card">
    <div class="lp-card-header">
        <h2 class="lp-card-title"><i class="fas fa-list-alt"></i> Transaction History</h2>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;padding:0 22px 18px;">
        <div style="flex:1;min-width:220px;">
            <label style="display:block;font-size:.8rem;font-weight:600;color:#6c757d;margin-bottom:6px;">Search</label>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Client or consultation code"
                style="width:100%;height:42px;padding:0 14px;border:1px solid #d9e2ef;border-radius:8px;font-size:.92rem;color:#1e2d4d;">
        </div>
        <div style="min-width:180px;">
            <label style="display:block;font-size:.8rem;font-weight:600;color:#6c757d;margin-bottom:6px;">Status</label>
            <select wire:model.live="status" style="width:100%;height:42px;padding:0 14px;border:1px solid #d9e2ef;border-radius:8px;font-size:.92rem;color:#1e2d4d;background:#fff;">
                <option value="">All statuses</option>
                <option value="paid">Paid</option>
                <option value="downpayment_paid">Paid (Down)</option>
                <option value="pending">Pending</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
        <div style="min-width:180px;">
            <label style="display:block;font-size:.8rem;font-weight:600;color:#6c757d;margin-bottom:6px;">Payment Type</label>
            <select wire:model.live="type" style="width:100%;height:42px;padding:0 14px;border:1px solid #d9e2ef;border-radius:8px;font-size:.92rem;color:#1e2d4d;background:#fff;">
                <option value="">All types</option>
                <option value="downpayment">Downpayment 50%</option>
                <option value="balance">Balance 50%</option>
                <option value="full">Full</option>
            </select>
        </div>
        <div>
            <button type="button" wire:click="clearFilters" class="lp-btn-review" style="height:42px;padding:0 16px;display:inline-flex;align-items:center;">
                Clear
            </button>
        </div>
    </div>

    <div wire:loading.delay style="padding:0 22px 14px;font-size:.85rem;color:#6c757d;">
        Updating transactions...
    </div>

    {{-- Filtered subtotal bar — only when a filter is active --}}
    @if(($search !== '' || $status !== '' || $type !== '') && ($filteredNet > 0 || $filteredPending > 0))
    <div style="margin:0 22px 16px;padding:12px 18px;background:#f0f7ff;border:1px solid #bfdbfe;border-radius:10px;display:flex;gap:24px;flex-wrap:wrap;font-size:.85rem;">
        @if($filteredNet > 0)
        <span><i class="fas fa-check-circle" style="color:#16a34a;margin-right:5px;"></i>
            <strong>Filtered Net Earned:</strong> &#8369;{{ number_format($filteredNet, 2) }}
        </span>
        @endif
        @if($filteredPending > 0)
        <span><i class="fas fa-hourglass-half" style="color:#f59e0b;margin-right:5px;"></i>
            <strong>Filtered Pending:</strong> &#8369;{{ number_format($filteredPending, 2) }}
        </span>
        @endif
    </div>
    @endif

    @if($payments->isEmpty())
        <div class="lp-empty"><i class="fas fa-receipt"></i><p>No transactions found</p></div>
    @else
        <div style="overflow-x:auto;">
            <table class="lp-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Consultation</th>
                        <th>Type</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Firm Cut</th>
                        <th>Your Net</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                    @php
                        $typeLabels  = ['downpayment' => 'Downpayment 50%', 'balance' => 'Balance 50%', 'full' => 'Full'];
                        $typeLabel   = $typeLabels[$p->type] ?? ucfirst($p->type ?? 'full');
                        $statusLabel = $p->status === 'downpayment_paid' ? 'Paid (Down)' : ucfirst(str_replace('_',' ',$p->status));
                        $consType    = $p->consultation?->type ?? null;
                        $isPending   = $p->status === 'pending';
                    @endphp
                    <tr wire:key="payment-row-{{ $p->id }}">
                        <td>{{ $p->created_at->format('M j, Y') }}</td>
                        <td>
                            <div class="lp-table-client">
                                <div class="lp-tc-avatar">{{ strtoupper(substr($p->client->name, 0, 1)) }}</div>
                                {{ $p->client->name }}
                            </div>
                        </td>
                        <td>{{ $p->consultation ? $p->consultation->code : '—' }}</td>
                        <td>
                            @if($consType === 'video')
                                <span style="font-size:.78rem;background:#eff6ff;color:#1d4ed8;padding:3px 9px;border-radius:20px;font-weight:600;white-space:nowrap;"><i class="fas fa-video" style="margin-right:3px;"></i>Video</span>
                            @elseif($consType === 'in_person' || $consType === 'in-person')
                                <span style="font-size:.78rem;background:#f0fdf4;color:#15803d;padding:3px 9px;border-radius:20px;font-weight:600;white-space:nowrap;"><i class="fas fa-handshake" style="margin-right:3px;"></i>In-Person</span>
                            @elseif($consType)
                                <span style="font-size:.78rem;background:#f8f9fa;color:#6c757d;padding:3px 9px;border-radius:20px;font-weight:600;">{{ ucfirst($consType) }}</span>
                            @else
                                <span style="color:#adb5bd;">—</span>
                            @endif
                        </td>
                        <td><span class="lp-type-badge">{{ $typeLabel }}</span></td>
                        <td style="font-weight:700;color:#1e2d4d;">&#8369;{{ number_format($p->amount, 2) }}</td>
                        <td style="color:#e07b00;">{!! $p->firm_cut > 0 ? '&#8369;' . number_format($p->firm_cut, 2) : '&mdash;' !!}</td>
                        <td style="font-weight:700;color:#1a6a2e;">&#8369;{{ number_format($p->lawyer_net ?? ($p->amount - ($p->firm_cut ?? 0)), 2) }}</td>
                        <td>
                            @if($isPending)
                                <span class="lp-pay-badge pending" title="Awaiting client payment">
                                    Awaiting Payment
                                </span>
                            @else
                                <span class="lp-pay-badge {{ $p->status }}">{{ $statusLabel }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->hasPages())
        @php
            $currentPage = $payments->currentPage();
            $lastPage    = $payments->lastPage();
            $startPage   = max(1, $currentPage - 2);
            $endPage     = min($lastPage, $currentPage + 2);
        @endphp
        <div style="margin-top:18px;padding:0 22px 20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                <div style="font-size:.85rem;color:#6c757d;">
                    Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                    <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
                        @disabled($payments->onFirstPage())
                        style="height:36px;padding:0 12px;border:1px solid #d9e2ef;border-radius:8px;background:{{ $payments->onFirstPage() ? '#f8fafc' : '#fff' }};color:{{ $payments->onFirstPage() ? '#9aa4b2' : '#1e2d4d' }};font-weight:600;">
                        Previous
                    </button>
                    @if($startPage > 1)
                        <button type="button" wire:click="gotoPage(1)" style="width:36px;height:36px;border:1px solid #d9e2ef;border-radius:8px;background:#fff;color:#1e2d4d;font-weight:600;">1</button>
                        @if($startPage > 2)<span style="color:#94a3b8;">...</span>@endif
                    @endif
                    @for($page = $startPage; $page <= $endPage; $page++)
                        <button type="button" wire:click="gotoPage({{ $page }})"
                            style="width:36px;height:36px;border:1px solid {{ $page === $currentPage ? '#1e2d4d' : '#d9e2ef' }};border-radius:8px;background:{{ $page === $currentPage ? '#1e2d4d' : '#fff' }};color:{{ $page === $currentPage ? '#fff' : '#1e2d4d' }};font-weight:700;">
                            {{ $page }}
                        </button>
                    @endfor
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)<span style="color:#94a3b8;">...</span>@endif
                        <button type="button" wire:click="gotoPage({{ $lastPage }})" style="width:36px;height:36px;border:1px solid #d9e2ef;border-radius:8px;background:#fff;color:#1e2d4d;font-weight:600;">{{ $lastPage }}</button>
                    @endif
                    <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
                        @disabled(!$payments->hasMorePages())
                        style="height:36px;padding:0 12px;border:1px solid #d9e2ef;border-radius:8px;background:{{ $payments->hasMorePages() ? '#fff' : '#f8fafc' }};color:{{ $payments->hasMorePages() ? '#1e2d4d' : '#9aa4b2' }};font-weight:600;">
                        Next
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
