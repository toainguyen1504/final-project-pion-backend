@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h2 class="fw-bold mb-4">📰 Chọn giao diện bài viết</h2>

        <div class="row">
            @foreach ($templates as $template)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 {{ $template->css_class }}">
                        <!-- Hoặc click vào ảnh -->
                        {{-- <img src="{{ $template->previewImage->path }}" class="card-img-top cursor-pointer"
                                data-bs-toggle="modal" data-bs-target="#templateModal-{{ $template->id }}"> --}}
                        @if ($template->previewImage)
                            <img src="{{ $template->previewImage->path }}" class="card-img-top" alt="{{ $template->name }}">
                        @else
                            <div class="bg-secondary text-white text-center py-5">
                                <span class="fw-semibold">Không có ảnh preview</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $template->name }}</h5>
                            {{-- <h5 class="card-title text-primary cursor-pointer" data-bs-toggle="modal"
                                data-bs-target="#templateModal-{{ $template->id }}">
                                {{ $template->name }}
                            </h5> --}}
                            <p class="text-muted mb-3">Slug: <code>{{ $template->slug }}</code></p>

                            <a href="{{ route('admin.posts.create', ['template_id' => $template->id]) }}"
                                class="btn btn-dark mt-auto">
                                Sử dụng mẫu này
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- <div class="modal fade" id="templateModal-{{ $template->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $template->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @foreach ($template->previewImages as $image)
                        <img src="{{ $image->path }}" class="img-fluid mb-3 rounded" alt="Preview">
                    @endforeach

                    <p class="text-muted">Mã giao diện: <code>{{ $template->slug }}</code></p>
                    <p>Mô tả: {{ $template->description ?? 'Không có mô tả' }}</p>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('admin.posts.create', ['template_id' => $template->id]) }}" class="btn btn-primary">
                        Sử dụng mẫu này
                    </a>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
