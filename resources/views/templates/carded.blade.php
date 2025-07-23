<div class="template-carded card shadow-sm p-4">
    {{-- bài tuyển dụng hoặc blog profile. --}}
    <div class="card-body">
        <h3 class="card-title">{{ $news->title }}</h3>
        <p class="text-muted">{{ optional($news->category)->name }}</p>
        {!! $news->content->content_html ?? '' !!}
    </div>
</div>
