let curr = 3;
let tx = -300;
const container = $(".newAriv1content");
const slides = container.children();

function updateInfo() {
  const slide = slides.eq(curr);
  $(".pName").text(`${slide.data("name")} - $${Number(slide.data("price")).toLocaleString()}`);
}

$(".leftControl").on("click", () => {
  if (curr === 0) return;
  curr--;
  tx += 100;
  container.css("transform", `translateX(${tx}%)`);
  updateInfo();
});

$(".rightControl").on("click", () => {
  if (curr === slides.length - 1) return;
  curr++;
  tx -= 100;
  container.css("transform", `translateX(${tx}%)`);
  updateInfo();
});

const arrow = $(".arrow");

function hideArrow() {
  arrow.stop(true).hide();
  $("body").css("cursor", "default");
}

function showArrow(e, symbol) {
  arrow.html(symbol);
  $("body").css("cursor", "none");
  arrow.css("left", e.clientX).css("top", e.clientY);
  arrow.stop(true).fadeIn(0);
}

$(".leftControl").on("mouseenter", (e) => {
  showArrow(e, "⇠");
});
$(".leftControl").on("mousemove", (e) => {
  arrow.css("left", e.clientX).css("top", e.clientY);
});
$(".leftControl").on("mouseleave", hideArrow);

$(".rightControl").on("mouseenter", (e) => {
  showArrow(e, "⇢");
});
$(".rightControl").on("mousemove", (e) => {
  arrow.css("left", e.clientX).css("top", e.clientY);
});
$(".rightControl").on("mouseleave", hideArrow);

$(window).on("blur", hideArrow);
