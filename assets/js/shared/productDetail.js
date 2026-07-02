/* ── Allbirds Product Detail ────────────────────────────────── */

function initProductGallery() {
  const gallery = document.querySelector("[data-gallery]");
  if (!gallery) return;

  const mainImg = document.getElementById("pdpMainImage");
  const thumbs = gallery.querySelectorAll("[data-thumbs] .pdp-thumb");

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      const idx = thumb.dataset.index;
      const img = thumb.querySelector("img");
      if (!img || !mainImg) return;

      thumbs.forEach((t) => t.classList.remove("active"));
      thumb.classList.add("active");

      mainImg.style.opacity = "0";
      setTimeout(() => {
        mainImg.src = img.src;
        mainImg.style.opacity = "1";
      }, 150);
    });
  });
}

function initColorSwatches() {
  const swatches = document.querySelector("[data-swatches]");
  if (!swatches) return;

  const colorNameEl = document.querySelector(".pdp-color-name");

  swatches.querySelectorAll(".pdp-swatch").forEach((swatch) => {
    swatch.addEventListener("click", () => {
      swatches.querySelectorAll(".pdp-swatch").forEach((s) =>
        s.classList.remove("active")
      );
      swatch.classList.add("active");

      if (colorNameEl) {
        const raw = swatch.dataset.color || "";
        const clean = raw.split("(")[0].trim();
        colorNameEl.textContent = clean;
      }
    });
  });
}

function initSizeSelector() {
  const grid = document.querySelector("[data-sizes]");
  const atb = document.querySelector("[data-atb]");
  if (!grid || !atb) return;

  let selectedSize = null;

  grid.querySelectorAll(".pdp-size-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      grid.querySelectorAll(".pdp-size-btn").forEach((b) =>
        b.classList.remove("selected")
      );
      btn.classList.add("selected");
      selectedSize = btn.dataset.size;
      atb.disabled = false;
      atb.textContent = "ADD TO BAG";
    });
  });

  atb.addEventListener("click", () => {
    if (!selectedSize) return;
    const form = document.querySelector(".pdp-atb-form");
    if (form) {
      form.querySelector("[name='size']")?.remove();
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "size";
      input.value = selectedSize;
      form.appendChild(input);
      form.submit();
    } else {
      const productId =
        document.querySelector("[data-product-id]")?.dataset.productId;
      if (productId) {
        const f = document.createElement("form");
        f.method = "POST";
        f.action = "?route=cart";
        f.innerHTML = `
          <input type="hidden" name="action" value="add" />
          <input type="hidden" name="product_id" value="${productId}" />
          <input type="hidden" name="size" value="${selectedSize}" />
        `;
        document.body.appendChild(f);
        f.submit();
      }
    }
  });
}

function initAccordion() {
  document.querySelectorAll(".pdp-details").forEach((details) => {
    details.addEventListener("toggle", () => {
      if (window.gsap) {
        const content = details.querySelector(".pdp-details__content");
        if (details.open) {
          gsap.fromTo(
            content,
            { opacity: 0, y: -6 },
            { opacity: 1, y: 0, duration: 0.25, ease: "power2.out" }
          );
        }
      }
    });
  });
}

function initStarRating() {
  const container = document.querySelector("[data-stars]");
  if (!container) return;

  const score = parseFloat(container.dataset.stars) || 0;

  container.querySelectorAll(".star").forEach((star) => {
    const starScore = parseInt(star.dataset.score, 10);
    if (starScore <= Math.round(score)) {
      star.style.color = "#f5a623";
    } else {
      star.style.color = "#ddd";
    }
  });
}

function initScrollAnimations() {
  if (!window.gsap) return;

  const sections = document.querySelectorAll(
    ".pdp-features, .pdp-related, .pdp-reviews, .pdp-better"
  );

  sections.forEach((section) => {
    gsap.from(section, {
      scrollTrigger: {
        trigger: section,
        start: "top 85%",
        toggleActions: "play none none none",
      },
      opacity: 0,
      y: 30,
      duration: 0.6,
      ease: "power2.out",
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  initProductGallery();
  initColorSwatches();
  initSizeSelector();
  initAccordion();
  initStarRating();

  setTimeout(initScrollAnimations, 100);
});
