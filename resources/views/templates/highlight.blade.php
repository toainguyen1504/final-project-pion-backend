<article class="template-highlight p-5 border-start border-5 border-warning">
    {{-- Nhấn mạnh – phù hợp cho tin hot hoặc thông báo đặc biệt. --}}
    <h2 class="text-warning fw-bold">{{ $news->title }}</h2>
    <p class="text-muted">{{ optional($news->category)->name }}</p>
    {!! $news->content->content_html ?? '' !!}
    <div class="text-end mt-4">
        <span class="badge bg-warning text-dark">Nổi bật</span>
    </div>
</article>

