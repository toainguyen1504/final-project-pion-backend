 <div class="modal fade" id="modalAddCategory" tabindex="-1" aria-labelledby="modalAddCategoryLabel" aria-hidden="true">
     <div class="modal-dialog" style="margin-top: 80px;">
         <div class="modal-content">
             <form id="quickAddCategoryForm" action="{{ route('admin.categories.store') }}" method="POST">
                 @csrf

                 @if (!empty($fromPostCreate))
                     <input type="hidden" name="from_post_create" value="1">
                 @endif

                 <div class="modal-header">
                     <h5 class="modal-title" id="modalAddCategoryLabel">Thêm danh mục</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                 </div>

                 <div class="modal-body">
                     <div class="alert alert-danger mt-2 d-none" id="quickAddError"></div>
                     @if ($errors->category->any())
                         <div class="alert alert-danger mb-3">
                             <ul class="mb-0 small">
                                 @foreach ($errors->category->all() as $error)
                                     <li>{{ $error }}</li>
                                 @endforeach
                             </ul>
                         </div>
                     @endif

                     <div class="mb-5">
                         <label for="name" class="form-label">Tên danh mục</label>
                         <input id="name" type="text" name="name" class="form-control" required
                             maxlength="50">
                     </div>

                 </div>
                 <div class="modal-footer py-2">
                     <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Hủy</button>
                     <button type="submit" class="btn btn-dark px-5">Lưu</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
