//========== CONSTANT ==========
const baseUrl = "https://pion.edu.vn/";
const baseDomain = window.location.hostname;

document.addEventListener("DOMContentLoaded", function () {
    //========== PREVIEW ==========
    const titleInput = document.getElementById("post-title");
    const sapoInput = document.getElementById("sapo_text");

    const editSnippetBtn = document.querySelector(
        '[data-bs-target="#snippetModal"]'
    );
    const btnSaveSnippet = document.querySelector("#snippetModal #btn-save");

    // Default placeholder values
    const defaultTitle = "Tiêu đề bài viết sẽ hiển thị ở đây...";
    const defaultSlug = "duong-dan-mac-dinh";
    const defaultDesc = "Đoạn giới thiệu ngắn sẽ hiển thị ở đây...";

    let normalizedTitleInput = "";
    let rawHtml = "";
    let paragraphHtmls = [];
    let normalizedContentPost = "";
    let isSeoEditedManually = false;

    // initial seo badge
    updateSeoBadge(0);

    // extract content CKEditor
    function extractContentFromEditor() {
        rawHtml = editorInstance.getData().trim();

        paragraphHtmls = rawHtml
            .split(/<\/p>|<br\s*\/?>|<\/div>/i)
            .map((p) => p.replace(/<[^>]+>/g, "").trim())
            .filter((p) => p);

        normalizedContentPost = paragraphHtmls.join("\n\n").toLowerCase();
    }

    // Synchronize 3 SEO attributes: slug, title, description
    function syncHiddenFields(title, slug, sapo) {
        document.getElementById("hidden_seo_title").value = title;
        document.getElementById("hidden_slug").value = slugify(slug);
        document.getElementById("hidden_seo_description").value = sapo;
    }

    function syncModalInputs(title, slug, sapo) {
        const seoTitleInput = document.getElementById("seo_title");
        const seoSlugInput = document.getElementById("seo_slug");
        const seoDescInput = document.getElementById("seo_description");

        if (seoTitleInput) seoTitleInput.value = title;
        if (seoSlugInput) seoSlugInput.value = slug;
        if (seoDescInput) seoDescInput.value = sapo;
    }

    function refreshSeo() {
        calculateSeoScore();
        updateSeoChecklist();
    }

    // Disable/Enable the Save button based on the default"
    function checkSnippetChanged() {
        const seoTitle = document.getElementById("seo_title").value.trim();
        const seoSlug = document.getElementById("seo_slug").value.trim();
        const seoDesc = document.getElementById("seo_description").value.trim();

        if (
            (seoTitle === "" || seoTitle === defaultTitle) &&
            (seoSlug === "" || seoSlug === defaultSlug) &&
            (seoDesc === "" || seoDesc === defaultDesc)
        ) {
            btnSaveSnippet.setAttribute("disabled", "disabled");
        } else {
            btnSaveSnippet.removeAttribute("disabled");
        }
    }

    //========== EVENT BINDING ==========
    // Update the preview when entering the title and assign the value to normalizedTitleInput
    if (titleInput) {
        titleInput.addEventListener("input", function () {
            const title = this.value.trim();
            const slug = slugify(title);
            const sapo = sapoInput?.value.trim() || "";

            normalizedTitleInput = normalizeText(this.value);

            if (!isSeoEditedManually) {
                updateMainPreview(title, slug, sapo);
                syncHiddenFields(title, slug, sapo);
                syncModalInputs(title, slug, sapo);
            }

            refreshSeo();
        });
    }

    // Update the preview when entering the sapo
    if (sapoInput) {
        sapoInput.addEventListener("input", function () {
            const title =
                document.getElementById("post-title")?.value.trim() || "";
            const slug = slugify(title);
            const sapo = this.value.trim();

            if (!isSeoEditedManually) {
                updateMainPreview(title, slug, sapo);
                syncHiddenFields(title, slug, sapo);
                syncModalInputs(title, slug, sapo);
            }

            refreshSeo();
        });
    }

    // When clicking the 'Edit snippet' button
    if (editSnippetBtn) {
        editSnippetBtn.addEventListener("click", function () {
            const previewTitle = normalizeText(
                document.getElementById("preview_title").textContent.trim()
            );

            const previewSlug = document
                .getElementById("preview_slug")
                .textContent.trim();

            const previewDesc = normalizeText(
                document.getElementById("preview_sapo")?.textContent.trim() ||
                    ""
            );

            const seoTitleInput = document.getElementById("seo_title");
            const seoSlugInput = document.getElementById("seo_slug");
            const seoDescInput = document.getElementById("seo_description");

            if (seoTitleInput) seoTitleInput.value = previewTitle;
            if (seoSlugInput) {
                const parts = previewSlug.split("/").filter(Boolean);
                seoSlugInput.value = parts[parts.length - 1] || "";
            }
            if (seoDescInput) seoDescInput.value = previewDesc;

            updateModalPreview(previewTitle, seoSlugInput.value, previewDesc);
            initSeoFields();
            checkSnippetChanged();
        });
    }

    // Synchronize content when typing in the modal input
    ["seo_title", "seo_slug", "seo_description"].forEach((id) => {
        const input = document.getElementById(id);
        input.addEventListener("input", function () {
            isSeoEditedManually = true; // Mark as manually edited

            const title = document.getElementById("seo_title").value.trim();
            const slug = document.getElementById("seo_slug").value.trim();
            const desc = document
                .getElementById("seo_description")
                .value.trim();

            updateModalPreview(title, slug, desc); // update data
            initSeoFields();
            refreshSeo();
            checkSnippetChanged(); // Check again on each keystroke
        });
    });

    // Handle when clicking 'Save changes' in the modal
    btnSaveSnippet.addEventListener("click", function () {
        const seoTitle = document.getElementById("seo_title").value.trim();
        const seoSlug = document.getElementById("seo_slug").value.trim();
        const seoDesc = document.getElementById("seo_description").value.trim();

        syncHiddenFields(seoTitle, seoSlug, seoDesc);
        updateMainPreview(seoTitle, seoSlug, seoDesc);

        showToast("Đã lưu thay đổi vào bản nháp SEO!");

        const modal = bootstrap.Modal.getInstance(
            document.getElementById("snippetModal")
        );
        refreshSeo();
        modal.hide();
    });
    //========== END PREVIEW ==========

    // ==========keywords AND checklist==============
    const keywordInput = document.getElementById("keyword-input");
    const keywordTagsContainer = document.querySelector(".keyword-tags");
    const hiddenKeywords = document.getElementById("hidden_keywords");
    const keywordWrapper = document.getElementById("keyword-wrapper");

    const toggles = document.querySelectorAll(".accordion-toggle");

    const maxKeywords = 10;
    const maxKeywordLength = 60;

    let keywords = [];

    // Display quantity limit notification
    const limitNotice = document.createElement("div");
    limitNotice.textContent =
        "Vui lòng xóa bớt keyword cũ để thêm keyword mới (tối đa 10 keyword)!";
    limitNotice.style.color = "#d9534f";
    limitNotice.style.fontSize = "0.9rem";
    limitNotice.style.marginTop = "6px";
    limitNotice.style.display = "none";
    keywordWrapper.parentElement.appendChild(limitNotice);

    // TDisplay length limit notification
    const lengthNotice = document.createElement("div");
    lengthNotice.textContent = `Keyword không được vượt quá ${maxKeywordLength} ký tự!`;
    lengthNotice.style.color = "#d9534f";
    lengthNotice.style.fontSize = "0.9rem";
    lengthNotice.style.marginTop = "6px";
    lengthNotice.style.display = "none";
    keywordWrapper.parentElement.appendChild(lengthNotice);

    // Display invalid keyword notification
    const errorNotice = document.createElement("div");
    errorNotice.style.color = "#d9534f";
    errorNotice.style.fontSize = "0.9rem";
    errorNotice.style.marginTop = "6px";
    errorNotice.style.display = "none";
    keywordWrapper.parentElement.appendChild(errorNotice);

    keywordInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && this.value.trim() !== "") {
            e.preventDefault();
            const keyword = this.value.trim().toLowerCase();

            // Reset errs
            errorNotice.style.display = "none";
            errorNotice.textContent = "";
            lengthNotice.style.display = "none";
            limitNotice.style.display = "none";

            if (keyword.length > maxKeywordLength) {
                lengthNotice.style.display = "block";
                return;
            }

            if (keywords.length >= maxKeywords) {
                limitNotice.style.display = "block";
                return;
            }

            // Check for spam / invalid input conditions
            const isValid = /^[a-zA-Z0-9À-ỹ\s\-]+$/.test(keyword);
            const notTooShort = keyword.length >= 6;
            const minTwoWords = keyword.trim().split(/\s+/).length >= 2;
            const notAllNumbers = !/^\d+$/.test(keyword);
            const notSpamRepeat = !/^([a-zA-ZÀ-ỹ0-9])\1{2,}$/.test(keyword);

            if (
                !isValid ||
                !notTooShort ||
                !notAllNumbers ||
                !notSpamRepeat ||
                !minTwoWords
            ) {
                errorNotice.textContent =
                    "Keyword không hợp lệ! Vui lòng nhập ít nhất 2 từ và đủ rõ nghĩa.";
                errorNotice.style.display = "block";
                return;
            }

            // Add keyword if valid
            if (!keywords.includes(keyword)) {
                keywords.push(keyword);
                renderKeywords();

                this.value = "";
                limitNotice.style.display = "none";
                lengthNotice.style.display = "none";
                errorNotice.style.display = "none";

                // update hidden fields from modal snippet
                const title = document.getElementById("seo_title").value;
                const slug = document.getElementById("seo_slug").value;
                const sapo = document.getElementById("seo_description").value;

                syncHiddenFields(title, slug, sapo);
                refreshSeo();
            }
        }
    });

    function renderKeywords() {
        keywordTagsContainer.innerHTML = "";

        keywords.forEach((kw, index) => {
            const tag = document.createElement("span");
            tag.className = "keyword-tag badge me-2 mb-1";
            tag.style.cursor = "default";
            tag.style.padding = "6px 10px";
            tag.style.borderRadius = "20px";
            tag.style.fontWeight = "normal";
            tag.style.color = index === 0 ? "#fff" : "#333";
            tag.style.backgroundColor = index === 0 ? "#ffd142" : "#fff3cd";
            tag.textContent = kw;

            const removeBtn = document.createElement("span");
            removeBtn.textContent = " ×";
            removeBtn.style.display = "inline-block";
            removeBtn.style.padding = "2px 4px";
            removeBtn.style.cursor = "pointer";
            removeBtn.style.marginLeft = "6px";
            removeBtn.style.color = index === 0 ? "#fff" : "#888";

            removeBtn.addEventListener("click", function () {
                keywords = keywords.filter((k) => k !== kw);
                renderKeywords();
                limitNotice.style.display = "none";
                lengthNotice.style.display = "none";

                // update hidden fields from modal snippet
                const title = document.getElementById("seo_title").value;
                const slug = document.getElementById("seo_slug").value;
                const sapo = document.getElementById("seo_description").value;

                syncHiddenFields(title, slug, sapo);
                refreshSeo();
            });

            tag.appendChild(removeBtn);
            keywordTagsContainer.appendChild(tag);
        });

        if (hiddenKeywords) {
            hiddenKeywords.value = keywords.join(",");
        }

        refreshSeo();
    }
    // ==========end keywords==============

    // ==========checklist toggles==============
    const firstToggle = toggles[0];
    if (firstToggle) {
        const firstTargetSelector = firstToggle.getAttribute("data-target");
        const firstTarget = document.querySelector(firstTargetSelector);
        const firstChevron = firstToggle.querySelector(".chevron-icon");
        firstTarget.classList.add("show");
        firstChevron.classList.add("rotate");
    }

    toggles.forEach((toggle) => {
        toggle.addEventListener("click", function () {
            const targetSelector = this.getAttribute("data-target");
            const target = document.querySelector(targetSelector);
            const chevron = this.querySelector(".chevron-icon");

            if (target.classList.contains("show")) {
                setTimeout(() => {
                    target.classList.remove("show");
                    chevron.classList.remove("rotate");
                }, 100);
            } else {
                target.classList.add("show");
                chevron.classList.add("rotate");
            }
        });
    });

    //========== FUNCTION ==========
    function normalizeText(str) {
        if (typeof str !== "string") {
            console.warn("normalizeText expects a string, got:", typeof str);
            return "";
        }
        return str.trim().replace(/\s+/g, " ");
    }

    function slugify(str) {
        return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .replace(/Đ/g, "D")
            .replace(/[^a-zA-Z0-9\s-]/g, "")
            .trim()
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-")
            .toLowerCase();
    }

    function cleanEmptyLines(content) {
        return content
            .split(/\n+/)
            .map((line) => line.trim())
            .filter((line) => line && !/^(&nbsp;|\s*)$/i.test(line))
            .join("\n\n");
    }

    function updateMainPreview(title, slug, desc) {
        const normalizedSlug = slugify(slug);

        document.getElementById("preview_title").textContent =
            title || "Đây là title mẫu khi bạn chưa có dữ liệu...";
        document.getElementById("preview_slug").textContent =
            baseUrl + normalizedSlug;
        document.getElementById("preview_sapo").textContent =
            desc || "Đây là đoạn mô tả mẫu khi bạn chưa có dữ liệu...";
    }

    function updateModalPreview(title, slug, desc) {
        const normalizedSlug = slugify(slug);
        const slugColor = getSlugColor(slug);

        document.getElementById("preview_title_modal").textContent =
            title || "Đây là title mẫu khi bạn chưa có dữ liệu...";
        document.getElementById("preview_slug_modal").textContent =
            normalizedSlug;
        document.getElementById("preview_description_modal").textContent =
            desc || "Đây là đoạn mô tả mẫu khi bạn chưa có dữ liệu...";

        updateProgressBar("titleBar", title.length, 60);
        updateProgressBar("slugBar", normalizedSlug.length, 60, slugColor);
        updateProgressBar("descBar", desc.length, 160);
    }

    function showToast(message, type = "success") {
        const toastEl = document.querySelector("#toast-message .toast");
        const toastBody = toastEl.querySelector(".toast-body");

        toastBody.textContent = message;

        // Remove the old color class and add the new color class
        toastEl.classList.remove("bg-success", "bg-danger", "bg-warning");
        toastEl.classList.add(`bg-${type}`);

        new bootstrap.Toast(toastEl).show();
    }

    //========== logic points seo ==========
    // SEO Score = Basic (30) + Additional (35) + Content Readability (35) = 100 points
    // Table of Contents -> 5 points -> handled in the backend API, and the frontend will render it

    editorInstance.model.document.on("change:data", () => {
        refreshSeo();
    });

    // Check SEO Basic
    function checkKeywordInTitle(title, keyword) {
        const keywordScore = title.toLowerCase().includes(keyword.toLowerCase())
            ? 2
            : 0;
        const lengthScore = getTitleScore(title); // max 8
        return keywordScore + lengthScore; // max 10
    }

    function checkKeywordInDescription(desc, keyword) {
        const keywordScore = desc.toLowerCase().includes(keyword.toLowerCase())
            ? 1
            : 0;
        const lengthScore = getDescriptionScore(desc); // max 4
        return keywordScore + lengthScore; // max 5
    }

    function checkKeywordInSlug(slug, keyword) {
        const keywordScore = slug.toLowerCase().includes(keyword.toLowerCase())
            ? 1
            : 0;
        const lengthScore = getSlugScore(slug); // max 4
        return keywordScore + lengthScore; // max 5
    }

    function checkKeywordInFirst10Percent(content, keyword) {
        if (!content || !keyword) return 0;

        // Normalize: remove extra spaces
        const normalizedContent = content.trim().toLowerCase();
        const normalizedKeyword = keyword.trim().toLowerCase();

        // Take the first 10% (at least 1 character)
        const firstCharsCount = Math.max(
            1,
            Math.floor(normalizedContent.length * 0.1)
        );
        const firstPart = normalizedContent.slice(0, firstCharsCount);

        // Check if the keyword is within the first 10%"
        return firstPart.includes(normalizedKeyword) ? 5 : 0; // max 5
    }

    function checkContentLength(content) {
        const wordCount = content.trim().split(/\s+/).length;
        return wordCount >= 600 && wordCount <= 2500 ? 5 : 0; // max 5
    }
    // End check SEO Basic

    // check Additional - max 35
    // checkKeywordDensity
    function checkKeywordDensity(content, keyword, options = {}) {
        const keywordLower = keyword.toLowerCase().trim();
        const contentLower = content.toLowerCase().trim();

        const totalWords = contentLower.split(/\s+/).length;
        const keywordCount = contentLower.split(keywordLower).length - 1;
        const density = (keywordCount / totalWords) * 100;

        const keywordLength = keywordLower.split(/\s+/).length;

        let minDensity = 0.3;
        let maxDensity = 0.8;

        if (keywordLength <= 2) {
            minDensity = 0.8;
            maxDensity = 1.5;
        } else if (keywordLength <= 4) {
            minDensity = 0.6;
            maxDensity = 1.2;
        }

        const range = options.range || {
            good: (d) => d >= minDensity && d <= maxDensity,
            ok: (d) => d > maxDensity && d <= maxDensity + 1,
            fail: (d) =>
                d < minDensity || d > maxDensity + 1 || keywordCount === 0,
        };

        const messages = options.messages || {
            good: "Mật độ từ khóa tối ưu.",
            ok: "Mật độ từ khóa tạm ổn.",
            fail: "Từ khóa chưa xuất hiện hoặc mật độ chưa đạt.",
        };

        let scoreDensity = 0;
        let messageDensity = "";
        let statusDensity = "";

        if (range.good(density)) {
            scoreDensity = 15;
            messageDensity = messages.good;
            statusDensity = "good";
        } else if (range.ok(density)) {
            scoreDensity = 5;
            messageDensity = messages.ok;
            statusDensity = "ok";
        } else {
            scoreDensity = 0;
            messageDensity = messages.fail;
            statusDensity = "fail";
        }

        return { scoreDensity, messageDensity, statusDensity };
    }

    // check h1,h2,h3
    function checkKeywordInHeadings(rawHtml, keyword, normalizedTitleInput) {
        if (!keyword || !normalizedTitleInput) {
            return {
                scoreInHeadings: 0,
                messageInHeadings: "Thiếu dữ liệu tiêu đề hoặc từ khóa.",
                statusInHeadings: "fail",
            };
        }

        const keywordLower = keyword.toLowerCase();
        const headings = rawHtml.match(/<(h2|h3)[^>]*>(.*?)<\/\1>/gi) || [];

        let scoreInHeadings = 0;
        let messageInHeadings =
            "Không tìm thấy từ khóa trong tiêu đề (hoặc H2/H3) của bài viết";
        let statusInHeadings = "fail";

        // check H1
        const h1Text = normalizedTitleInput.trim();
        const hasKeywordInH1 = h1Text.includes(keywordLower);

        const h1Words = h1Text.split(/\s+/).length;
        const h1KeywordCount = (
            h1Text.match(new RegExp(keywordLower, "g")) || []
        ).length;
        const h1DensityOk = h1KeywordCount / h1Words <= 0.4;
        const h1Stuffing = new RegExp(`(${keywordLower}\\s+){2,}`, "gi").test(
            h1Text
        );
        const h1LengthOk = h1Words >= 3 && h1Words <= 12;
        const h1Natural =
            hasKeywordInH1 && h1DensityOk && !h1Stuffing && h1LengthOk;

        // check H2/H3
        let naturalCount = 0;
        let totalWithKeyword = 0;

        headings.forEach((h) => {
            const text = h
                .replace(/<[^>]+>/g, "")
                .toLowerCase()
                .trim();
            if (text.includes(keywordLower)) {
                totalWithKeyword++;

                const words = text.split(/\s+/).length;
                const keywordCount = (
                    text.match(new RegExp(keywordLower, "g")) || []
                ).length;
                const densityOk = keywordCount / words <= 0.4;
                const stuffing = new RegExp(
                    `(${keywordLower}\\s+){2,}`,
                    "gi"
                ).test(text);
                const lengthOk = words >= 3 && words <= 12;

                if (densityOk && !stuffing && lengthOk) {
                    naturalCount++;
                }
            }
        });

        // Allocate points + status
        if (hasKeywordInH1 || totalWithKeyword > 0) {
            if (h1Natural) {
                if (naturalCount > 0) {
                    scoreInHeadings = 5;
                    messageInHeadings =
                        "Từ khóa xuất hiện tự nhiên trong tiêu đề (hoặc H2/H3) của bài viết.";
                    statusInHeadings = "good";
                } else {
                    scoreInHeadings = 3;
                    messageInHeadings =
                        "Từ khóa xuất hiện tự nhiên trong nhưng không có H2/H3 nào đạt chuẩn.";
                    statusInHeadings = "ok";
                }
            } else {
                scoreInHeadings = 0;
                messageInHeadings =
                    "Từ khóa có trong tiêu đề (hoặc H2/H3) của bài viết nhưng chưa tự nhiên!";
                statusInHeadings = "fail";
            }
        }

        return { scoreInHeadings, messageInHeadings, statusInHeadings };
    }

    function checkKeywordInImageAlt(rawHtml, keyword) {
        const keywordLower = keyword.toLowerCase();
        const imgTags = [...rawHtml.matchAll(/<img[^>]*>/gi)];

        let totalImages = 0;
        let altWithKeyword = 0;
        let altWithoutKeyword = 0;
        let missingAlt = 0;
        let spammyAlt = 0;

        for (const [imgTag] of imgTags) {
            totalImages++;

            const altMatch = imgTag.match(/alt=["']([^"']+)["']/i);
            if (!altMatch) {
                missingAlt++;
                continue;
            }

            const altText = altMatch[1].trim().toLowerCase();

            if (altText === "") {
                missingAlt++;
            } else if (
                altText.includes(keywordLower) &&
                altText.length >= keywordLower.length + 10 &&
                !/(?:keyword){3,}/i.test(altText)
            ) {
                altWithKeyword++;
            } else if (/(?:keyword){3,}/i.test(altText)) {
                spammyAlt++;
            } else {
                altWithoutKeyword++;
            }
        }

        let scoreInImageAlt = 0;
        let messageInImageAlt = "";
        let statusInImageAlt = "fail";

        if (altWithKeyword > 0 && altWithKeyword >= totalImages / 2) {
            scoreInImageAlt = 5;
            messageInImageAlt = `Có ${altWithKeyword}/${totalImages} ảnh có Alt text chứa từ khóa và mô tả tự nhiên – tối ưu.`;
            statusInImageAlt = "good";
        } else if (altWithoutKeyword > 0 || altWithKeyword > 0) {
            scoreInImageAlt = 3;
            messageInImageAlt = `Có Alt text nhưng ${altWithKeyword}/${totalImages} ảnh chứa từ khóa – nên cải thiện thêm.`;
            statusInImageAlt = "ok";
        } else if (missingAlt === totalImages || spammyAlt > 0) {
            scoreInImageAlt = 0;
            messageInImageAlt = `Alt text bị thiếu hoặc spam từ khóa – chưa đạt yêu cầu SEO.`;
            statusInImageAlt = "fail";
        } else {
            scoreInImageAlt = 1;
            messageInImageAlt = `Alt text chưa chứa từ khóa hoặc mô tả chưa rõ ràng.`;
            statusInImageAlt = "ok";
        }

        return { scoreInImageAlt, messageInImageAlt, statusInImageAlt };
    }

    function checkInternalLink(rawHtml, baseDomain) {
        const anchors = [
            ...rawHtml.matchAll(/<a[^>]*href=["']([^"']+)["'][^>]*>/gi),
        ];

        let internalCount = 0;

        for (const [_, href] of anchors) {
            try {
                const url = new URL(href, `https://${baseDomain}`);
                if (url.hostname === baseDomain) {
                    internalCount++;
                }
            } catch {
                continue;
            }
        }

        let scoreInternalLinks = 0;
        let messageInternalLinks = "";
        let statusInternalLinks = "fail";

        if (internalCount === 0) {
            scoreInternalLinks = 0;
            messageInternalLinks = "Chưa có liên kết nội bộ trong nội dung.";
            statusInternalLinks = "fail";
        } else if (internalCount === 1) {
            scoreInternalLinks = 2;
            messageInternalLinks = "Có 1 liên kết nội bộ – nên bổ sung thêm.";
            statusInternalLinks = "ok";
        } else if (internalCount >= 2 && internalCount <= 5) {
            scoreInternalLinks = 5;
            messageInternalLinks = `Có ${internalCount} liên kết nội bộ – tối ưu.`;
            statusInternalLinks = "good";
        } else {
            scoreInternalLinks = 2;
            messageInternalLinks = `Có ${internalCount} liên kết nội bộ – hơi nhiều, nên giới hạn 2–5.`;
            statusInternalLinks = "ok";
        }

        return {
            scoreInternalLinks,
            messageInternalLinks,
            statusInternalLinks,
        };
    }

    function checkExternalLink(rawHtml, baseDomain) {
        const anchors = [
            ...rawHtml.matchAll(/<a[^>]*href=["']([^"']+)["'][^>]*>/gi),
        ];

        let externalCount = 0;
        let relCount = 0;

        for (const [fullMatch, href] of anchors) {
            try {
                const url = new URL(href, `https://${baseDomain}`);
                if (url.hostname !== baseDomain) {
                    externalCount++;
                    if (/rel=["'](nofollow|dofollow)["']/i.test(fullMatch)) {
                        relCount++;
                    }
                }
            } catch {
                continue;
            }
        }

        let scoreExternalLinks = 0;
        let messageExternalLinks = "";
        let statusExternalLinks = "fail";

        if (externalCount === 0) {
            scoreExternalLinks = 0;
            messageExternalLinks = "Chưa có liên kết ngoài trong nội dung.";
            statusExternalLinks = "fail";
        } else if (externalCount >= 1 && externalCount <= 3) {
            if (relCount === externalCount) {
                scoreExternalLinks = 5;
                messageExternalLinks =
                    "Có 1–3 liên kết ngoài với thuộc tính rel hợp lý – tối ưu.";
                statusExternalLinks = "good";
            } else {
                scoreExternalLinks = 2;
                messageExternalLinks =
                    "Có 1–3 liên kết ngoài nhưng thiếu thuộc tính rel – cần bổ sung.";
                statusExternalLinks = "ok";
            }
        } else {
            scoreExternalLinks = 0;
            messageExternalLinks =
                "Có hơn 3 liên kết ngoài – chưa tối ưu, nên giới hạn lại.";
            statusExternalLinks = "fail";
        }

        return {
            scoreExternalLinks,
            messageExternalLinks,
            statusExternalLinks,
        };
    }
    // End check Additional - max 35

    // Table of Contents will be rendered automatically later, it's not counted here
    // The remaining 5 points are reserved for bonus points for additional keywords
    // Keyword near the beginning of the title (starting from position 0–4 counts as "near the beginning") -> 5 points
    function checkKeywordNearTitleStart(title, keyword) {
        const titleWords = title.toLowerCase().trim().split(/\s+/);
        const keywordWords = keyword.toLowerCase().trim().split(/\s+/);

        let found = false;

        for (let i = 0; i <= 4; i++) {
            const segment = titleWords
                .slice(i, i + keywordWords.length)
                .join(" ");
            if (segment === keywordWords.join(" ")) {
                found = true;
                break;
            }
        }

        const scoreTitleStart = found ? 5 : 0;
        const messageTitleStart = found
            ? "Từ khóa nằm gần đầu tiêu đề."
            : "Từ khóa chưa nằm gần đầu tiêu đề.";

        return { scoreTitleStart, messageTitleStart };
    }

    // Title contains a number (year, ranking, etc.) -> 5 points
    function checkTitleHasNumber(title) {
        const currentYear = new Date().getFullYear();
        const minYear = currentYear - 2;
        const maxYear = currentYear + 1;

        const yearRegex = /\b(20\d{2})\b/g;
        const topRegex = /\btop\s*\d+\b/i;
        const numberRegex = /\b\d{1,3}\b/;

        let found = false;

        // Check year
        const yearMatches = [...title.matchAll(yearRegex)];
        const validYear = yearMatches.some((match) => {
            const year = parseInt(match[1]);
            return year >= minYear && year <= maxYear;
        });

        // Check ranking or number
        const hasTop = topRegex.test(title);
        const hasGeneralNumber = numberRegex.test(title);

        found = validYear || hasTop || hasGeneralNumber;

        const scoreTitleNumber = found ? 5 : 0;
        const messageTitleNumber = found
            ? "Tiêu đề có chứa số hợp lệ (năm, xếp hạng hoặc số lượng)."
            : "Tiêu đề chưa có số hợp lệ hoặc năm không phù hợp.";

        return { scoreTitleNumber, messageTitleNumber };
    }

    // Post title is concise and clear (≤ 60 characters) -> 5 points
    function checkTitlePostLength(title) {
        const length = title.trim().length;
        let score = 0;
        let message = "";
        let status = "fail";

        if (length >= 40 && length <= 70) {
            score = 5;
            message = `Tiêu đề bài viết tối ưu (${length}/70 ký tự).`;
            status = "good";
        } else if (length >= 20 && length < 40) {
            score = 3;
            message = `Tiêu đề hơi ngắn (${length}/70 ký tự) – nên mở rộng thêm.`;
            status = "ok";
        } else if (length < 20) {
            score = 0;
            message = `Tiêu đề quá ngắn (${length}/70 ký tự) – chưa đủ rõ ràng.`;
            status = "fail";
        } else {
            score = 0;
            message = `Tiêu đề quá dài (${length}/70 ký tự) – cần rút gọn.`;
            status = "fail";
        }

        return { score, message, status };
    }

    // Paragraph is clear -> 5 points
    function checkParagraphLength(normalizedContent) {
        const paragraphs = normalizedContent
            .split(/\n{2,}/)
            .map((p) => p.trim())
            .filter((p) => p);

        if (paragraphs.length === 0) {
            return {
                score: 0,
                message: "Không tìm thấy đoạn văn nào để đánh giá.",
                status: "fail",
            };
        }

        let score = 0;
        let hasEmptyParagraph = false;
        let hasExtremeParagraph = false;

        paragraphs.forEach((p) => {
            const wordCount = p.split(/\s+/).filter((w) => w.length > 0).length;
            const charCount = p.length;

            if (wordCount <= 2 || charCount <= 10) {
                hasEmptyParagraph = true;
                return;
            }

            if (wordCount < 20 || wordCount > 250) {
                hasExtremeParagraph = true;
                return;
            }

            if (wordCount >= 30 && wordCount <= 180 && charCount <= 900) {
                score += 1;
            }
        });

        const finalScore = Math.max(0, Math.min(score, 5));
        let message = "Các đoạn văn đều ngắn gọn, dễ đọc và phù hợp.";
        let status = "good";

        if (hasEmptyParagraph) {
            message = "Bài viết có chứa đoạn rỗng hoặc chỉ có khoảng trắng.";
            status = "fail";
        } else if (
            hasExtremeParagraph ||
            finalScore < Math.ceil(paragraphs.length * 0.7)
        ) {
            message = "Một số đoạn văn quá dài hoặc quá ngắn – nên điều chỉnh.";
            status = "ok";
        }

        return {
            score: finalScore,
            message,
            status,
        };
    }

    // Check images -> max 5 point
    function checkImages(content) {
        const imgCount = (content.match(/<img\b[^>]*>/gi) || []).length;
        let score = 0;
        let message = "";
        let status = "fail";

        if (imgCount >= 3) {
            score = 5;
            message = `Bài viết có ${imgCount} ảnh minh họa -> Rất tốt!`;
            status = "good";
        } else if (imgCount > 0) {
            score = 3;
            message = `Bài viết có ${imgCount} ảnh minh họa -> Tạm ổn, nên có ≥3 ảnh.`;
            status = "ok";
        } else {
            score = 0;
            message = "Bài viết chưa có ảnh minh họa.";
            status = "fail";
        }

        return { score, message, status };
    }

    // Check Media (Video/Audio/Iframe) -> max 5 points
    function checkMedia(content) {
        const mediaCount = (
            content.match(/<(video|audio|iframe)\b[^>]*>/gi) || []
        ).length;

        if (mediaCount >= 1) {
            return {
                score: 5,
                message: `Bài viết có ${mediaCount} video/media bổ trợ -> Tốt!`,
            };
        } else {
            return {
                score: 0,
                message: "Bài viết chưa có video/media bổ trợ.",
            };
        }
    }
    // End check Content Readability

    // total points Basic SEO
    function calculateBasicScore({ title, desc, slug, content, keyword }) {
        const score1 = checkKeywordInTitle(title, keyword);
        const score2 = checkKeywordInDescription(desc, keyword);
        const score3 = checkKeywordInSlug(slug, keyword);
        const score4 = checkKeywordInFirst10Percent(content, keyword);
        const score5 = checkContentLength(content);

        return score1 + score2 + score3 + score4 + score5;
    }

    // total points Additional SEO
    function calculateAdditionalScore({
        content,
        rawHtml,
        keyword,
        baseDomain,
    }) {
        const { scoreDensity } = checkKeywordDensity(content, keyword);
        const { scoreInHeadings } = checkKeywordInHeadings(rawHtml, keyword);
        const { scoreInImageAlt } = checkKeywordInImageAlt(rawHtml, keyword);
        const { scoreInternalLinks } = checkInternalLink(rawHtml, baseDomain);
        const { scoreExternalLinks } = checkExternalLink(rawHtml, baseDomain);

        return (
            scoreDensity +
            scoreInHeadings +
            scoreInImageAlt +
            scoreInternalLinks +
            scoreExternalLinks
        );
    }

    // total points Content Readability
    function calculateContentReadabilityScore({
        normalizedTitleInput,
        content,
        keyword,
    }) {
        const { scoreTitleStart } = checkKeywordNearTitleStart(
            normalizedTitleInput,
            keyword
        );
        const { scoreTitleNumber } = checkTitleHasNumber(normalizedTitleInput);
        const { score: scoreTitlePost } =
            checkTitlePostLength(normalizedTitleInput);
        const { score: scoreParagraphLength } = checkParagraphLength(content);
        const { score: scoreImages } = checkImages(content);
        const { score: scoreMedia } = checkMedia(content);

        return (
            scoreTitleStart +
            scoreTitleNumber +
            scoreTitlePost +
            scoreParagraphLength +
            scoreImages +
            scoreMedia
        );
    }

    // Total bonus points when entering multiple keywords to check
    function calculateSecondaryKeywordScore({
        title,
        desc,
        slug,
        content,
        keywords,
    }) {
        let score = 0;

        keywords.forEach((keyword) => {
            if (title.toLowerCase().includes(keyword)) score += 1;
            if (desc.toLowerCase().includes(keyword)) score += 1;
            if (slug.toLowerCase().includes(keyword)) score += 1;
            if (content.toLowerCase().includes(keyword)) score += 2;
        });

        return Math.min(score, 5);
    }

    // Total final points
    function calculateSeoScore() {
        // extract content
        extractContentFromEditor();

        const title = document.getElementById("hidden_seo_title").value;
        const slug = document.getElementById("hidden_slug").value;
        const desc = document.getElementById("hidden_seo_description").value;
        rawHtml = editorInstance.getData().trim();

        const keywordTags = Array.from(
            document.querySelectorAll(".keyword-tag")
        );
        const keywords = keywordTags.map((tag) =>
            tag.textContent.replace("×", "").trim().toLowerCase()
        );

        let totalScore = 0;

        if (keywords.length > 0) {
            const focusKeyword = keywords[0];
            const secondaryKeywords = keywords.slice(1);

            console.log(">>>>check title input:", normalizedTitleInput);

            // Basic SEO
            const basicScore = calculateBasicScore({
                title,
                desc,
                slug,
                content: normalizedContentPost,
                keyword: focusKeyword,
            });

            // points bonus for multi keywords
            const secondaryScore = calculateSecondaryKeywordScore({
                title,
                desc,
                slug,
                content: normalizedContentPost,
                keywords: secondaryKeywords,
            });

            // points Additional SEO
            const additionalScore = calculateAdditionalScore({
                content: normalizedContentPost,
                rawHtml,
                keyword: focusKeyword,
                baseDomain,
            });

            // points content Readability
            const readabilityScore = calculateContentReadabilityScore({
                normalizedTitleInput,
                content: normalizedContentPost,
                keyword: focusKeyword,
            });

            console.log("🎯 Tổng points SEO:", {
                basicScore,
                secondaryScore,
                additionalScore,
                readabilityScore,
            });

            totalScore =
                basicScore +
                secondaryScore +
                additionalScore +
                readabilityScore;
        }

        updateSeoBadge(Math.round(totalScore));
    }
    //========== end points seo ==========

    // func get color for bagde
    function getSeoColor(value, max) {
        if (value > max) return "#e74c3c";
        const percent = (value / max) * 100;

        if (percent < 50) return "#e74c3c";
        if (percent < 80) return "#f39c12";
        return "#2ecc71";
    }

    function getSeoStatusColor(len, type) {
        if (type === "title") {
            if (len >= 50 && len <= 60) return "#2ecc71";
            if (len >= 36 && len < 50) return "#f39c12";
            return "#e74c3c";
        }

        if (type === "description") {
            if (len >= 120 && len <= 160) return "#2ecc71";
            if (len >= 90 && len < 120) return "#f39c12";
            return "#e74c3c";
        }

        return "#999"; // fallback color
    }

    function getSlugColor(slug) {
        const normalizedSlug = slugify(slug);
        const slugLength = normalizedSlug.length;
        const slugWords = normalizedSlug.split("-").filter(Boolean).length;

        // GOOD
        if (
            (slugLength >= 20 && slugLength <= 35) ||
            (slugWords >= 3 && slugWords <= 5)
        ) {
            return "#2ecc71";
        }

        // OK
        if (
            (slugLength >= 36 && slugLength <= 60) ||
            (slugWords >= 6 && slugWords <= 8)
        ) {
            return "#f39c12";
        }

        // FAIL
        return "#e74c3c";
    }

    function getTitleScore(title, fullScore = 8) {
        const len = normalizeText(title).length;

        if (len >= 50 && len <= 60) return fullScore; // good
        if (len >= 36 && len < 50) return fullScore * 0.7; // ok
        return 0; // fail
    }

    function getSlugScore(slug, fullScore = 4) {
        const normalizedSlug = slugify(slug);
        const slugLength = normalizedSlug.length;
        const slugWords = normalizedSlug.split("-").filter(Boolean).length;

        const isGood =
            (slugLength >= 20 && slugLength <= 35) ||
            (slugWords >= 3 && slugWords <= 5);

        const isOk =
            (slugLength >= 36 && slugLength <= 60) ||
            (slugWords >= 6 && slugWords <= 8);

        if (isGood) return fullScore;
        if (isOk) return fullScore * 0.7;
        return 0;
    }

    function getDescriptionScore(desc, fullScore = 4) {
        const len = normalizeText(desc).length;

        if (len >= 120 && len <= 160) return fullScore;
        if (len >= 90 && len < 120) return fullScore * 0.7;
        return 0;
    }

    function updateSeoBadge(score, max = 100) {
        const badges = document.querySelectorAll(".seo-score-badge");
        badges.forEach((badge) => {
            badge.textContent = `SEO: ${score} / ${max}`;
            badge.style.backgroundColor = getSeoColor(score, max);
            badge.style.color = "#fff";
            badge.style.transition = "background-color 0.3s ease";
        });
    }

    function updateProgressBar(barId, value, max, customColor = null) {
        const bar = document.getElementById(barId);
        const count = document.getElementById(barId.replace("Bar", "Count"));
        const percent = Math.min((value / max) * 100, 100);
        const color = customColor || "#999";

        bar.style.width = percent + "%";
        bar.style.backgroundColor = color;
        bar.style.transition = "width 0.3s ease, background-color 0.3s ease";
        count.textContent = `${value} / ${max}`;
    }

    function updatePreview() {
        document.getElementById("preview_title_modal").textContent =
            normalizeText(document.getElementById("seo_title").value);
        document.getElementById("preview_slug_modal").textContent = slugify(
            document.getElementById("seo_slug").value
        );
        document.getElementById("preview_description_modal").textContent =
            normalizeText(document.getElementById("seo_description").value);
    }

   // Attach event listeners to the SEO inputs
    function initSeoFields() {
        [
            { id: "seo_title", bar: "titleBar", max: 60 },
            { id: "seo_slug", bar: "slugBar", max: 60 },
            { id: "seo_description", bar: "descBar", max: 160 },
        ].forEach(({ id, bar, max }) => {
            const input = document.getElementById(id);
            let valueLength;
            let customColor = null;

            if (id === "seo_slug") {
                const normalizedSlug = slugify(input.value);
                valueLength = normalizedSlug.length;
                customColor = getSlugColor(input.value);
            } else {
                const normalizedText = normalizeText(input.value);
                valueLength = normalizedText.length;
                customColor = getSeoStatusColor(
                    valueLength,
                    id === "seo_title" ? "title" : "description"
                );
            }

            updateProgressBar(bar, valueLength, max, customColor);
        });
    }

    // checklist SEO
    function updateSeoChecklist() {
        const focusKeyword = normalizeText(
            document
                .querySelector(".keyword-tag")
                ?.textContent.replace("×", "") || ""
        ).toLowerCase();

        // SEO TITLE
        const title = normalizeText(
            document.getElementById("hidden_seo_title")?.value || ""
        ).toLowerCase();

        const slug = slugify(
            document
                .getElementById("hidden_slug")
                ?.value.trim()
                .toLowerCase() || ""
        );

        const desc = normalizeText(
            document.getElementById("hidden_seo_description")?.value || ""
        ).toLowerCase();

        rawHtml = editorInstance.getData().trim();

        const basicList = document.getElementById("basic-seo-list");
        const additionalList = document.getElementById("additional-list");
        const contentList = document.getElementById("content-readability");

        const slugLength = slug.length;
        const slugWords = slug.split("-").filter(Boolean).length;

        const wordCount = normalizedContentPost.split(/\s+/).length;

        const checkBasic = (score, ok, fail) =>
            `<li class="list-group-item d-flex align-items-center gap-2">
        <i class="fa-solid ${
            score > 0
                ? "fa-circle-check text-success"
                : "fa-circle-xmark text-danger"
        }"></i>
        <span class="text-dark small">${score > 0 ? ok : fail}</span>
    </li>`;

        const checkDetailByRange = (value, ranges, messages) => {
            let iconClass = "fa-circle-xmark text-danger";
            let text = messages.fail;

            if (ranges.good(value)) {
                iconClass = "fa-circle-check text-success";
                text = messages.good;
            } else if (ranges.ok(value)) {
                iconClass = "fa-triangle-exclamation text-warning";
                text = messages.ok;
            }

            return `<li class="list-group-item d-flex align-items-center gap-2">
        <i class="fa-solid ${iconClass}"></i>
        <span class="text-dark small">${text}</span>
    </li>`;
        };

        const checkDetailByStatus = (status, message) => {
            const iconMap = {
                good: "fa-circle-check text-success",
                ok: "fa-triangle-exclamation text-warning",
                fail: "fa-circle-xmark text-danger",
            };

            const iconClass = iconMap[status] || iconMap.fail;

            return `<li class="list-group-item d-flex align-items-center gap-2">
        <i class="fa-solid ${iconClass}"></i>
        <span class="text-dark small">${message}</span>
    </li>`;
        };

        // Core scoring
        const scoreFirst10Per = focusKeyword
            ? checkKeywordInFirst10Percent(normalizedContentPost, focusKeyword)
            : 0;
        const scoreLength = normalizedContentPost
            ? checkContentLength(normalizedContentPost)
            : 0;

        const scoreTitleKeyword =
            focusKeyword && title.includes(focusKeyword) ? 1 : 0;

        const scoreDescKeyword =
            focusKeyword && desc.includes(focusKeyword) ? 1 : 0;

        const scoreSlugKeyword =
            focusKeyword && slug.includes(slugify(focusKeyword)) ? 1 : 0;

        const basicChecks = [
            focusKeyword
                ? checkBasic(
                      scoreTitleKeyword,
                      "Tiêu đề SEO có chứa từ khóa chính",
                      "Tiêu đề SEO chưa chứa từ khóa chính"
                  )
                : "",

            title
                ? checkDetailByRange(
                      title.length,
                      {
                          good: (len) => len >= 50 && len <= 60,
                          ok: (len) => len >= 36 && len < 50,
                      },
                      {
                          good: `Độ dài tiêu đề SEO đã tối ưu (${title.length}/60 ký tự)`,
                          ok: `Độ dài tiêu đề SEO tạm ổn (${title.length}/60 ký tự). Mở Snippet để chỉnh sửa!`,
                          fail: `Độ dài tiêu đề SEO chưa đạt (${title.length}/60 ký tự). Mở Snippet để chỉnh sửa!`,
                      }
                  )
                : "",

            focusKeyword
                ? checkBasic(
                      scoreDescKeyword,
                      "Mô tả SEO chứa từ khóa chính",
                      "Mô tả SEO chưa chứa từ khóa chính"
                  )
                : "",
            desc
                ? checkDetailByRange(
                      desc.length,
                      {
                          good: (len) => len >= 120 && len <= 160,
                          ok: (len) => len >= 90 && len < 120,
                      },
                      {
                          good: `Độ dài mô tả SEO đã tối ưu (${desc.length}/160 ký tự)`,
                          ok: `Độ dài mô tả SEO tạm ổn (${desc.length}/160 ký tự). Mở Snippet để chỉnh sửa!`,
                          fail: `Độ dài mô tả SEO chưa đạt (${desc.length}/160 ký tự). Mở Snippet để chỉnh sửa!`,
                      }
                  )
                : "",

            focusKeyword
                ? checkBasic(
                      scoreSlugKeyword,
                      "URL có chứa từ khóa chính",
                      "URL chưa chứa từ khóa chính. Mở Snippet để chỉnh sửa!"
                  )
                : "",
            slug
                ? checkDetailByRange(
                      slugLength,
                      {
                          good: (len) =>
                              (len >= 20 && len <= 35) ||
                              (slugWords >= 3 && slugWords <= 5),

                          ok: (len) =>
                              (len >= 36 && len <= 60) ||
                              (slugWords >= 6 && slugWords <= 8),
                      },
                      {
                          good: `Độ dài URL đã tối ưu (${slugWords} từ, ${slugLength} ký tự)`,
                          ok: `Độ dài URL tạm ổn (${slugWords} từ, ${slugLength} ký tự). Đảm bảo URL đủ rõ nghĩa và đơn giản!`,
                          fail: `Độ dài URL chưa đạt (${slugWords} từ, ${slugLength} ký tự). Đảm bảo URL đủ rõ nghĩa và đơn giản!`,
                      }
                  )
                : "",

            focusKeyword
                ? checkBasic(
                      scoreFirst10Per,
                      "Từ khóa xuất hiện trong phần đầu nội dung (10% đầu tiên)",
                      "Từ khóa không xuất hiện trong phần đầu nội dung (10% đầu tiên)"
                  )
                : "",

            checkBasic(
                scoreLength,
                `Nội dung có ${wordCount} từ. Good job!`,
                "Nội dung chưa tối ưu, nên có khoảng 600–2500 từ!"
            ),
        ];

        // Additional SEO
        const { messageDensity, status: statusDensity } = checkKeywordDensity(
            normalizedContentPost,
            focusKeyword
        );

        const { messageInHeadings, statusInHeadings } = checkKeywordInHeadings(
            rawHtml,
            focusKeyword,
            normalizedTitleInput
        );

        const { messageInternalLinks, statusInternalLinks } = checkInternalLink(
            rawHtml,
            baseDomain
        );

        const { statusExternalLinks, messageExternalLinks } = checkExternalLink(
            rawHtml,
            baseDomain
        );

        const { statusInImageAlt, messageInImageAlt } = checkKeywordInImageAlt(
            rawHtml,
            focusKeyword
        );

        const additionalChecks = [
            checkDetailByStatus(statusDensity, messageDensity),
            checkDetailByStatus(statusInHeadings, messageInHeadings),
            checkDetailByStatus(statusInternalLinks, messageInternalLinks),
            checkDetailByStatus(statusExternalLinks, messageExternalLinks),
            checkDetailByStatus(statusInImageAlt, messageInImageAlt),
        ];

        // Title and content Readability checklist
        const { status: statusTitlePost, message: messageTitlePost } =
            checkTitlePostLength(normalizedTitleInput);

        const { scoreTitleStart, messageTitleStart } =
            checkKeywordNearTitleStart(normalizedTitleInput, focusKeyword);

        const { scoreTitleNumber, messageTitleNumber } =
            checkTitleHasNumber(normalizedTitleInput);

        const {
            status: statusParagraphLength,
            message: messageParagraphLength,
        } = checkParagraphLength(normalizedContentPost);

        const { status: statusImages, message: messageImages } =
            checkImages(rawHtml);

        const { score: scoreMedia, message: messageMedia } =
            checkMedia(rawHtml);

        const contentChecks = [
            checkDetailByStatus(statusTitlePost, messageTitlePost),
            checkBasic(
                scoreTitleStart >= 5,
                messageTitleStart,
                messageTitleStart
            ),
            checkBasic(
                scoreTitleNumber,
                messageTitleNumber,
                messageTitleNumber
            ),
            checkDetailByStatus(statusParagraphLength, messageParagraphLength),
            checkDetailByStatus(statusImages, messageImages),
            checkBasic(scoreMedia, messageMedia, messageMedia),
        ];

        basicList.innerHTML = basicChecks.join("");
        additionalList.innerHTML = additionalChecks.join("");
        contentList.innerHTML = contentChecks.join("");

        // update badge
        const updateBadge = (listId, badgeSelector) => {
            const errorCount = document.querySelectorAll(
                `#${listId} .text-danger`
            ).length;
            const badge = document.querySelector(badgeSelector);

            if (!badge) return;

            // remove old class
            badge.classList.remove("bg-light-success", "bg-light-danger");

            const iconClass =
                errorCount === 0 ? "fa-circle-check" : "fa-circle-xmark";

            const bgClass =
                errorCount === 0 ? "bg-light-success" : "bg-light-danger";

            const text = errorCount === 0 ? "Hoàn tất" : `${errorCount} lỗi`;

            badge.classList.add(bgClass);
            badge.innerHTML = `<i class="fa-solid ${iconClass}"></i> ${text}`;
        };

        // call func for accordion
        updateBadge(
            "basic-seo-list",
            '[data-target="#basic-seo-list"] .seo-badge'
        );
        updateBadge(
            "additional-list",
            '[data-target="#additional-list"] .seo-badge'
        );
        updateBadge(
            "content-readability",
            '[data-target="#content-readability"] .seo-badge'
        );
    }
});
