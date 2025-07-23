<div class="template-classic px-4 py-5">
    <h2 class="text-primary fw-bold">{{ $news->title }}</h2>
    <small class="text-muted">{{ optional($news->category)->name }}</small>
    <hr>
    {!! $news->content->content_html ?? '' !!}
    <p class="text-end fst-italic">Đăng lúc: {{ $news->created_at->format('d/m/Y') }}</p>
</div>
