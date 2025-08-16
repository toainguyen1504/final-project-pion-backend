<div class="template-minimal py-5">
    {{-- phù hợp nội dung sạch, ít hình. --}}
    <h1 class="fw-semibold">{{ $posts->title }}</h1>
    {!! $posts->content->content_html ?? '' !!}
</div>
