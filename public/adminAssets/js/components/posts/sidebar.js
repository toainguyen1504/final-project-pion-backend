// Publish section
document.addEventListener("DOMContentLoaded", function () {
    // DOM elements
    const statusSelect = document.getElementById("status-select");
    const statusLabel = document.getElementById("status-label");
    const hiddenStatus = document.getElementById("hidden_status");

    const visibilitySelect = document.getElementById("visibility-select");
    const visibilityLabel = document.getElementById("visibility-label");
    const hiddenVisibility = document.getElementById("hidden_visibility");

    const publishInput = document.querySelector(
        '#edit-date input[type="datetime-local"]'
    );
    const publishLabel = document.getElementById("publish-label");
    const hiddenPublishAt = document.getElementById("hidden_publish_at");

    // Set min/max date
    const now = new Date();
    publishInput.min = now.toISOString().slice(0, 16);

    const maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 2);
    publishInput.max = maxDate.toISOString().slice(0, 16);

    const triggerBadge = document.getElementById("seo-score-box");
    const targetBadge = document.getElementById("seo-score-target");

    // DEFAULT VALUE
    const initialStatus = hiddenStatus.value || "draft";
    const initialVisibility = hiddenVisibility.value || "private";
    const label = document.getElementById("publish-label");

    let previousStatus = hiddenStatus.value;
    let previousVisibility = initialVisibility;

    // set time published
    const raw = label.dataset.raw;
    if (raw) {
        const date = new Date(raw);
        label.textContent = formatDateForVN(date);
    }

    updateStatusUI(initialStatus);
    updateVisibilityUI(initialVisibility);

    // Utility functions
    function updateStatusUI(value) {
        const map = {
            published: { text: "Đã xuất bản", class: "text-success" },
            draft: { text: "Bản nháp", class: "text-dark" },
            pending: { text: "Đang chờ xét duyệt", class: "text-warning" },
        };
        const { text, class: cls } = map[value] || {};
        statusSelect.value = value;
        statusLabel.textContent = text;
        statusLabel.className = cls;
        hiddenStatus.value = value;
    }

    function updateVisibilityUI(value) {
        const map = {
            public: { text: "Công khai", class: "text-success" },
            private: { text: "Riêng tư", class: "text-dark" },
            password: { text: "Mật khẩu bảo vệ", class: "text-info" },
            scheduled_public: {
                text: "Công khai theo lịch",
                class: "text-success",
            },
        };
        const { text, class: cls } = map[value] || {};
        visibilitySelect.value = value;
        visibilityLabel.textContent = text;
        visibilityLabel.className = cls;
        hiddenVisibility.value = value;
    }

    function syncVisibilityWithStatus(statusValue) {
        const hasScheduledDate =
            hiddenPublishAt.value &&
            new Date(hiddenPublishAt.value) > new Date();
        if (hasScheduledDate) {
            updateVisibilityUI("scheduled_public");
            return;
        }

        const visibilityValue =
            statusValue === "published" ? "public" : "private";
        updateVisibilityUI(visibilityValue);
        document.querySelector(
            '[data-target="#edit-visibility"]'
        ).style.display = "none";
    }

    function formatDateForVN(dateObj) {
        return new Intl.DateTimeFormat("vi-VN", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "numeric",
            minute: "numeric",
            hour12: true,
        }).format(dateObj);
    }

    // Event bindings
    document.querySelectorAll(".edit-toggle").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            document.querySelector(btn.dataset.target).classList.toggle("show");
        });
    });

    document.querySelectorAll(".cancel-toggle").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            document.querySelector(btn.dataset.target).classList.remove("show");
        });
    });

    document
        .querySelector('[data-action="save-status"]')
        .addEventListener("click", () => {
            const statusValue = statusSelect.value;
            updateStatusUI(statusValue);
            syncVisibilityWithStatus(statusValue);
            document.querySelector("#edit-status").classList.remove("show");
        });

    document
        .querySelector('[data-action="save-visibility"]')
        .addEventListener("click", () => {
            updateVisibilityUI(visibilitySelect.value);
            document.querySelector("#edit-visibility").classList.remove("show");
        });


    // publish
    document.getElementById("save-schedule").addEventListener("click", () => {
        const selectedDate = publishInput.value;
        if (!selectedDate) return;

        const dateObj = new Date(selectedDate);
        publishLabel.textContent = formatDateForVN(dateObj);
        hiddenPublishAt.value = selectedDate;

        if (dateObj > new Date()) {
            updateVisibilityUI("scheduled_public");

            if (["draft", "pending"].includes(statusSelect.value)) {
                updateStatusUI("published");
            }
        }

        document.querySelector("#edit-date").classList.remove("show");
    });
    // End processing scheduled tasks

    // Handle moving to trash
    // document.querySelector(".trash-link").addEventListener("click", (e) => {
    //     e.preventDefault();

    //     // Save previous status (visibility)
    //     previousStatus = hiddenStatus.value;
    //     previousVisibility = hiddenVisibility.value;

    //     updateStatusUI("archived");
    //     updateVisibilityUI("private");

    //     hiddenStatus.value = "archived";
    //     hiddenVisibility.value = "private";

    //     document.querySelector(".trash-link").classList.add("d-none");
    //     document.querySelector(".trash-status").classList.remove("d-none");

    //     statusLabel.textContent = "Đã bỏ vào thùng rác";
    //     statusLabel.className = "text-danger";
    // });

    // Handle restore
    // document.querySelector(".restore-link").addEventListener("click", (e) => {
    //     e.preventDefault();

    //     // Restore previous status and visibility
    //     updateStatusUI(previousStatus);
    //     updateVisibilityUI(previousVisibility);

    //     hiddenStatus.value = previousStatus;
    //     hiddenVisibility.value = previousVisibility;

    //     document.querySelector(".trash-link").classList.remove("d-none");
    //     document.querySelector(".trash-status").classList.add("d-none");
    // });

    // highlight Badge when click seo-score-box
    if (triggerBadge && targetBadge) {
        triggerBadge.addEventListener("click", function () {
            targetBadge.scrollIntoView({
                behavior: "smooth",
                block: "center",
                inline: "nearest",
            });

            //highlight
            targetBadge.classList.add("highlight-seo");
            setTimeout(() => {
                targetBadge.classList.remove("highlight-seo");
            }, 1500);
        });
    }

    // Category
    const searchInput = document.getElementById("category-search");
    const categoryItems = document.querySelectorAll(".category-item");

    if (!searchInput || categoryItems.length === 0) return;

    // Function to remove Vietnamese diacritics
    function normalizeVietnamese(str) {
        return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    }

    searchInput.addEventListener("input", function () {
        const keyword = normalizeVietnamese(this.value);

        categoryItems.forEach((item) => {
            const label = item.querySelector("label").textContent;
            const normalizedLabel = normalizeVietnamese(label);

            item.style.display = normalizedLabel.includes(keyword)
                ? "block"
                : "none";
        });
    });
});
