@push('styles')
<style>
    .medo-pseo-wrap {
        background: #f6f8fb;
        padding: 34px 0 46px;
    }
    .medo-pseo-header,
    .medo-pseo-panel,
    .medo-job-row {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }
    .medo-pseo-header {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 220px;
        gap: 22px;
        align-items: start;
        padding: 24px;
        margin-bottom: 22px;
    }
    .medo-pseo-header h1 {
        margin: 0 0 10px;
        color: #111827;
        font-size: 32px;
        line-height: 1.2;
        letter-spacing: 0;
    }
    .medo-pseo-header p,
    .medo-pseo-panel p,
    .medo-job-row p {
        color: #4b5563;
    }
    .medo-eyebrow {
        margin: 0 0 7px;
        color: #0f766e;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .medo-stat {
        border-left: 4px solid #0f766e;
        padding-left: 16px;
    }
    .medo-stat span {
        display: block;
        color: #64748b;
        font-size: 13px;
    }
    .medo-stat strong {
        display: block;
        color: #111827;
        font-size: 36px;
        line-height: 1.05;
        margin: 4px 0;
    }
    .medo-pseo-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }
    .medo-pseo-panel {
        padding: 18px;
        margin-bottom: 16px;
    }
    .medo-pseo-panel h2,
    .medo-pseo-panel h3 {
        margin: 0 0 12px;
        color: #111827;
        font-size: 21px;
        line-height: 1.25;
        letter-spacing: 0;
    }
    .medo-pseo-panel ul,
    .medo-job-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .medo-link-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .medo-link-list a {
        display: block;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #111827;
        background: #fff;
        font-weight: 700;
    }
    .medo-link-list a:hover {
        color: #0f766e;
        text-decoration: none;
        border-color: #99d5ca;
    }
    .medo-job-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 18px;
        margin-bottom: 12px;
    }
    .medo-job-row h3 {
        margin: 0 0 7px;
        font-size: 20px;
        line-height: 1.25;
        letter-spacing: 0;
    }
    .medo-job-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .medo-pill {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #edf7f5;
        color: #0f766e;
        font-size: 13px;
        font-weight: 700;
    }
    .medo-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 8px 16px;
        border-radius: 6px;
        background: #0f766e;
        color: #fff;
        font-weight: 700;
        white-space: nowrap;
    }
    .medo-button:hover {
        color: #fff;
        background: #115e59;
        text-decoration: none;
    }
    .medo-muted-box {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 18px;
        color: #475569;
        background: #f8fafc;
    }
    .medo-breadcrumbs {
        margin-bottom: 14px;
        color: #64748b;
        font-size: 14px;
    }
    .medo-breadcrumbs a {
        color: #0f766e;
        font-weight: 700;
    }
    @media (max-width: 767px) {
        .medo-pseo-header,
        .medo-pseo-grid,
        .medo-link-list,
        .medo-job-row {
            grid-template-columns: 1fr;
            display: block;
        }
        .medo-pseo-header h1 {
            font-size: 26px;
        }
        .medo-stat {
            margin-top: 18px;
        }
        .medo-button {
            width: 100%;
            margin-top: 12px;
        }
    }
</style>
@endpush
