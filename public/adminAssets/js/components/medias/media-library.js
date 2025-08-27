document.addEventListener("DOMContentLoaded", function () {
    const defaultImageUrl =
        document.getElementById("previewImage").dataset.default;
    const openMediaBtn = document.getElementById("openMediaLibrary");
    const mediaModal = new bootstrap.Modal(
        document.getElementById("mediaLibraryModal")
    );
    const libraryTab = document.querySelector("#library-tab");

    openMediaBtn.addEventListener("click", function () {
        loadMediaList(); // call Ajax to load img
        mediaModal.show();
    });

    // form seo img data
    const form = document.getElementById("editMetadataForm");
    const titleInput = form.querySelector('input[name="title"]');
    const captionInput = form.querySelector('textarea[name="caption"]');
    const descriptionInput = form.querySelector('textarea[name="description"]');
    const regex = /^[\p{L}\p{N}\s]+$/u;

    // check valid
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

    function loadMediaList() {
        const grid = document.getElementById("mediaGrid");
        grid.innerHTML = "";

        fetch("/api/media")
            .then((res) => res.json())
            .then((data) => {
                const mediaItems = data.data || data; // if use paginate

                mediaItems.forEach((media) => {
                    const col = document.createElement("div");
                    col.className = "col-md-2 media-item";
                    col.dataset.id = media.id;
                    col.dataset.url = media.url;
                    col.innerHTML = `
                        <div class="card h-100 border position-relative">
                            <img src="${media.url}" class="card-img-top" alt="${media.title}">
                          
                            <div class="selected-overlay d-none position-absolute top-0 end-0 p-1">
                            <i class="fa-solid fa-circle-check text-success fa-lg"></i>
                            </div>
                        </div>
                    `;
                    grid.appendChild(col);
                });
            })
            .catch((err) => {
                console.error("Lỗi khi tải media:", err);
            });
    }
    // End load media list

    // Upload new imgs
    const input = document.getElementById("mediaUploadInput");
    const chooseBtn = document.getElementById("chooseFileBtn");
    const uploadBtn = document.getElementById("uploadMediaBtn");

    const fileList = document.getElementById("selectedFileList");

    input.addEventListener("change", function () {
        fileList.innerHTML = "";

        if (input.files.length > 0) {
            chooseBtn.classList.add("d-none");
            uploadBtn.classList.remove("d-none");

            // show file when choose
            Array.from(input.files).forEach((file) => {
                const li = document.createElement("li");
                li.textContent = `📁 ${file.name}`;
                fileList.appendChild(li);
            });
        } else {
            chooseBtn.classList.remove("d-none");
            uploadBtn.classList.add("d-none");
        }
    });

    // when express "Tải lên"
    uploadBtn.addEventListener("click", function () {
        const files = input.files;
        if (!files.length) {
            showToast("⚠️ Vui lòng chọn ít nhất một tập tin.", "bg-danger");
            return;
        }

        const formData = new FormData();
        let validFileCount = 0;

        for (let file of files) {
            if (!file.type.startsWith("image/")) {
                showToast(`❌ "${file.name}" không phải là ảnh.`, "bg-danger");
                chooseBtn.classList.remove("d-none");
                uploadBtn.classList.add("d-none");
                continue;
            }

            if (file.size > 10 * 1024 * 1024) {
                showToast(
                    `❌ "${file.name}" vượt quá dung lượng cho phép (10MB).`,
                    "bg-danger"
                );
                chooseBtn.classList.remove("d-none");
                uploadBtn.classList.add("d-none");
                continue;
            }

            const nameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
            const slug = window.slugify(nameWithoutExt);
            formData.append("files[]", file);
            formData.append("slugs[]", slug);
            validFileCount++;
        }

        if (validFileCount === 0) {
            return; // Don't file → no fetch
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
            .then((data) => {
                console.log("Upload thành công:", data);
                showToast("🎉 Tải lên thành công!", "bg-success");

                if (typeof loadMediaList === "function") {
                    loadMediaList();
                }

                // Reset UI
                input.value = "";
                fileList.innerHTML = "";
                chooseBtn.classList.remove("d-none");
                uploadBtn.classList.add("d-none");

                if (libraryTab) {
                    new bootstrap.Tab(libraryTab).show();
                }
            })
            .catch((err) => {
                console.error(err);
                showToast("❌ Có lỗi xảy ra khi tải lên.", "bg-danger");
                chooseBtn.classList.remove("d-none");
                uploadBtn.classList.add("d-none");
            });
    });

    // End upload new imgs

    // Click media item
    document.addEventListener("click", function (e) {
        const item = e.target.closest(".media-item");
        if (!item) return;

        if (item.classList.contains("selected")) {
            item.classList.remove("selected");

            // Reset preview
            document.getElementById("previewImage").querySelector("img").src =
                defaultImageUrl;

            // Reset metadata
            const form = document.getElementById("editMetadataForm");
            form.media_id.value = "";
            form.title.value = "";
            form.caption.value = "";
            form.description.value = "";

            document.getElementById("fileName").textContent =
                "Đang cập nhật...";
            document.getElementById("uploadDate").textContent =
                "Đang cập nhật...";
            document.getElementById("fileSize").textContent =
                "Đang cập nhật...";
            document.getElementById("imageDimensions").textContent =
                "Đang cập nhật...";

            return;
        }

        document.querySelectorAll(".media-item").forEach((el) => {
            el.classList.remove("selected");
        });

        item.classList.add("selected");

        const mediaId = item.dataset.id;
        const imageUrl = item.dataset.url;

        // show preview
        document.getElementById("previewImage").querySelector("img").src =
            imageUrl;

        // Get metadata from server
        fetch(`/media-library/${mediaId}`, {
            headers: {
                Accept: "application/json",
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error("Không thể lấy metadata.");
                return res.json();
            })
            .then((media) => {
                const form = document.getElementById("editMetadataForm");
                form.media_id.value = media.id;
                form.title.value = media.title || "";
                form.caption.value = media.caption || "";
                form.description.value = media.description || "";

                document.getElementById("fileName").textContent =
                    media.file_name || "Đang cập nhật...";
                document.getElementById("uploadDate").textContent =
                    media.created_at || "Đang cập nhật...";
                document.getElementById("fileSize").textContent =
                    media.size || "Đang cập nhật...";
                document.getElementById("imageDimensions").textContent =
                    media.dimensions || "Đang cập nhật...";
            })
            .catch((err) => {
                console.error("Lỗi khi lấy metadata:", err);
                showToast("❌ Không thể tải thông tin ảnh.", "bg-danger");
            });
    });

    // Realtime validation
    [titleInput, captionInput, descriptionInput].forEach((input) => {
        input.addEventListener("input", () =>
            validateField(input, input !== descriptionInput)
        );
    });

    // handle insert img to content editor
    document
        .getElementById("insertToEditorBtn")
        .addEventListener("click", () => {
            const imageUrl = document
                .getElementById("previewImage")
                .querySelector("img").src;
            const title = titleInput.value.trim();
            const caption = captionInput.value.trim();
            const description = descriptionInput.value.trim();
            const alt = caption;

            const isTitleValid = validateField(titleInput, true);
            const isCaptionValid = validateField(captionInput, true);
            const isDescriptionValid = validateField(descriptionInput, false);

            if (!isTitleValid || !isCaptionValid || !isDescriptionValid) return;

            const html = `
            <figure class="image">
                <img src="${imageUrl}" alt="${alt}" title="${title}" data-description="${description}" />
                ${caption ? `<figcaption>${caption}</figcaption>` : ""}
            </figure>
        `;

            editorInstance.model.change((writer) => {
                const viewFragment = editorInstance.data.processor.toView(html);
                const modelFragment = editorInstance.data.toModel(viewFragment);
                editorInstance.model.insertContent(modelFragment);
            });

            const modalEl = document.getElementById("mediaLibraryModal");
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();
        });
});

function showToast(message, bgClass = "bg-success") {
    const toastBody = document.querySelector("#toast-message .toast-body");
    const toastEl = document.querySelector("#toast-message .toast");

    toastBody.textContent = message;

    toastEl.classList.remove(
        "bg-success",
        "bg-danger",
        "bg-warning",
        "bg-info"
    );
    toastEl.classList.add(bgClass);

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
