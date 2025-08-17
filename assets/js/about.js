// image

document.addEventListener("DOMContentLoaded", function () {
  const elements = document.querySelectorAll(".set-bg");

  elements.forEach(function (el) {
    const bg = el.getAttribute("data-setbg");
    if (bg) {
      el.style.backgroundImage = `url('${bg}')`;
      el.style.backgroundSize = "cover";
      el.style.backgroundPosition = "center";
      el.style.backgroundRepeat = "no-repeat";
    }
  });
});

// Counter
document.addEventListener("DOMContentLoaded", function () {
  const counters = document.querySelectorAll(".cn_num");
  let started = false; // prevent multiple triggers

  function animateCounter(el, target) {
    let count = 0;
    const speed = 50; // lower is faster

    const update = () => {
      count += Math.ceil(target / 40);
      if (count >= target) {
        el.textContent = target;
      } else {
        el.textContent = count;
        requestAnimationFrame(update);
      }
    };
    update();
  }

  function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
      rect.top <=
        (window.innerHeight || document.documentElement.clientHeight) &&
      rect.bottom >= 0
    );
  }

  function handleScroll() {
    const section = document.querySelector(".counter");
    if (!started && isInViewport(section)) {
      counters.forEach((counter) => {
        const target = parseInt(counter.textContent);
        animateCounter(counter, target);
      });
      started = true;
    }
  }

  window.addEventListener("scroll", handleScroll);
  handleScroll(); // trigger if already in view on load
});
