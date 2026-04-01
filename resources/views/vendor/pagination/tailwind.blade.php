@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div style="display:flex;align-items:center;justify-content:center;gap:0;margin-top:8px;">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" style="display:inline-flex;align-items:center;padding:6px 10px;background:#fff;border:1px solid #d1d5db;border-right:none;border-radius:6px 0 0 6px;cursor:not-allowed;color:#9ca3af;">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" style="display:inline-flex;align-items:center;padding:6px 10px;background:#fff;border:1px solid #d1d5db;border-right:none;border-radius:6px 0 0 6px;color:#6b7280;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="display:inline-flex;align-items:center;padding:6px 12px;background:#fff;border:1px solid #d1d5db;border-left:none;font-size:13px;color:#374151;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="display:inline-flex;align-items:center;padding:6px 12px;background:#e5e7eb;border:1px solid #d1d5db;border-left:none;font-size:13px;font-weight:600;color:#374151;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" style="display:inline-flex;align-items:center;padding:6px 12px;background:#fff;border:1px solid #d1d5db;border-left:none;font-size:13px;color:#374151;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" style="display:inline-flex;align-items:center;padding:6px 10px;background:#fff;border:1px solid #d1d5db;border-left:none;border-radius:0 6px 6px 0;color:#6b7280;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" style="display:inline-flex;align-items:center;padding:6px 10px;background:#fff;border:1px solid #d1d5db;border-left:none;border-radius:0 6px 6px 0;cursor:not-allowed;color:#9ca3af;">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif

        </div>

        <p style="text-align:center;font-size:.82rem;color:#6b7280;margin-top:8px;">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} lawyers
        </p>

    </nav>
@endif
