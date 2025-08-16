document.getElementById("thumbnail").addEventListener("change", function () {
    console.log("Thumbnail selected:", this.files[0]);
});

// Demo STATUS of Edit Snippet Modal
function updateProgressBar(id, value, max) {
    const bar = document.getElementById(id);
    const percent = Math.min((value / max) * 100, 100);
    bar.style.width = percent + "%";

    if (percent < 50) {
        bar.className = "progress-bar bg-danger";
    } else if (percent < 80) {
        bar.className = "progress-bar bg-warning";
    } else {
        bar.className = "progress-bar bg-success";
    }
}

document.getElementById("seo_title").addEventListener("input", function () {
    updateProgressBar("titleBar", this.value.length, 60);
});

document.getElementById("seo_slug").addEventListener("input", function () {
    updateProgressBar("slugBar", this.value.length, 100);
});

document.getElementById("seo_desc").addEventListener("input", function () {
    updateProgressBar("descBar", this.value.length, 160);
});

//Focus keyword when express enter
const input = document.getElementById("keyword-input");
const container = document.getElementById("keyword-container");

input.addEventListener("keydown", function (e) {
    if (e.key === "Enter" && this.value.trim() !== "") {
        e.preventDefault();
        const keyword = this.value.trim();
        addKeywordTag(keyword);
        this.value = "";
    }
});

function addKeywordTag(text) {
    const tag = document.createElement("span");
    tag.className = "keyword-tag";
    tag.innerHTML = `${text}<span class="remove-tag">&times;</span>`;
    container.insertBefore(tag, input);

    tag.querySelector(".remove-tag").addEventListener("click", () => {
        tag.remove();
    });
}
//End focus keyword when express enter

// toggle Checklist
document.querySelectorAll(".accordion-toggle").forEach((btn) => {
    btn.addEventListener("click", () => {
        const target = document.querySelector(btn.dataset.target);
        const icon = btn.querySelector(".chevron-icon");

        target.classList.toggle("show");
        icon.classList.toggle("rotate");
    });
});
