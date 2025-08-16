<main class="template-longread p-4 container">
    {{-- Layout thích hợp cho nội dung dài, bài viết dạng chuyên sâu hoặc chia sẻ kiến thức. --}}
    <header class="mb-4">
        <h1 class="fw-bold">{{ $posts->title }}</h1>
        <p class="text-muted">{{ optional($posts->category)->name }}</p>
    </header>

    <div class="content-body fs-5 lh-lg">
        {!! $posts->content->content_html ?? '' !!}
    </div>

    <footer class="mt-5 text-end text-muted fst-italic">
        Xuất bản: {{ $posts->created_at->format('d/m/Y') }}
    </footer>
</main>
