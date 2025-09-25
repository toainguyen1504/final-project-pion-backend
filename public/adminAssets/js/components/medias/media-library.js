document.addEventListener("DOMContentLoaded", function () {
    const defaultImageUrl =
        document.getElementById("previewImage").dataset.default;
    const openMediaBtn = document.getElementById("openMediaLibrary");
    const mediaModal = new bootstrap.Modal(
        document.getElementById("mediaLibraryModal")
    );
    const libraryTab = document.querySelector("#library-tab");

    // Form metadata
    const form = document.getElementById("editMetadataForm");
    const titleInput = form.querySelector('input[name="title"]');
    const captionInput = form.querySelector('textarea[name="caption"]');
    const descriptionInput = form.querySelector('textarea[name="description"]');
    regex = /^[\p{L}\p{N}\s.,!?'"-—]+$/u;

    let selectedMediaId = null;

    /** ==========================
     *  Helpers
     * ========================== */
    function validateField(input, required = false) {
        const value = input.value.trim();
        const isEmpty = value === "";
        const isValid = regex.test(value);

        if ((required && isEmpty) || (!isEmpty && !isValid)) {
            input.classList.add("is-invalid");
            return false;
        }
        input.classList.remove("is-invalid");
        return true;
    }

    function validateAllFields() {
        const isTitleValid = validateField(titleInput, true);
        const isCaptionValid = validateField(captionInput, true);
        const isDescriptionValid = validateField(descriptionInput, false);

        return isTitleValid && isCaptionValid && isDescriptionValid;
    }

    function formatBytes(bytes) {
        if (typeof bytes !== "number") return null;
        const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
        if (bytes === 0) return "0 Bytes";
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        const value = bytes / Math.pow(1024, i);
        return `${value.toFixed(2)} ${sizes[i]}`;
    }

    function resetPreview() {
        document.getElementById("previewImage").querySelector("img").src =
            defaultImageUrl;
        // selectedMediaId = null;
        form.title.value = "";
        form.caption.value = "";
        form.description.value = "";
        document.getElementById("fileName").textContent = "Đang cập nhật...";
        document.getElementById("uploadDate").textContent = "Đang cập nhật...";
        document.getElementById("fileSize").textContent = "Đang cập nhật...";
        document.getElementById("imageDimensions").textContent =
            "Đang cập nhật...";
    }

    function fillMetadata(media) {
        selectedMediaId = media.id;

        const original = media.meta?.variants?.original || {};

        form.title.value = media.title || "";
        form.caption.value = media.caption || "";
        form.description.value = media.description || "";

        document.getElementById("fileName").textContent =
            media.meta?.filename || "Đang cập nhật...";
        document.getElementById("uploadDate").textContent =
            media.created_at || "Đang cập nhật...";
        document.getElementById("fileSize").textContent =
            formatBytes(original.size) || "Đang cập nhật...";
        document.getElementById("imageDimensions").textContent =
            original.width && original.height
                ? `${original.width} x ${original.height}px`
                : "Đang cập nhật...";
    }

    /** ==========================
     *  Load media list
     * ========================== */
    function loadMediaList() {
        const grid = document.getElementById("mediaGrid");
        grid.innerHTML = "";

        fetch("/api/media")
            .then((res) => res.json())
            .then((data) => {
                const mediaItems = data.data || data;

                if (!mediaItems.length) {
                    grid.innerHTML = `
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-image fa-2x mb-3 d-block"></i>
                        <p class="mb-0">Thư viện chưa có ảnh nào được tải lên.</p>
                    </div>
                `;
                    return;
                }

                mediaItems.forEach((media) => {
                    const col = document.createElement("div");
                    col.className = "col-md-2 media-item";
                    col.dataset.id = media.id;
                    col.dataset.url = media.url;
                    col.innerHTML = `
                    <div class="card h-100 border position-relative">
                        <div class="d-flex align-items-center justify-content-center" style="height: 96px; overflow: hidden;">
                            <img src="${media.url}" class="object-fit-cover" alt="${media.title}" style="max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="selected-overlay d-none">
                            <i class="fa-solid fa-check fa-lg"></i>
                        </div>
                    </div>
                `;
                    grid.appendChild(col);
                });
            })
            .catch((err) => {
                grid.innerHTML = `
                <div class="col-12 text-center text-danger py-5">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                    <p class="mb-0">Không thể tải thư viện ảnh. Vui lòng thử lại sau.</p>
                </div>
            `;
            });
    }

    /** ==========================
     *  Upload media
     * ========================== */
    const input = document.getElementById("mediaUploadInput");
    const chooseBtn = document.getElementById("chooseFileBtn");
    const uploadBtn = document.getElementById("uploadMediaBtn");
    const fileList = document.getElementById("selectedFileList");
    const uploadStatus = document.getElementById("uploadStatus");
    document.getElementById("deleteImageBtn").disabled = true;

    input.addEventListener("change", function () {
        fileList.innerHTML = "";

        if (input.files.length > 0) {
            chooseBtn.classList.add("d-none");
            uploadBtn.classList.remove("d-none");
            Array.from(input.files).forEach((file) => {
                const li = document.createElement("li");
                li.innerHTML = `<i class="fas fa-image me-2 text-secondary"></i> ${file.name}`;
                fileList.appendChild(li);
            });
        } else {
            chooseBtn.classList.remove("d-none");
            uploadBtn.classList.add("d-none");
        }
    });

    uploadBtn.addEventListener("click", function () {
        const files = input.files;
        if (!files.length) {
            showToast("Vui lòng chọn ít nhất một tập tin.", "bg-danger");
            return;
        }

        uploadStatus.classList.remove("d-none");
        uploadBtn.disabled = true; // disable btn to avoid spam

        const formData = new FormData();
        let validFileCount = 0;

        for (let file of files) {
            if (!file.type.startsWith("image/")) {
                showToast(`❌ "${file.name}" không phải là ảnh.`, "bg-danger");
                continue;
            }
            if (file.size > 10 * 1024 * 1024) {
                showToast(`❌ "${file.name}" vượt quá 10MB.`, "bg-danger");
                continue;
            }
            const slug = window.slugify(file.name.replace(/\.[^/.]+$/, ""));
            formData.append("files[]", file);
            formData.append("slugs[]", slug);
            validFileCount++;
        }

        if (validFileCount === 0) {
            uploadStatus.classList.add("d-none");
            uploadBtn.disabled = false;
            return;
        }

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        fetch("/api/media", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: formData,
        })
            .then((res) => {
                if (!res.ok) throw new Error("Upload thất bại.");
                return res.json();
            })
            .then(() => {
                showToast("🎉 Tải lên thành công!", "bg-success");
                loadMediaList();
                input.value = "";
                fileList.innerHTML = "";
                chooseBtn.classList.remove("d-none");
                uploadBtn.classList.add("d-none");
                if (libraryTab) new bootstrap.Tab(libraryTab).show();
            })
            .catch(() => {
                showToast("❌ Có lỗi xảy ra khi tải lên.", "bg-danger");
                chooseBtn.classList.remove("d-none");
                uploadBtn.classList.add("d-none");
            })
            .finally(() => {
                uploadStatus.classList.add("d-none");
                uploadBtn.disabled = false;
            });
    });

    /** ==========================
     *  Click media item
     * ========================== */
    document.addEventListener("click", function (e) {
        const item = e.target.closest(".media-item");
        if (!item) return;

        // Unselect
        if (item.classList.contains("selected")) {
            item.classList.remove("selected");
            resetPreview();
            return;
        }

        // Select new
        document
            .querySelectorAll(".media-item")
            .forEach((el) => el.classList.remove("selected"));
        item.classList.add("selected");
        selectedMediaId = item.dataset.id;

        const imageUrl = item.dataset.url;
        document.getElementById("previewImage").querySelector("img").src =
            imageUrl;

        fetch(`/api/media/${item.dataset.id}`, {
            headers: { Accept: "application/json" },
        })
            .then((res) => {
                if (!res.ok) throw new Error("Không thể lấy metadata.");
                return res.json();
            })
            .then((media) => {
                selectedMediaData = media; // save object
                fillMetadata(media);
                document.getElementById("deleteImageBtn").disabled = false;
            })
            .catch(() =>
                showToast("❌ Không thể tải thông tin ảnh.", "bg-danger")
            );
    });

    /** ==========================
     *  Delete media
     * ========================== */
    document
        .getElementById("deleteImageBtn")
        .addEventListener("click", function () {
            if (!selectedMediaId || !selectedMediaData) {
                showToast("⚠️ Vui lòng chọn ảnh để xóa.", "bg-warning");
                return;
            }

            if (!confirm("Bạn có chắc muốn xóa ảnh này vĩnh viễn?")) return;

            fetch(`/api/media/${selectedMediaId}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                },
            })
                .then(async (res) => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw new Error(data.error || "Xóa thất bại.");
                    }

                    showToast("🗑️ Đã xóa ảnh thành công!", "bg-success");
                    resetPreview();
                    loadMediaList();
                    selectedMediaId = null;
                    selectedMediaData = null;
                    document.getElementById("deleteImageBtn").disabled = true;
                })
                .catch((err) => {
                    showToast(`❌ ${err.message}`, "bg-danger");
                });
        });

    /** ==========================
     *  Insert into editor
     * ========================== */
    document
        .getElementById("insertToEditorBtn")
        .addEventListener("click", () => {
            if (!selectedMediaId || !selectedMediaData) {
                showToast("Vui lòng chọn ảnh!", "bg-danger");
                return;
            }

            if (!validateAllFields()) return;

            const medium = selectedMediaData.meta?.variants?.medium;
            if (!medium || !medium.path) {
                showToast(
                    "❌ Không tìm thấy phiên bản medium của ảnh.",
                    "bg-danger"
                );
                return;
            }

            const imageUrl = `/storage/${medium.path}`;
            const captionText = captionInput.value.trim();
            const html = `
                <figure class="image">
                    <img src="${imageUrl}" 
                        alt="${captionText}" 
                        title="${titleInput.value.trim()}" 
                        width="${medium.width}" 
                        height="${medium.height}" 
                        data-description="${descriptionInput.value.trim()}" />
                    <figcaption class="caption"><i>${captionText}</i></figcaption>
                </figure>
            `;

            editorInstance.model.change(() => {
                const viewFragment = editorInstance.data.processor.toView(html);
                const modelFragment = editorInstance.data.toModel(viewFragment);
                editorInstance.model.insertContent(modelFragment);
            });

            bootstrap.Modal.getInstance(
                document.getElementById("mediaLibraryModal")
            ).hide();
        });

    /** ==========================
     *  handle thumbnail when click btn 'Chọn làm ảnh đại diện'
     * ========================== */
    document
        .getElementById("insertToThumbnailBtn")
        .addEventListener("click", function () {
            if (!selectedMediaId || !selectedMediaData) {
                showToast(
                    "Vui lòng chọn ảnh để làm ảnh đại diện.",
                    "bg-warning"
                );
                return;
            }

            if (!validateAllFields()) return;

            const imageUrl = selectedMediaData.url;
            const captionText = captionInput.value.trim();

            // render to thumbnail preview
            const thumbnailImg = document.getElementById(
                "thumbnail-preview-img"
            );
            thumbnailImg.src = imageUrl;
            thumbnailImg.alt = captionText || "Thumbnail preview";

            document
                .getElementById("thumbnail-preview-container")
                .classList.remove("d-none");
            document
                .getElementById("thumbnail-placeholder")
                .classList.add("d-none");

            // hidden input
            document.getElementById("featured_media_id").value =
                selectedMediaId;

            // close modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("mediaLibraryModal")
            );

            modal.hide();
        });

    /** ==========================
     *  Tab toggle
     * ========================== */
    const sidebar = document.getElementById("mediaSidebar");
    const insertBtn = document.getElementById("insertToEditorBtn");

    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach((btn) => {
        btn.addEventListener("shown.bs.tab", function (event) {
            const targetId = event.target.getAttribute("data-bs-target");
            if (targetId === "#upload") {
                sidebar.classList.add("d-none");
                insertBtn.disabled = true;
            } else if (targetId === "#library") {
                sidebar.classList.remove("d-none");
                insertBtn.disabled = false;
            }
        });
    });

    /** ==========================
     *  Open modal
     * ========================== */
    openMediaBtn.addEventListener("click", function () {
        resetPreview();
        loadMediaList();
        mediaModal.show();
        openMediaBtn.style.display = "none";
    });

    /** ==========================
     *  Hidden modal
     * ========================== */
    document
        .getElementById("mediaLibraryModal")
        .addEventListener("hidden.bs.modal", function () {
            openMediaBtn.style.display = "block";
        });

    /** ==========================
     *  Realtime validation
     * ========================== */
    [titleInput, captionInput, descriptionInput].forEach((input) => {
        input.addEventListener("input", () =>
            validateField(input, input !== descriptionInput)
        );
    });
});

/** ==========================
 *  Toast helper
 * ========================== */
function showToast(message, bgClass = "bg-primary") {
    const toastEl = document.getElementById("liveToast");
    const toastBody = document.getElementById("toast-body");

    toastBody.textContent = message;

    // Reset color
    toastEl.classList.remove(
        "bg-success",
        "bg-danger",
        "bg-warning",
        "bg-primary"
    );
    toastEl.classList.add(bgClass);

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
