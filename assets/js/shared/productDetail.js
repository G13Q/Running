function initProductGallery() {
  const gallery = document.querySelector("[data-gallery]");
  if (!gallery) return;

  const mainImg = document.getElementById("pdpMainImage");
  const thumbsContainer = gallery.querySelector("[data-thumbs]");
  if (!thumbsContainer || !mainImg) return;

  thumbsContainer.addEventListener("click", (e) => {
    const thumb = e.target.closest(".pdp-thumb");
    if (!thumb) return;
    const idx = thumb.dataset.index;
    const img = thumb.querySelector("img");
    if (!img) return;

    thumbsContainer.querySelectorAll(".pdp-thumb").forEach((t) =>
      t.classList.remove("active")
    );
    thumb.classList.add("active");

    mainImg.style.opacity = "0";
    setTimeout(() => {
      mainImg.src = img.src;
      mainImg.style.opacity = "1";
    }, 150);
  });
}

function initColorSwatches() {
  const swatches = document.querySelector("[data-swatches]");
  if (!swatches) return;

  const colorNameEl = document.querySelector(".pdp-color-name");
  const mainImg = document.getElementById("pdpMainImage");
  const thumbsContainer = document.querySelector("[data-thumbs]");

  function swapGallery(images) {
    if (!images || images.length === 0) return;

    if (mainImg) {
      mainImg.style.opacity = "0";
      setTimeout(() => {
        mainImg.src = images[0];
        mainImg.style.opacity = "1";
      }, 150);
    }

    if (thumbsContainer) {
      thumbsContainer.innerHTML = images
        .map(
          (src, i) =>
            `<button class="pdp-thumb${i === 0 ? " active" : ""}" data-index="${i}">
              <img src="${src}" alt="" loading="lazy" />
            </button>`
        )
        .join("");
    }
  }

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

      let images;
      try {
        images = JSON.parse(swatch.dataset.images || "[]");
      } catch (e) {
        images = [];
      }
      swapGallery(images);
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
      if (btn.disabled) return;
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
    ".pdp-tech, .pdp-related-new, .pdp-sustainability"
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
