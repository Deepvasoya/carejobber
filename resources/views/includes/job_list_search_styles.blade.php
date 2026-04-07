@push('styles')
    <style type="text/css">
        .searchList li .jobimg {

            min-height: 80px;

        }

        .hide_vm_ul {

            height: 100px;

            overflow: hidden;

        }

        .hide_vm {

            display: none !important;

        }

        .view_more {

            cursor: pointer;

        }

        .view_less {

            cursor: pointer;

        }

        .job-card-highlighted {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
            border: 1px solid #f59e0b !important;
            box-shadow: 0 2px 12px rgba(245, 158, 11, 0.12);
        }

        .promotepof-badge.job-urgent-badge {
            background: #dc2626 !important;
            left: 10px;
            right: auto;
        }

        /* Narrow list lives inside normal Bootstrap col (avoid width:auto on col — breaks flex + width:100% child) */
        .job-search-results-narrow {

            width: 100%;
        }

        .job-search-results-narrow>h3 {
            font-size: 1.1rem;
            margin-bottom: 0.35rem;
        }

        .job-search-results-narrow .topstatinfo {
            font-size: 0.8rem;
            margin-bottom: 0.5rem !important;
            color: #64748b;
        }

        .job-search-pagi .pagination {
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 0.2rem;
            margin-bottom: 0;
            font-size: 0.8rem;
        }

        .job-search-pagi .page-link {
            padding: 0.25rem 0.45rem;
        }

        ul.featuredlist.row.job-search-list-single {
            --bs-gutter-x: 0.65rem;
            --bs-gutter-y: 0.5rem;
            margin-left: 0;
            margin-right: 0;
        }

        ul.featuredlist.row.job-search-list-single>li {
            padding-left: 0;
            padding-right: 0;
        }

        .job-list-card-enhanced {
            position: relative;
            height: auto;
            display: flex;
            flex-direction: column;
            padding: 1.1rem 1.15rem 1rem;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .job-list-card-compact {
            padding: 0.55rem 0.65rem 0.5rem;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(15, 23, 42, 0.05);
        }

        .job-list-card-compact .job-list-card-top {
            margin-bottom: 0.35rem;
            min-height: 0;
        }

        .job-list-card-compact .job-list-pill {
            font-size: 0.62rem;
            padding: 0.15rem 0.4rem;
        }

        .job-list-card-compact .job-list-save-btn {
            width: 1.85rem;
            height: 1.85rem;
        }

        .job-list-card-compact .job-list-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 0.4rem;
        }

        .job-list-card-compact .job-list-card-title a {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .job-list-card-compact .job-list-card-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.35rem 0.75rem;
            margin: 0 0 0.45rem;
            padding: 0;
            font-size: 0.88rem;
            grid-template-columns: unset;
        }

        .job-list-card-compact .job-list-meta-row {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.2rem 0.35rem;
            margin: 0;
        }

        .job-list-card-compact .job-list-meta-row-full {
            flex-basis: 100%;
        }

        .job-list-card-compact .job-list-card-meta dt {
            font-size: 0.74rem;
            margin-bottom: 0;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 600;
            color: #475569;
        }

        .job-list-card-compact .job-list-card-meta dt i {
            margin-right: 0.15rem;
            font-size: 0.78rem;
            color: #64748b;
        }

        .job-list-card-compact .job-list-card-meta dd {
            line-height: 1.45;
            margin: 0;
            color: #1e293b;
            font-size: 0.88rem;
        }

    .job-list-card-compact .job-list-card-main {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 0.6rem;
        min-width: 0;
    }

    .job-list-card-compact .job-list-card-body {
        flex: 1;
        min-width: 0;
    }

    .job-list-card-compact .job-list-card-company {
        padding-top: 0.45rem;
        margin-top: 0;
        margin-bottom: 0;
        border-top: 1px solid #f1f5f9;
        gap: 0.5rem;
    }

        .job-list-card-compact .job-list-company-logo img {
            max-height: 32px;
            max-width: 32px;
            border-radius: 6px;
        }

        .job-list-card-compact .job-list-company-name {
            font-size: 0.82rem;
        }

        .job-list-card-compact .job-list-posted {
            font-size: 0.72rem;
            margin-top: 0;
        }

    .job-list-card-compact .job-list-card-actions--stack {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        align-items: stretch;
        justify-content: center;
        gap: 0.35rem;
        margin: 0;
        padding: 0;
        align-self: center;
        min-width: 6.75rem;
    }

    .job-list-card-compact .job-list-card-actions--stack .job-list-apply {
        width: 100%;
        justify-content: center;
        text-align: center;
        box-sizing: border-box;
    }

    .job-list-card-compact .job-list-apply {
        padding: 0.35rem 0.65rem;
        font-size: 0.78rem;
        border-radius: 6px;
    }

    @media (max-width: 575.98px) {
        .job-list-card-compact .job-list-card-main {
            flex-direction: column;
            align-items: stretch;
        }

        .job-list-card-compact .job-list-card-actions--stack {
            flex-direction: row;
            flex-wrap: wrap;
            align-self: stretch;
            width: 100%;
            min-width: 0;
            justify-content: flex-start;
        }

        .job-list-card-compact .job-list-card-actions--stack .job-list-apply {
            width: auto;
            flex: 1 1 auto;
            min-width: 0;
        }
    }

        .job-list-card-compact .job-list-meta-row-full dd {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .job-list-card-enhanced:hover {
            border-color: #c7d2fe;
            box-shadow: 0 8px 24px rgba(37, 87, 167, 0.12);
        }

        .job-list-card-applied {
            border-color: #86efac !important;
            background: linear-gradient(180deg, #f0fdf4 0%, #fff 55%) !important;
        }

        .job-list-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.65rem;
            min-height: 1.75rem;
        }

        .job-list-card-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            flex: 1;
            min-width: 0;
        }

        .job-list-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            line-height: 1.2;
        }

        .job-list-pill-urgent {
            background: #fee2e2;
            color: #b91c1c;
        }

        .job-list-pill-featured {
            background: #fef3c7;
            color: #92400e;
        }

        .job-list-pill-highlight {
            background: #e0f2fe;
            color: #0369a1;
        }

        .job-list-pill-applied {
            background: #d1fae5;
            color: #047857;
        }

        .job-list-pill-expired {
            font-size: 0.65rem;
            background: #f3f4f6;
            color: #6b7280;
            text-transform: none;
            font-weight: 600;
        }

        .job-list-save-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
            color: #64748b;
            background: #f8fafc;
            text-decoration: none;
            flex-shrink: 0;
            transition: color 0.15s, background 0.15s, border-color 0.15s;
        }

        .job-list-save-btn:hover {
            color: #dc2626;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .job-list-save-btn-active {
            color: #dc2626;
            border-color: #fecaca;
            background: #fff1f2;
        }

        .job-list-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.35;
            margin: 0 0 0.85rem;
        }

        .job-list-card-title a {
            color: #0f172a;
            text-decoration: none;
        }

        .job-list-card-title a:hover {
            color: #2557a7;
        }

        .job-list-card-meta {
            margin: 0 0 1rem;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.55rem 1rem;
            font-size: 0.875rem;
        }

        @media (max-width: 575.98px) {
            .job-list-card-meta {
                grid-template-columns: 1fr;
            }
        }

        .job-list-meta-row {
            margin: 0;
            min-width: 0;
        }

        .job-list-meta-row-full {
            grid-column: 1 / -1;
        }

        .job-list-card-meta dt {
            font-weight: 600;
            color: #475569;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.15rem;
        }

        .job-list-card-meta dt i {
            margin-right: 0.25rem;
            color: #64748b;
            opacity: 1;
        }

        .job-list-card-meta dd {
            margin: 0;
            color: #0f172a;
            line-height: 1.45;
            word-break: break-word;
        }

        .job-list-meta-muted {
            color: #64748b;
            font-weight: 500;
        }

        .job-list-card-company {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-top: 0.75rem;
            margin-top: auto;
            border-top: 1px solid #f1f5f9;
            margin-bottom: 0.85rem;
        }

        .job-list-company-logo img {
            max-height: 44px;
            max-width: 44px;
            border-radius: 8px;
            object-fit: contain;
        }

        .job-list-company-name {
            font-weight: 600;
            color: #2557a7;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
        }

        .job-list-company-name:hover {
            text-decoration: underline;
        }

        .job-list-posted {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .job-list-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }

        .job-list-apply {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            border: 1px solid transparent;
            line-height: 1.2;
        }

        .job-list-apply-primary {
            background: #2557a7;
            color: #fff !important;
            border-color: #2557a7;
        }

        .job-list-apply-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff !important;
        }

        .job-list-apply-secondary {
            background: #fff;
            color: #334155 !important;
            border-color: #e2e8f0;
        }

        .job-list-apply-secondary:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #0f172a !important;
        }

        .job-list-apply-done,
        .job-list-apply-disabled {
            cursor: default;
            background: #f1f5f9;
            color: #64748b !important;
            border-color: #e2e8f0;
        }

        /* Full-screen overlay while apply form submits (above Bootstrap modal, z-modal ~1055) */
        .apply-submit-page-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.48);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
        }

        .apply-submit-page-overlay.is-visible {
            display: flex;
        }

        .apply-submit-page-overlay__card {
            background: #fff;
            border-radius: 20px;
            padding: 2.25rem 2.5rem 2rem;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.28),
                0 0 0 1px rgba(255, 255, 255, 0.08) inset;
            text-align: center;
            max-width: 340px;
            width: 100%;
            animation: apply-submit-card-in 0.35s ease-out;
        }

        @keyframes apply-submit-card-in {
            from {
                opacity: 0;
                transform: scale(0.94) translateY(8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .apply-submit-page-overlay__rings {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 1.35rem;
        }

        .apply-submit-page-overlay__ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #17d27c;
            border-right-color: rgba(23, 210, 124, 0.35);
            animation: apply-submit-orbit 1s linear infinite;
        }

        .apply-submit-page-overlay__ring--delay {
            inset: 8px;
            border-top-color: #2557a7;
            border-right-color: rgba(37, 87, 167, 0.3);
            animation-duration: 1.35s;
            animation-direction: reverse;
        }

        @keyframes apply-submit-orbit {
            to {
                transform: rotate(360deg);
            }
        }

        .apply-submit-page-overlay__title {
            margin: 0 0 0.35rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .apply-submit-page-overlay__hint {
            margin: 0;
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.45;
        }

        ul.home-featured-jobs-2col.job-search-list-single {
            margin-left: 0;
            margin-right: 0;
        }

        ul.home-featured-jobs-2col.job-search-list-single > li {
            padding-left: calc(var(--bs-gutter-x) * 0.5);
            padding-right: calc(var(--bs-gutter-x) * 0.5);
        }
    </style>
@endpush
