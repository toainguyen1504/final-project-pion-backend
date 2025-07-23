<section class="template-landing p-5 bg-gradient text-light text-center">
    {{-- Tin bài giới thiệu khóa học, du học, workshop, Bài viết cần thu hút đăng ký hoặc chuyển đổi, cần Tạo CTA mạnh --}}
    <h1 class="fw-bold">{{ $news->title }}</h1>
    <p class="lead mb-3">{{ optional($news->category)->name }}</p>

    @if ($news->images()->where('type', 'post_thumbnail')->first())
        <img src="{{ $news->images()->where('type', 'post_thumbnail')->first()->path }}" class="img-fluid mb-4"
            alt="Banner">
    @endif

    {!! $news->content->content_html ?? '' !!}

    <a href="/tuvan" class="btn btn-warning mt-4">Yêu cầu tư vấn</a>
</section>
