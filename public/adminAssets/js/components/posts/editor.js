document.addEventListener("DOMContentLoaded", function () {
    // let editorInstance;
    ClassicEditor.create(document.querySelector("#editor"), {
        heading: {
            options: [
                {
                    model: "paragraph",
                    title: "Đoạn văn",
                    class: "ck-heading_paragraph",
                },
                {
                    model: "heading2",
                    view: "h2",
                    title: "H2 - Tiêu đề phụ",
                    class: "ck-heading_heading2",
                },
                {
                    model: "heading3",
                    view: "h3",
                    title: "H3 - Tiêu đề nhỏ",
                    class: "ck-heading_heading3",
                },
            ],
        },
        toolbar: [
            "heading",
            "|",
            "bold",
            "italic",
            "link",
            "|",
            "bulletedList",
            "|",
            "uploadImage",
            "|",
            "undo",
            "redo",
        ],
        removePlugins: ["MediaEmbed"],
        image: {
            styles: ["alignCenter", "alignLeft", "alignRight"],
            resizeUnit: "%",
            toolbar: [
                "imageTextAlternative",
                "|",
                "imageStyle:alignLeft",
                "imageStyle:alignCenter",
                "imageStyle:alignRight",
            ],
        },
        ckfinder: {
            uploadUrl: window.ckUploadUrl || "",
        },
    })
        .then((editor) => {
            window.editorInstance = editor;

            const form = document.getElementById("postForm");
            const titleInput = document.getElementById("post-title");
            const publishButton = document.querySelector(".publish-submit");
            const categoryCheckboxes = document.querySelectorAll(
                ".category-item input[type='checkbox']"
            );

            publishButton.disabled = true;

            const originalTitle = titleInput?.value.trim() || "";
            const originalContent = editor.getData().trim();
            const originalCategories = Array.from(categoryCheckboxes)
                .filter((cb) => cb.checked)
                .map((cb) => cb.value);

            function getCurrentCategories() {
                return Array.from(categoryCheckboxes)
                    .filter((cb) => cb.checked)
                    .map((cb) => cb.value);
            }

            function checkChanges() {
                const currentTitle = titleInput?.value.trim() || "";
                const currentContent = editor.getData().trim();
                const currentCategories = getCurrentCategories();

                const hasTitleChanged = currentTitle !== originalTitle;
                const hasContentChanged = currentContent !== originalContent;
                const hasCategoryChanged =
                    originalCategories.length !== currentCategories.length ||
                    !originalCategories.every((id) =>
                        currentCategories.includes(id)
                    );

                const hasChanges =
                    hasTitleChanged || hasContentChanged || hasCategoryChanged;

                publishButton.disabled = !hasChanges;
            }

            publishButton.addEventListener("click", function (e) {
                e.preventDefault();
                document.querySelector("#editor").value = editor.getData();
                form.submit();
            });

            const previewBtn = document.querySelector(
                '[data-bs-target="#modalPreviewPost"]'
            );
            const previewContent = document.getElementById("preview-content");

            if (previewBtn && previewContent) {
                previewBtn.addEventListener("click", () => {
                    const title = titleInput?.value.trim() || "";
                    const content = editor.getData();
                    const selectedCategoryNames = Array.from(categoryCheckboxes)
                        .filter((cb) => cb.checked)
                        .map((cb) =>
                            cb
                                .closest(".category-item")
                                .querySelector("label")
                                .textContent.trim()
                        );

                    previewContent.innerHTML = `
                <h1 class="fw-bold mb-3">${title}</h1>
                <div class="text-muted mb-4 fst-italic">Chuyên mục: ${selectedCategoryNames.join(
                    ", "
                )}</div>
                <div class="post-content">${content}</div>
            `;
                });
            }

            titleInput?.addEventListener("input", checkChanges);
            editor.model.document.on("change:data", checkChanges);
            categoryCheckboxes.forEach((cb) =>
                cb.addEventListener("change", checkChanges)
            );

            checkChanges();
        })

        .catch((error) => {
            console.error("Lỗi khi khởi tạo CKEditor:", error);
        });
});
