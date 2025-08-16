<div class="template-carded card shadow-sm p-4">
    {{-- bài tuyển dụng hoặc blog profile. --}}
    <div class="card-body">
        <h3 class="card-title">{{ $posts->title }}</h3>
        <p class="text-muted">{{ optional($posts->category)->name }}</p>
        {!! $posts->content->content_html ?? '' !!}
    </div>
</div>
