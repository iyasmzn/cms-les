{{--
    Shared rich prose typography for public detail pages (program / event / etc).
    Themed on --primary. Tuned for comfortable long-form reading:
    larger body text, generous line-height, constrained measure via .article-prose wrapper.
--}}
<style>
    .article-prose {
        color: #374151;
        font-size: 1.0625rem;
        line-height: 1.85;
    }
    .article-prose > *:first-child { margin-top: 0; }

    .article-prose h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #111827;
        line-height: 1.3;
        margin-top: 2.75rem;
        margin-bottom: 1rem;
        padding-bottom: .5rem;
        border-bottom: 2px solid var(--primary-200);
        display: inline-block;
    }
    .article-prose h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 2rem;
        margin-bottom: .75rem;
    }
    .article-prose h4 {
        font-size: 1.0625rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 1.5rem;
        margin-bottom: .5rem;
    }
    .article-prose p { margin-bottom: 1.375rem; }
    .article-prose ul, .article-prose ol {
        padding-left: 1.75rem;
        margin-bottom: 1.375rem;
    }
    .article-prose ul { list-style: disc; }
    .article-prose ol { list-style: decimal; }
    .article-prose li { margin-bottom: .5rem; line-height: 1.75; }
    .article-prose li::marker { color: var(--primary-400); }

    .article-prose blockquote {
        border-left: 4px solid var(--primary);
        padding: .875rem 1.25rem;
        margin: 2rem 0;
        background: var(--primary-50);
        border-radius: 0 .75rem .75rem 0;
        color: var(--primary-900);
        font-style: italic;
        font-size: 1.05rem;
    }
    .article-prose blockquote p:last-child { margin-bottom: 0; }

    .article-prose a {
        color: var(--primary);
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-thickness: 1.5px;
        font-weight: 500;
    }
    .article-prose a:hover { color: var(--primary-700); }

    .article-prose strong { color: #111827; font-weight: 700; }
    .article-prose em { font-style: italic; }

    .article-prose img {
        border-radius: .875rem;
        margin: 2rem auto;
        max-width: 100%;
        height: auto;
        box-shadow: 0 8px 28px rgba(0,0,0,.1);
    }
    .article-prose figure { margin: 2rem 0; }
    .article-prose figure img { margin: 0 auto; }
    .article-prose figcaption {
        text-align: center;
        font-size: .85rem;
        color: #9ca3af;
        margin-top: .625rem;
        font-style: italic;
    }

    .article-prose code {
        background: var(--primary-50);
        padding: .15rem .4rem;
        border-radius: .3rem;
        font-size: .9em;
        color: var(--primary-800);
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    }
    .article-prose pre {
        background: #111827;
        color: #f9fafb;
        padding: 1.125rem 1.375rem;
        border-radius: .75rem;
        overflow-x: auto;
        margin: 1.75rem 0;
        font-size: .9rem;
        line-height: 1.7;
    }
    .article-prose pre code { background: none; color: inherit; padding: 0; }

    .article-prose table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.75rem 0;
        font-size: .9375rem;
        display: block;
        overflow-x: auto;
    }
    .article-prose th {
        background: var(--primary-50);
        color: var(--primary-800);
        font-weight: 700;
        padding: .65rem 1rem;
        text-align: left;
        border-bottom: 2px solid var(--primary-200);
    }
    .article-prose td {
        padding: .65rem 1rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }
    .article-prose tr:last-child td { border-bottom: none; }

    .article-prose hr {
        border: 0;
        border-top: 1px solid var(--border);
        margin: 2.5rem 0;
    }

    .article-prose iframe {
        width: 100%;
        aspect-ratio: 16 / 9;
        border-radius: .875rem;
        margin: 2rem 0;
        border: 0;
    }

    /* Lead paragraph shown above the article body */
    .article-lead {
        font-size: 1.1875rem;
        line-height: 1.7;
        color: #4b5563;
        font-weight: 500;
        margin-bottom: 2rem;
        padding-left: 1.125rem;
        border-left: 4px solid var(--primary-300);
    }

    @media (min-width: 640px) {
        .article-prose { font-size: 1.125rem; }
        .article-prose h2 { font-size: 1.65rem; }
    }
</style>
