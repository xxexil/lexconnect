@if ($paginator->hasPages())
    @once
        <style>
            .lc-pagination {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 20px;
                flex-wrap: wrap;
                margin: 0;
            }

            .lc-pagination-btn,
            .lc-pagination-status {
                min-height: 42px;
                border-radius: 12px;
                font-size: 0.95rem;
            }

            .lc-pagination-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                min-width: 140px;
                padding: 0 20px;
                border: 1px solid #d9e0eb;
                background: #ffffff;
                color: #1e2d4d;
                font-weight: 700;
                text-decoration: none;
                box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
                transition: border-color 0.18s ease, box-shadow 0.18s ease, color 0.18s ease;
            }

            .lc-pagination-btn:hover {
                border-color: #b9c6da;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
                color: #10203f;
                text-decoration: none;
            }

            .lc-pagination-btn[aria-disabled="true"] {
                color: #a8b1c2;
                border-color: #e7ebf2;
                background: #f8fafc;
                box-shadow: none;
                pointer-events: none;
            }

            .lc-pagination-icon {
                font-size: 1rem;
                line-height: 1;
            }

            .lc-pagination-status {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #52627b;
                font-weight: 500;
                white-space: nowrap;
            }

            @media (max-width: 640px) {
                .lc-pagination {
                    gap: 12px;
                }

                .lc-pagination-btn {
                    flex: 1 1 calc(50% - 6px);
                    min-width: 0;
                    padding: 0 16px;
                }

                .lc-pagination-status {
                    order: -1;
                    width: 100%;
                }
            }
        </style>
    @endonce

    <nav class="lc-pagination" role="navigation" aria-label="Pagination Navigation">
        @if ($paginator->onFirstPage())
            <span class="lc-pagination-btn" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="lc-pagination-icon" aria-hidden="true">&#8249;</span>
                <span>Previous</span>
            </span>
        @else
            <a class="lc-pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                <span class="lc-pagination-icon" aria-hidden="true">&#8249;</span>
                <span>Previous</span>
            </a>
        @endif

        <div class="lc-pagination-status">
            {{ number_format($paginator->firstItem() ?? 0) }}-{{ number_format($paginator->lastItem() ?? 0) }} of {{ number_format($paginator->total()) }}
        </div>

        @if ($paginator->hasMorePages())
            <a class="lc-pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                <span>Next</span>
                <span class="lc-pagination-icon" aria-hidden="true">&#8250;</span>
            </a>
        @else
            <span class="lc-pagination-btn" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span>Next</span>
                <span class="lc-pagination-icon" aria-hidden="true">&#8250;</span>
            </span>
        @endif
    </nav>
@endif
