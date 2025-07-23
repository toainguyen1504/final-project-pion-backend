<div class="template-minimal py-5">
    {{-- phù hợp nội dung sạch, ít hình. --}}
    <h1 class="fw-semibold">{{ $news->title }}</h1>
    {!! $news->content->content_html ?? '' !!}
</div>
