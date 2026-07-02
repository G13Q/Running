const closeBtn = $(".closeCart");
const container = $(".cartContainer");
const cart = $(".cart");
const cartOpen = $(".cartOpen");

function openCart() {
  gsap.to(cart, {
    translateX: "0%",
    duration: 1,
    ease: "expo.out",
  });
  gsap.to(container, {
    opacity: 1,
    duration: 1,
    ease: "expo.out",
  });
}

function closeCart() {
  gsap.to(cart, {
    translateX: "100%",
    duration: 0.7,
    ease: "expo.in",
  });
  gsap.to(container, {
    opacity: 0,
    duration: 0.7,
    ease: "expo.in",
  });
}

closeBtn.on("click", closeCart);
container.on("click", closeCart);
cartOpen.on("click", openCart);

const params = new URLSearchParams(window.location.search);
if (params.get("cart_open") === "1") {
  openCart();
  params.delete("cart_open");
  const qs = params.toString();
  const clean = qs ? "?" + qs : window.location.pathname;
  window.history.replaceState({}, "", clean);
}
