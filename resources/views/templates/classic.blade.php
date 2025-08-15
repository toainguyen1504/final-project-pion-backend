<div class="template-classic px-4 py-5">
    <h2 class="text-primary fw-bold">{{ $posts->title }}</h2>
    <small class="text-muted">{{ optional($posts->category)->name }}</small>
    <hr>
    {!! $posts->content->content_html ?? '' !!}
    <p class="text-end fst-italic">Đăng lúc: {{ $posts->created_at->format('d/m/Y') }}</p>
</div>
