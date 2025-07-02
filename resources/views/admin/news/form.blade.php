<form action="{{ $route }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method($method)

    <!-- Các trường form khác giữ nguyên -->

    <div class="mb-3">
        <label for="content" class="form-label">Nội dung chi tiết</label>
        <textarea class="form-control" id="content" name="content">
            {!! $news->content->content ?? old('content') !!}
        </textarea>
    </div>

    <!-- Phần còn lại của form -->
</form>

@section('scripts')
    <script>
        ClassicEditor
            .create(document.querySelector('#content')).catch(error => {
                console.log(error)
            })
    </script>
@endsection
