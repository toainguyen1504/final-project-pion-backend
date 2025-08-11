@extends('layouts.app')

@section('title', ' - Quản lý tư vấn')

@section('content')
    <div class="container-fluid py-3">
        {{-- <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td> --}}
        <h2 class="mb-3">Danh sách tư vấn</h2>

        <div class="table-responsive">
            <table class="table table-bordered align-middle table-striped table-hover" id="consultations-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="min-width: 40px;">STT</th>
                        <th class="text-center" style="min-width: 196px;">Người gửi</th>
                        <th class="text-center" style="min-width: 100px;">Phone</th>
                        <th class="text-center" style="min-width: 200px;">Email</th>
                        <th style="min-width: 240px;">Nội dung cần tư vấn</th>
                        <th class="text-center" style="min-width: 140px;">Trạng thái</th>
                        <th class="text-center" style="min-width: 160px;">Thời gian</th>
                        {{-- <th class="text-center" style="min-width: 160px;">Cập nhật</th> --}}
                        <th class="text-center" style="min-width: 160px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($consultations as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center text-truncate" style="max-width: 184px;"
                                title="{{ $item->user_name ?? $item->guest_name }}">
                                {{ $item->user_name ?? $item->guest_name }}</td>
                            <td class="text-center">{{ $item->user_phone ?? $item->guest_phone }}</td>
                            <td class="text-center">{{ $item->user_email ?? $item->guest_email }}</td>
                            {{-- <td>{{ $item->request_content }}</td> --}}
                            <td class="text-truncate" style="max-width: 224px;" title="{{ $item->request_content }}">
                                {{ $item->request_content }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $item->status === 'approved' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                            {{-- <td class="text-center">{{ $item->updated_at->format('d/m/Y H:i:s') }}</td> --}}

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a href="#" class="btn btn-dark btn-sm py-1 px-3 btn-view" data-bs-toggle="modal"
                                        data-bs-target="#consultationModal"
                                        data-sender="{{ $item->user_name ?? $item->guest_name }}"
                                        data-phone="{{ $item->user_phone ?? $item->guest_phone }}"
                                        data-email="{{ $item->user_email ?? $item->guest_email }}"
                                        data-content="{{ $item->request_content }}"
                                        data-status="{{ ucfirst($item->status) }}"
                                        data-created="{{ $item->created_at->format('d/m/Y H:i:s') }}">
                                        Xem chi tiết
                                    </a>

                                    {{-- <a href="#" class="btn btn-info btn-sm py-1 px-3">Duyệt</a> --}}
                                    {{-- <a href="#" class="btn btn-danger btn-sm">Xóa</a> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                <span>Chưa có yêu cầu tư vấn nào.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal view -->
    <div class="modal fade" id="consultationModal" tabindex="-1" aria-labelledby="consultationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">Chi tiết tư vấn</h5>
                    <button type="button" class="btn btn-icon-only btn-close-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Người gửi:</strong> <span id="modalSender"></span></p>
                    <p><strong>Số điện thoại:</strong> <span id="modalPhone"></span></p>
                    <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                    <p><strong>Nội dung cần tư vấn:</strong></p>
                    <div class="border rounded p-2 bg-light" id="modalContent"></div>
                    <p class="mt-3"><strong>Trạng thái:</strong> <span id="modalStatus"></span></p>
                    <p><strong>Thời gian:</strong> <span id="modalCreated"></span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Script Categories DataTables --}}
    <script>
        $.fn.dataTable.ext.errMode = 'none'; //off warming when no data

        $(document).ready(function() {
            var table = $('#consultations-table').DataTable({
                scrollX: true,

                dom: '<"top"Blfr>t<"bottom"ip>',
                buttons: [{
                        extend: 'collection',
                        text: '<i class="fas fa-file-export me-1"></i>&nbsp;Xuất dữ liệu',
                        buttons: [{
                                extend: 'copy',
                                title: 'Danh sách tư vấn',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                }
                            },
                            {
                                extend: 'excel',
                                title: 'Danh sách tư vấn',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                }
                            },
                            {
                                extend: 'pdf',
                                title: 'Danh sách tư vấn',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                }
                            }
                        ]
                    },
                    // {
                    //     extend: 'colvis',
                    //     text: '<i class="fas fa-columns me-1"></i>&nbsp;Tùy chỉnh cột'
                    // },
                    {
                        text: '<i class="fas fa-list-ol me-1"></i>&nbsp;Số dòng',
                        extend: 'collection',
                        autoClose: true,
                        buttons: [{
                                text: '4 dòng',
                                action: function() {
                                    table.page.len(4).draw();
                                }
                            },
                            {
                                text: '6 dòng',
                                action: function() {
                                    table.page.len(6).draw();
                                }
                            },
                            {
                                text: '12 dòng',
                                action: function() {
                                    table.page.len(12).draw();
                                }
                            },
                            {
                                text: 'Tất cả',
                                action: function() {
                                    table.page.len(-1).draw();
                                }
                            }
                        ]
                    }
                ],
                fixedColumns: {
                    leftColumns: 2,
                    rightColumns: 1
                },
                pageLength: 6,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json',
                    info: "Đang hiển thị _START_ - _END_ trong tổng số _TOTAL_ dữ liệu",
                    infoEmpty: "Không có dữ liệu để hiển thị",
                    infoFiltered: "(lọc từ _MAX_ dữ liệu)"
                },
                paging: true,
                lengthChange: false, // hidden lengthMenu default
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: false // off responsive to avoid conflict scrollX + FixedColumns
            });
        });

        // Logic view btn
        $(document).on('click', '.btn-view', function() {
            const button = $(this);

            $('#modalSender').text(button.data('sender'));
            $('#modalPhone').text(button.data('phone'));
            $('#modalEmail').text(button.data('email'));
            $('#modalContent').text(button.data('content'));
            $('#modalStatus').text(button.data('status'));
            $('#modalCreated').text(button.data('created'));
            $('#modalUpdated').text(button.data('updated'));
        });
    </script>

@endsection
